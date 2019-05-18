<?php if (! defined('BASEPATH')) { exit('No direct script access allowed'); }

/**
 * @file
 * Helper functions for inspection controller.
 * @var  [type]
 */

/**
 * Load inspection component.
 * @var [type]
 */
if (!function_exists('inspection_component')) {
  /**
   * define variables for inspection component.
   *
   * @return array
   *   Array containing all inspection variables.
   */
  function inspection_component($filemanaged, $session) {
    $data = [];
    $data['processed_files'] = [];
    $data['report_files'] = [];
    $allFiles = [];

    $files = $filemanaged->getRows();
    $processedFiles = $filemanaged->getProcessedRows();
		$reportFiles = $filemanaged->getEnabledRows();

    if (!empty($files)) {
      foreach ($files as $file) {
        $temp = [];
        $props = $filemanaged->getPropsLatestRevision($file['fid']);
        if (!empty($props)) {
          $temp = $props;
        }
        else {
          // initialize properties.
          $temp['distance_cfo'] = 0;
          $temp['unit_cfo'] = 'inch';
          $temp['distance_poo'] = 0;
          $temp['unit_poo'] = 'inch';
          $temp['pixels'] = 0;
          $temp['is_image_visual'] = 0;
          $temp['is_image_dim'] = 0;
        }

        $file = array_merge($file, $temp);
        $allFiles[] = $file;
      }
    }

    // Set all files.
    $data['files'] = $allFiles;

    // Set processed files.
    if (!empty($processedFiles)) {
      $data['processed_files'] = $processedFiles;
    }

    // Set report files.
    if (!empty($reportFiles)) {
      $data['report_files'] = _merge_report_data($reportFiles, $filemanaged);
    }

    if (empty($data['files'])) {
      //$session->set_flashdata('error_msg', 'Please start uploading files for inspection.');
    }

    return $data;
  }
}

/**
 * Merge report data from csv files.
 * @var [type]
 */
if (!function_exists('_merge_report_data')) {
  /**
   * define variables for inspection component.
   *
   * @return array
   *   Array containing all inspection variables.
   */
  function _merge_report_data($reportFiles, $filemanaged) {
    $return = [];
    if (!empty($reportFiles)) {
      foreach ($reportFiles as $reportFile) {
        $temp = $reportFile;
        $props = $filemanaged->getPropsLatestRevision($reportFile['fid']);
        $temp['props'] = $props;

        $csv_sum_data = [];
        if (isset($reportFile['csv_sum_filename'])) {
          $csv_sum_file_path = realpath('uploads/processed/measure/' . $reportFile['csv_sum_filename']);

          if ($csv_sum_file_path) {
            $csv_sum_temp = [];
            $csv_sum_array = array_map('str_getcsv', file($csv_sum_file_path));
            foreach ($csv_sum_array as $key => $row) {
              // $row index: 0->id,1->Area,2->length,3->width,4->crack_id
              if ($key != 0) {
                $csv_sum_temp['crack_id'] = $row[0];
                $csv_sum_temp['area'] = _convert_pixels_to_distance($row[1], $props);
                $csv_sum_temp['length'] = _convert_pixels_to_distance($row[2], $props);
                $csv_sum_temp['width'] = _convert_pixels_to_distance($row[3], $props);

                $csv_sum_data[] = $csv_sum_temp;
              }
            }

            $temp['csv_sum_data'] = $csv_sum_data;
          }

          $return[] = $temp;
        }
      }
    }

    return $return;
  }
}


/**
 * Convert value into distance unit.
 *
 * @var int
 */
if (!function_exists('_convert_pixels_to_distance')) {
  /**
   * Convert pixels into distance unit.
   *
   * @param  int $value
   *   Value from CSV in pixels.
   * @param  array $props
   *   Media file properties.
   *
   * @return int
   *   Value in distance unit after conversion.
   */
  function _convert_pixels_to_distance($pixel_value, $props) {
    if ($props['pixels'] && $props['distance_poo']) {
      return  round(($pixel_value * $props['distance_poo'])/($props['pixels']), '2');
    }

    return $pixel_value;
  }
}
