<?php if (! defined('BASEPATH')) { exit('No direct script access allowed');
}

/**
 * @file
 * Helper functions for Media Controller.
 * @var  [type]
 */

/**
 * Get extension of media file.
 * @var [type]
 */
if (!function_exists('parse_file_type')) {
    /**
     * [parse_file_type description]
     *
     * @param  [type] $filetype [description]
     * @return [type]           [description]
     */
    function parse_file_type($filetype) {
      switch ($filetype) {
        case 'image/jpeg':
          return '.jpg';

        case 'image/png' :
          return '.png';

        case 'image/gif' :
          return '.gif';

        case 'video/mp4':
          return '.mp4';

        default:
          return FALSE;
      }
    }
}

/**
 * Move media file once saved.
 * @var [type]
 */
if (!function_exists('move_selected_file')) {
    /**
     * Move selected file.
     *
     * @param  array $file
     *   Array containing media file data.
     * @param  string $filename
     *   New file name.
     * @param  string $file_extension
     *   Extension of media file.
     * @param  string $uploadPath
     *   Path of upload directory.
     *
     * @return bool
     *   Return TRUE if file moved, else FALSE.
     */
    function move_selected_file($file, $filename, $file_extension, $uploadPath) {
      $filename = str_replace(' ', '_', $filename);
      $src_path = $uploadPath . $file['filename'];
      $return = rename($src_path, $uploadPath . $filename . $file_extension);
      return $return;
    }
}

/**
 * Unit conversion to cm.
 * @var [type]
 */
if (!function_exists('unit_conversion')) {
    /**
     * convert data into centimetre.
     *
     * @param  array $props
     *   Array containing file properties.
     *
     * @return float
     *   Distance value in centimetre.
     */
    function unit_conversion($props) {
      switch($props['unit_cfo']) {
        case 'inch':
          # code...
          return round($props['distance_cfo'] * 2.54);
        case 'ft':
          #code..
          return round($props['distance_cfo'] * 30.48);
        default:
          return round($props['distance_cfo'] * 2.54);
      }
    }
}


/**
 * Validate media.
 * @var array
 */
if (!function_exists('validate_media')) {
    /**
     * convert data into centimetre.
     *
     * @param  array $props
     *   Array containing file properties.
     *
     * @return float
     *   Distance value in centimetre.
     */
    function validate_media(array $props) {
      if (isset($props['distance_cfo']) && ($props['distance_cfo'] <= 0)) {
        return 2;
      }

      return 1;
    }
}
