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

          // Upload to google drive.
          // Get the API client and construct the service object.
          $client = $this->googledriveservices->getClient();
          $service = new Google_Service_Drive($client);
          // Id of folder where files are uploaded.
          //$folderId = '1kZ_jQMDREVz5GD8RcJEfhtGBrQHBhaCx';
          $folderId = '1Erf9JuzVDzt2-9Xp48-JBD1NiSAJRVR0';
          $fileMetadata = new Google_Service_Drive_DriveFile([
            'name' => $insert . '_' . time() . '_' . $postData['filename'],
            'parents' => [$folderId]
          ]);

          $content = file_get_contents($this->rawSourceUploadPath . $postData['filename']);
          /*$file = $service->files->create($fileMetadata, [
            'data' => $content,
            'mimeType' => $postData['filetype'],
            'uploadType' => 'multipart',
            'fields' => 'id'
          ]); */
          $file->id = TRUE;
          if ($file->id) {
            $this->session->set_flashdata('success_msg', 'File successfully uploaded.');
          } else {
            $this->session->set_flashdata('error_msg', 'Unexpected error occurred, please try again.');
          }
        } else {
          $this->session->set_flashdata('error_msg', 'Unexpected error occurred, please try again.');
        }
      }
    } else {
      $error = array('error' => $this->upload->display_errors());
      $this->session->set_flashdata('error_msg', $error['error']);
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
        $this->save($inputs);
        $this->process($inputs);
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
            'distance_cfo' => $inputs['distance_cfo'][$fid],
            'unit_cfo' => $inputs['unit_cfo'][$fid],
            'is_image_visual' => ($inputs['distance_cfo'][$fid]) ? 1 : 0,
            'date_modified' => date('m/d/Y')
          ];
          if ($error_code = validate_media($file_props)) {
            switch ($error_code) {
              case 2:
                $this->session->set_flashdata('error_msg', 'Invalid distance of camera from object.');
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
            $this->processObject($file);
            $update = [
              'filename' => $inputs['filename'][$fid] . $fileextension,
              'is_processed' => 1, // File is processed.
              'processed_filename' => '',
              'measout_filename' => '',
              'csv_filename' => '',
              'csv_sum_filename' => '',
              'date_processed' => date('m/d/Y'),
              'status' => 1
            ];

            $update = $this->filemanaged->update($update, $fid);
            return;
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
      $this->session->set_flashdata('success_msg', 'Files successfully processed.');
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
    // Process algorithm to get input.csv
    // read input.csv file, and rearrage $data as per class id.
    // read track_point.csv and collect lat long value.
    // plotonmap($data);
    $input_csv_path = 'uploads/object/raw/input.csv';
    $inputs = $this->csvreader->parse_file($input_csv_path);
    $results = [];

    if (!empty($inputs)) {
      foreach ($inputs as $input) {
        $results[$input['classID']] = $input;
      }
    }
    // $results
    $results = [
      '10' => [
        'classID' => '10',
        'classLabel' => 'E5-1a EXIT WITH NUMBER Marker',
        'timestamps' => [
          '35' => [13242, 25336],
          '66' => [2.64646353, -4.6685757]
        ]
      ]
    ];
  }

  /**
   *  Plot results on map.
   */
  public function map($fid) {
    // Start XML file, create parent node
    $dom = new DOMDocument("1.0");
    $node = $dom->createElement("markers");
    $parnode = $dom->appendChild($node);
    $results = $this->filemanaged->getMapMarkers();
    $filepath = 'uploads/object/raw/output.xml';

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

    // Manage views components.
		$this->load->view('components/inspection_header', header_component());
		$this->load->view('components/map');
		$this->load->view('components/inspection_footer');

  }
}
