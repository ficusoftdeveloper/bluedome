<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require __DIR__ . '/../../../vendor/autoload.php';

/**
 * Controls all media related actions.
 */
class Media extends CI_Controller {

  /**
   * Directory path of source raw images.
   * @var string
   */
  private $rawSourceUploadPath = "uploads/raw/";

  /**
   * Directory path of destination processed images
   * after crack detection.
   * @var string
   */
  private $processedDestinationUploadPath = "uploads/processed/detect/";

  /**
   * Directory path of destination processed images
   * after crack measurement.
   * @var string
   */
  private $processedMeasureUploadPath= "uploads/processed/measure/";

  /**
   * construct an object of Media controller.
   */
  function __construct() {
    parent::__construct();
    $this->load->helper('form');
    $this->load->library('upload');
    $this->load->library('session');
    $this->load->library('csvreader');
    $this->load->library('googledriveservices');
    $this->load->library('unzip');
    $this->load->helper(array('form', 'url'));
    $this->load->helper('media/media');
    $this->load->helper('welcome');
    $this->load->library('form_validation');
    $this->load->model('media/filemanaged');
  }

  /**
   * Set configuration of image upload.
   *
   * @param array $input
   *  An array containing user inputs.
   */
  protected function setConfig($input) {
    $config = [];
    if (!empty($input)) {
      $config['upload_path'] = $this->rawSourceUploadPath;
      $config['allowed_types'] = 'gif|jpg|png|jpeg|mp4';
      $config['overwrite'] = FALSE;
      if (isset($input['videoSubmit'])) {
        $config['allowed_types'] = 'mp4';
      }
    }

    return $config;
  }

  /**
   * An action callback to upload media files.
   */
  function do_file_upload() {
    $config = $this->setConfig($this->input->post());
    $inputs = $this->input->post();
    $this->upload->initialize($config);
    $this->load->library('upload', $config);
    if ($this->upload->do_upload()) {
      $fdata = ['upload_data' => $this->upload->data()];
      if (!empty($fdata)) {
        $postData = [
          'operation' => $inputs['operation'],
          'filename' => $fdata['upload_data']['file_name'],
          'rawname' => $fdata['upload_data']['file_name'],
          'filetype' => $fdata['upload_data']['file_type'],
          'filesize' => $fdata['upload_data']['file_size'],
          'is_image' => $fdata['upload_data']['is_image'],
          'file_width' => $fdata['upload_data']['image_width'],
          'file_height' => $fdata['upload_data']['image_height'],
          'is_processed' => 0, //false
          'is_uploaded' => 1, // in queue.
          'processed_filename' => '',
          'date_captured' => time(),
          'status' => 0
        ];

        $insert = $this->filemanaged->add($postData);
        if ($insert) {
          // Initialize file properties by default value.
          $file_props = [
            'fid' => $insert,
            'distance_cfo' => 0,
            'unit_cfo' => 'inch',
            'distance_poo' => 0,
            'unit_poo' => 'inch',
            'is_image_visual' => 0,
            'is_image_dim' => 0,
            'date_created' => date('m/d/Y'),
          ];

          // Insert file properties.
          $insertProps = $this->filemanaged->insertFileProps($file_props);

          // Selected file uploaded to google drive folder.
          $client = $this->googledriveservices->getClient();
          $service = new Google_Service_Drive($client);

          // File id of parent folder.
          $folder_id = '1CUjrrgfH0Ryfg3ZoTgWgccSNilibRlxI';
          $name = $insert . '_' . time() . '_' . $postData['filename'];
          $file_meta_data = new Google_Service_Drive_DriveFile([
            'name' => $insert . '_' . time() . '_' . $postData['filename'],
            'parents' => [$folder_id]
          ]);

          $content = file_get_contents($this->rawSourceUploadPath . $postData['filename']);
          $file = $service->files->create($file_meta_data, [
            'data' => $content,
            'mimeType' => $postData['filetype'],
            'uploadType' => 'multipart',
            'fields' => 'id'
          ]);

          // File ID of uploaded file in google drive.
          $gfile_id = $file->getId();

          if ($gfile_id) {
            $update = [
              'gdrive_filename' => preg_replace('/\\.[^.\\s]{3,4}$/', '', $name),
            ];
            $this->filemanaged->update($update, $insert);
            $this->session->set_flashdata('success_msg', 'File successfully uploaded.');
          } else {
            $this->session->set_flashdata('error_msg', 'Unexpected error occurred, please try again.');
          }
        }
      } else {
        $error = array('error' => $this->upload->display_errors());
        $this->session->set_flashdata('error_msg', $error['error']);
      }
    }

    redirect('/inspection');
  }

  /**
   * Main action callback
   */
  public function action() {
    $inputs = $this->input->post();
    if (!empty($inputs['file_check'])) {
      if (isset($inputs['deleteFiles'])) {
        $this->delete($inputs);
      }
      if (isset($inputs['saveFiles'])) {
        $this->save($inputs);
      }
      if (isset($inputs['processFiles'])) {
        $code = $this->save($inputs);
        if ($code != 2) {
          $this->process($inputs);
        }
      }
      if (isset($inputs['reprocessFiles'])) {
        $this->process($inputs, TRUE);
      }
    } else {
      $this->session->set_flashdata('error_msg', 'Please select atleast 1 file.');
    }

    redirect('/inspection');
  }

  /**
   * Delete media files and database entries.
   *
   * @param  array $inputs
   *   Array containing user inputs.
   */
  public function delete($inputs) {
    $fids = $inputs['file_check'];
    if (!empty($fids)) {
      foreach ($fids as $fid) {
        $delete = $this->filemanaged->deleteFile($fid);
        if (!$delete) {
          $this->session->set_flashdata('error_msg', 'Unexpected error occurred, please try again!');
        }
      }
      $this->session->set_flashdata('success_msg', 'Selected files are removed successfully.');
    } else {
      $this->session->set_flashdata('error_msg', 'Please select files to perform delete operation.');
    }
  }

  /**
   * Save files state to database.
   *
   * @param  array $inputs
   * // Array containing user inputs.
   */
  public function save($inputs) {
    $fids = $inputs['file_check'];
    if (!empty($fids)) {
      foreach ($fids as $fid) {
        $file = $this->filemanaged->getRows($fid);
        if (!empty($file)) {
          // Get file extension.
          $fileextension = parse_file_type($file['filetype']);
          $update = [];
          $file_props = [];
          $update = [
            'filename' => str_replace(' ', '_', $inputs['filename'][$fid]) . $fileextension,
          ];

          $file_props = [
            'distance_cfo' => isset($inputs['distance_cfo'][$fid]) ? $inputs['distance_cfo'][$fid] : 0,
            'unit_cfo' => isset($inputs['unit_cfo'][$fid]) ? $inputs['unit_cfo'][$fid] : 0,
            'is_image_visual' => (isset($inputs['distance_cfo'][$fid]) && $inputs['distance_cfo'][$fid]) ? 1 : 0,
            'date_modified' => date('m/d/Y')
          ];
          if ($error_code = validate_media($file_props)) {
            switch ($error_code) {
              case 2:
                if ($file['operation'] != 'detect_and_locate_objects') {
                  $this->session->set_flashdata('error_msg', 'Invalid distance of camera from object.');
                  return $error_code;
                }
                break;

              default:
                $updateResult = $this->filemanaged->update($update, $fid);
                if ($updateResult) {
                  //move selected image.
                  move_selected_file($file, $inputs['filename'][$fid], $fileextension, $this->rawSourceUploadPath);
                  $insert = $this->filemanaged->updateFileProps($fid, $file_props);
                  $this->session->set_flashdata('success_msg', 'Changes saved successfully.');
                } else {
                  $this->session->set_flashdata('error_msg', 'Unexpected error occurred, please try again.');
                }
                break;
            }
          }
        } else {
          $this->session->set_flashdata('error_msg', 'File does not exist.');
        }
      }
    } else {
      $this->session->set_flashdata('error_msg', 'Unexpected error occurred, please try again.');
    }

    return;
  }

  /**
   * Process media file.
   *
   * @param array $inputs
   *   Array containing user data.
   */
  public function process($inputs, $reprocess = FALSE) {
    $fids = $inputs['file_check'];
    $distance = 0;
    if (!empty($fids)) {
      foreach ($fids as $fid) {
        $update = [];
        $freeze = time();
        $file = $this->filemanaged->getRows($fid);
        $distance_cfo = 1;
        $distance_poo = 1;
        $props = $this->filemanaged->getPropsLatestRevision($file['fid']);
        if (!empty($props['distance_cfo']) && ($props['distance_cfo']) != 0) {
          $distance_cfo = unit_conversion($props);
        }
        if (!empty($props['distance_poo']) && ($props['distance_poo']) != 0) {
          $distance_poo = $props['distance_poo'];
        }
        if (!empty($file)) {
          // check operation.
          if ($file['operation'] == 'detect_and_locate_objects') {
            $fileextension = parse_file_type($file['filetype']);
            $update = [
              'filename' => $inputs['filename'][$fid] . $fileextension,
              'is_processed' => 2, // 2 -> Processing.
              'processed_filename' => '',
              'measout_filename' => '',
              'csv_filename' => '',
              'csv_sum_filename' => '',
              'date_processed' => date('m/d/Y'),
              'status' => 1
            ];

            $update = $this->filemanaged->update($update, $fid);
            $this->processObject($file);
            continue;
          }

          $sourceFile = realpath($this->rawSourceUploadPath . $file['filename']);
          $fileextension = ($file['is_image']) ? parse_file_type($file['filetype']) : '.png';
          if (isset($inputs['filename'][$fid])) {
            $processFile = realpath($this->processedDestinationUploadPath)  . '/' . $inputs['filename'][$fid] . '_' . $freeze . $fileextension;
            $processFileBin = realpath($this->processedDestinationUploadPath)  . '/' . $inputs['filename'][$fid] . '_bin_' . $freeze . $fileextension;
            // File path of crack measure.
            $measureFileBin = realpath($this->processedMeasureUploadPath)  . '/' . $inputs['filename'][$fid] . '_mesout_' . $freeze . $fileextension;
            $measureFileCsv = realpath($this->processedMeasureUploadPath)  . '/' . $inputs['filename'][$fid] . '_' . $freeze . '.csv';
            $measureFileSum = realpath($this->processedMeasureUploadPath)  . '/' . $inputs['filename'][$fid] . '_sum_' . $freeze . '.csv';
            if (!$file['is_image']) {
              $frameFile = realpath($this->processedDestinationUploadPath)  . '/' . $inputs['filename'][$fid] . '_frame_' . $freeze . $fileextension;
              $stichingFile = realpath($this->processedDestinationUploadPath)  . '/' . $inputs['filename'][$fid] . '_stiching_' . $freeze . $fileextension;
            }
          } else {
            $inputs['filename'][$fid] = preg_replace('/\\.[^.\\s]{3,4}$/', '', $file['filename']);
            $processFile = realpath($this->processedDestinationUploadPath)  . '/' . preg_replace('/\\.[^.\\s]{3,4}$/', '', $file['filename'])  . '_' . $freeze . $fileextension;
            $processFileBin = realpath($this->processedDestinationUploadPath)  . '/' . preg_replace('/\\.[^.\\s]{3,4}$/', '', $file['filename']) . '_bin_' . $freeze . $fileextension;
            // File path of crack measure.
            $measureFileBin = realpath($this->processedMeasureUploadPath)  . '/' . preg_replace('/\\.[^.\\s]{3,4}$/', '', $file['filename']) . '_mesout_' . $freeze . $fileextension;
            $measureFileCsv = realpath($this->processedMeasureUploadPath)  . '/' . preg_replace('/\\.[^.\\s]{3,4}$/', '', $file['filename']) . '_' . $freeze . '.csv';
            $measureFileSum = realpath($this->processedMeasureUploadPath)  . '/' . preg_replace('/\\.[^.\\s]{3,4}$/', '', $file['filename']) . '_sum_' . $freeze . '.csv';
            if (!$file['is_image']) {
              $frameFile = realpath($this->processedDestinationUploadPath)  . '/' . preg_replace('/\\.[^.\\s]{3,4}$/', '', $file['filename'])  . '_frame_' . $freeze . $fileextension;
              $stichingFile = realpath($this->processedDestinationUploadPath)  . '/' . preg_replace('/\\.[^.\\s]{3,4}$/', '', $file['filename'])  . '_stiching_' . $freeze . $fileextension;
            }
          }

          // Call python script to execute processing of
          // image for crack detection.
          if ($file['is_image']) {
            $cmd_detect = "/usr/local/bin/python3 scripts/image/crack_detection_fast.py ".  $sourceFile . " " . $processFile . " " . $processFileBin . " 2>&1";
            //$cmd_detect = "/usr/local/bin/python3 scripts/image/crack_detection2_fast.py ".  $sourceFile . " " . $distance_cfo . " " . $processFile . " " . $processFileBin . " 2>&1";
            $output_detect = shell_exec($cmd_detect);

            // Call python script to execute processing of
            // image for crack measurement.
            $cmd_measure = "/usr/local/bin/python3 scripts/image/crack_measure/crack_measure.py ".  $processFileBin . " " . $distance_cfo . " " . $measureFileBin . " " . $measureFileCsv . " " . $measureFileSum . " 2>&1";
            $output_measure = shell_exec($cmd_measure);
          } else {
            $cmd_detect = "/usr/local/bin/python3 scripts/video/crack_detection_video.py -v ".  $sourceFile . " -o " . $processFile . " -f " . $frameFile . " -b " . $processFileBin . " -s " . $stichingFile . " 2>&1";
            $output_detect = shell_exec($cmd_detect);

            // Call python script to execute processing of
            // image for crack measurement.
            $cmd_measure = "/usr/local/bin/python3 scripts/video/crack_measure/crack_measure.py ".  $processFileBin . " " . $distance_cfo . " " . $measureFileBin . " " . $measureFileCsv . " " . $measureFileSum . " 2>&1";
            $output_measure = shell_exec($cmd_measure);
          }


          $update = [
            'filename' => $inputs['filename'][$fid] . $fileextension,
            'is_processed' => 1, // File is processed.
            'processed_filename' => $inputs['filename'][$fid] . '_' . $freeze . $fileextension,
            'measout_filename' => $inputs['filename'][$fid] . '_mesout_' . $freeze . $fileextension,
            'csv_filename' => $inputs['filename'][$fid] . '_' . $freeze . '.csv',
            'csv_sum_filename' => $inputs['filename'][$fid] . '_sum_' . $freeze . '.csv',
            'date_processed' => date('m/d/Y'),
            'status' => 1
          ];

          $update = $this->filemanaged->update($update, $fid);
        }
      }
      $this->session->set_flashdata('success_msg', 'Files sent for processing.');
    } else {
      $this->session->set_flashdata('error_msg', 'Please select atleast 1 file.');
    }
  }

  public function download($fid) {

    $files = $this->filemanaged->getRows($fid);

    // Get real path for our folder
    $rootPath = realpath('uploads/processed/measure');
    $token = time();

    // Initialize archive object
    $zip = new ZipArchive();
    $zip->open('uploads/processed/measure/' . $files['filename'] . '_' . $token . '.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

    // Add current file to archive
   $zip->addFile(realpath('uploads/raw/' . $files['filename']), 'raw/' . $files['filename']);
   if ($files['calibrated_filename']) {
     $zip->addFile(realpath('uploads/raw/' . $files['calibrated_filename']), 'raw/' . $files['calibrated_filename']);
   }
   $zip->addFile(realpath('uploads/processed/detect/' . $files['processed_filename']), 'processed/detect/' . $files['processed_filename']);
   $zip->addFile(realpath('uploads/processed/measure/' . $files['measout_filename']), 'processed/measure/' . $files['measout_filename']);
   // Add binary image and csv files.
   $zip->addFile(realpath('uploads/processed/measure/' . $files['measout_filename']), 'processed/measure/' . $files['measout_filename']);
   $zip->addFile(realpath('uploads/processed/measure/' . $files['csv_filename']), 'processed/measure/' . $files['csv_filename']);
   $zip->addFile(realpath('uploads/processed/measure/' . $files['csv_sum_filename']), 'processed/measure/' . $files['csv_sum_filename']);

    // Zip archive will be created only after closing object
    $zip->close();

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.basename('uploads/processed/measure/' . $files['filename'] . '_' . $token . '.zip'));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize('uploads/processed/measure/' . $files['filename'] . '_' . $token . '.zip'));
    readfile('uploads/processed/measure/' . $files['filename'] . '_' . $token . '.zip');

    redirect('/inspection');
  }

  /**
   * Process object identification.
   *
   * @param  array  $file
   *  File array
   * @return [type]
   *  Return
   */
  public function processObject(array $file) {
    // Download processed files from google drive.
    $client = $this->googledriveservices->getClient();
    $service = new Google_Service_Drive($client);
    $pageToken = null;
    $name = $file['gdrive_filename'];
    do {
      $response = $service->files->listFiles([
        'q' => 'name="' . $name . '.zip"',
        'spaces' => 'drive',
        'pageToken' => $pageToken,
        'fields' => 'nextPageToken, files(id, name)',
      ]);
      foreach ($response->files as $drive_file) {
        if ($drive_file->getId()) {
          $download = $service->files->get($drive_file->getId(), ['alt' => 'media']);
          $content = $download->getBody()->getContents();
          // Code to save content in file to output.
          $output_path = "uploads/processed/object/" . $drive_file->getName();
          //print_r($output_path); exit;
          file_put_contents($output_path, $content);

          // extract zip file.
          if (file_exists($output_path)) {
            $this->unzip->extract($output_path);
          }
        }
      }

      $pageToken = $response->pageToken;
    } while ($pageToken != NULL);

    // Step 02 - Parse output csv file and update db table.
    $csv_path = "uploads/processed/object/" . $file['gdrive_filename'] . "/" . $file['gdrive_filename'] . ".csv";
    $results = $this->csvreader->parse_file($csv_path);
    if (!empty($results)) {
      foreach ($results as $result) {
        $check = $this->filemanaged->checkMapMarker($file['fid'], $result['classID'], $result['classLABEL'], round($result['GPS_LON'], 7), round($result['GPS_LAT'], 7));
        if (!$check) {
          // set address.
          $address = base_url('uploads/object/raw/maps/1.png');
          if ($result['classID'] == 1) {
            $address = base_url('uploads/object/raw/maps/1.png');
          } else if ($result['classID'] == 2) {
            $address = base_url('uploads/object/raw/maps/2.jpeg');
          }
          $mapMarker = [
            'fid' => $file['fid'],
            'class_id' => $result['classID'],
            'class_label' => $result['classLABEL'],
            'address' => $address,
            'lng' => round($result['GPS_LAT'],7),
            'lat' => round($result['GPS_LON'],7),
            'type' => 'sign_board',
          ];

          $insert = $this->filemanaged->setMapMarker($mapMarker);
        }
      }
    }

    // Update file manage table.
    $update = [
      'is_processed' => 1,
    ];

    $updated = $this->filemanaged->update($update, $file['fid']);
  }

  /**
   *  Plot results on map.
   */
  public function map($fid) {
    // Start XML file, create parent node
    $dom = new DOMDocument("1.0");
    $node = $dom->createElement("markers");
    $parnode = $dom->appendChild($node);
    $results = $this->filemanaged->getMapMarkers($fid);
    $filepath = 'uploads/processed/object/maps/' . $fid . '.xml';

    if (!empty($results)) {
      foreach ($results as $result) {
        $node = $dom->createElement("marker");
        $newnode = $parnode->appendChild($node);
        $newnode->setAttribute("id", $result['id']);
        $newnode->setAttribute("class_id", $result['class_id']);
        $newnode->setAttribute("class_label", $result['class_label']);
        $newnode->setAttribute("address", $result['address']);
        $newnode->setAttribute("lat", $result['lat']);
        $newnode->setAttribute("lng", $result['lng']);
        $newnode->setAttribute("type", $result['type']);
      }

      $dom->save($filepath);
    }

    $data['filepath'] = base_url($filepath);

    // Manage views components.
		$this->load->view('components/inspection_header', header_component());
		$this->load->view('components/map', $data);
		$this->load->view('components/inspection_footer');

  }
}
