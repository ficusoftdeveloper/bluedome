<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CSV Reader class.
 */
class CSVReader {
  /**
   * Columns names retrieved after parsing.
   * @var array
   */
  var $fields;

  /**
   * Separator uset to explode each line.
   * @var string
   */
  var $separator = ';';

  /**
   * Enclosure used to decorate each fields.
   * @var string
   */
  var $enclosure = '"';

  /**
   * Maximum row size to be used for decoding.
   * @var integer
   */
  var $max_row_size = 4096;

  /**
   * Parse csv file.
   *
   * @param  string $p_Filepath
   *  Path of csv file.
   *
   * @return array
   *  parsed results.
   */
  function parse_file($p_Filepath) {
    $file = fopen($p_Filepath, 'r');
    $this->fields = fgetcsv($file, $this->max_row_size, $this->separator, $this->enclosure);
    $keys_values = explode(',',$this->fields[0]);
    $content = [];
    $keys = $this->escape_string($keys_values);
    $i = 1;
    while (($row = fgetcsv($file, $this->max_row_size, $this->separator, $this->enclosure)) != false ) {
      // skip empty lines
      if ($row != null) {
        $values = explode(',',$row[0]);
        if (count($keys) == count($values)) {
          $arr = [];
          $new_values = [];
          $new_values = $this->escape_string($values);
          for ($j=0;$j<count($keys);$j++) {
            if ($keys[$j] !=  "") {
              $arr[$keys[$j]] =   $new_values[$j];
            }
          }
          $content[$i] = $arr;
          $i++;
        }
      }
    }

    fclose($file);
    return $content;
  }

  /**
   * Escape string.
   *
   * @param  array $data
   *  Key value data.
   *
   * @return array
   *  output array.
   */
  function escape_string($data) {
    $result = [];
    foreach ($data as $row) {
      $result[] = str_replace('"', '',$row);
    }

    return $result;
  }
}
