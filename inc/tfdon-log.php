<?php
if ( ! defined( 'ABSPATH' ) ) exit; 

/** 
 *  Custom log file handler 
 *  This log file is to debug the tf-paypal-donations plugin. Entries are placed 
 *  in the uploads folder. This function will determine the type of data 
 *  ($message) and will try to present the data in a readable fashion. In cases 
 *  where the data is allready in a readable format, the $forcestring argument 
 *  will cause the function to place the data into the file without attempting 
 *  to format it at all.      
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

const TFDON_CURRENT_LOG = "tf_paypal_donate.log";
const TFDON_OLDER_LOG = "tf_paypal_donate_old.log";

function tfdon_log($description, $message, $forceformat = "") { 
  $options = get_option( 'tfdon_settings' ); // return if log turned off
  // if (empty($options['tfdon_log_display'])) {
  //   echo ('TF Paypal Donations Setup Page: URL of page for log file displays is empty!'); 
  //   return; 
  // } 
  if (!isset($options['tfdon_log'])) {
    return;
  } 

  $upload_dir = wp_upload_dir();
  $upload_dir = $upload_dir['basedir'];
  $file  = $upload_dir . '/' . TFDON_CURRENT_LOG;
  $file_old  = $upload_dir . '/' . TFDON_OLDER_LOG;

  if ($forceformat == "string"){
    $message = $message;
    $description = "(string): " . $description;
  } else
  if (gettype ( $message ) == "array" || gettype ( $message ) == "object") {
    $description = "(array|object): " . $description;
    $message = tfdon_pretty_it($message);
  }
  else {
    $description = "(unknown): " . $description;
    $message = ($message);
  }
 
  file_put_contents($file, "\n<span class='tfdon-log-date-desc'>" . date('Y-m-d h:i:s') . " :: " . $description . "</span>\n   " . $message, FILE_APPEND);
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
function tfdon_pretty_it($arr){
    $start = "'";
    foreach ($arr as $key => $value) {
        $data = $data."".$start."".$key."'=>'".$value."',\n";
        $start = "   '";
    }
    return $data;
}
?>