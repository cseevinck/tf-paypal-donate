<?php
if ( ! defined( 'ABSPATH' ) ) exit; 

/** 
 *  Custom log file handler 
 *  This log file is to debug the tf-paypal-donations plugin. Entries are placed in the 
 *  uploads folder. This function will determine the type of data ($message) and will 
 *  attempt to present the data in a readable fashion. In cases where the data is allready 
 *  in a readable format, the $forcestring argument will cause the function to place the data
 *  into the file without attempting to format it at all.      
 * 
 *  Use like this:
 *    1. tfdon_log("description of log entry", "data to log");
 * 
 *    2. $array_value = array("foo","bar");
 *       tfdon_log("description of log entry", $array_value);
 * 
 *    3. tfdon_log("description of log entry", "formatted data to log", true);
 * 
 *  $description - A string describing the log entry
 *  $message - The item to logged
 *  $forceformat - optional argument: 
 *    If "string" - force string
 * 
 *  The file will be in the WordPress uploads directory
 * 
*/
function tfdon_log($description, $message, $forceformat = "") { 
  $options = get_option( 'tfdon_settings' ); // return if log turned off
  if (!isset($options['tfdon_log'])) {
    return;
  } 
 
  $upload_dir = wp_upload_dir();
  $upload_dir = $upload_dir['basedir'];
  $file  = $upload_dir . '/tf_paypal_donate.log';
  $file_old  = $upload_dir . '/tf_paypal_donate_old.log';

  if ($forceformat == "string"){
    $message = $message . "\n";
  } else
  if (gettype ( $message ) == "array" || gettype ( $message ) == "object") {
    $message = pretty_it($message);
  }
  else {
    $message = json_encode($message) . "\n";
  }

  file_put_contents($file, "\n" . date('Y-m-d h:i:s') . " :: " . $description . "\n   " . $message, FILE_APPEND);
  clearstatcache();
  $siz = filesize ($file);
  if ($siz > 80000){
    rename($file, $file_old);
    // put a header in file to advise on existence "old" log file
    file_put_contents($file, "When the log file reaches a limit (around 800kb), it is saved as a different file. Latest entries are in: " . $file . ", while the older entries are in: " . $file_old . ". Only two files are kept.\n", FILE_APPEND);
  } 
  return;
}

/** 
 *  Pretty up for array and object entries  
 * 
*/
function pretty_it($arr){
    $start = "'";
    foreach ($arr as $key => $value) {
        $data = $data."".$start."".$key."'=>'".$value."',\n";
        $start = "   '";
    }
    return $data;
}
?>