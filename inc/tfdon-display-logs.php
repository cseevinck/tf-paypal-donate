<?php
if ( ! defined( 'ABSPATH' ) ) exit; 

/**
* 
* Here to display the log files 
*
*/

function tfdon_display_log_file ($what_file=null){
  $upload_dir = wp_upload_dir();
  $upload_dir = $upload_dir['basedir'];
  if ($what_file) {
    $file  = $upload_dir . '/' . $what_file;
    } 
  else { return; };

  // message if file does not exist
  if (!file_exists ( $file )) {
    ?>
    <div id="tfdon-page"> 
      <h1 class="tfdon-page-hdr">Log file does not exist</h1>
      <p class="tfdon-page-hdr">file name=<?php echo $file;?></p>
    </div>
    <?php 
    return;
  }
  ?>
  <div id="tfdon-page"> 
    <?php
    $log_data = file_get_contents ($file);
    ?>
    <div class="tfdon-box-style">
    <?php
    echo tfdonConvertPlainTextToHTML($log_data);
    ?>
    </div>
  </div>
  <?php
}
/**
 *  Convert plain text to HTML
 *  From https://www.willmaster.com/library/generators/convert-plain-text-to-html.php
 * 
 *  1. Insert <p> tags into string
 *  2. Increase indent for indents > 4
 * 
 *  Input: $s string
 * 
 */
function tfdonConvertPlainTextToHTML($s) {
   $LineFeed = strpos($s,"\r\n")!==false ? "\r\n" : ( strpos($s,"\n")!==false ? "\n" : "\r" );
   $s = trim($s); 
   $s = strpos($s,"\n")===false ? str_replace("\r","\n",$s) : str_replace("\r",'',$s);
   $s = preg_replace('/\n\n+/','</p><p class="tfdon-p-margin">~~N-n~--',$s);
   $s = str_replace('~~N-n~--',"\n",$s);
   $s = "<p class='tfdon-p-margin'>\n$s\n</p>";

   // make all linefeeds into 7F 
   $s = str_replace("\n",chr(0x7f),$s); 

   // Insert line number in front of the line
   $linenum = 1;
   $ss = $s;
   $cnt = 1;
   $backn = chr(0x7f);
   while ($cnt == 1) {
      $newnum = chr(0x0a) . "<span class='tfdon-log-num'>" . sprintf('%05d', $linenum++) . '</span>' . "  "; 
      $s = str_replace_first($backn, $newnum, $s, $cnt);
    }
   return $s;
}

/**
 *  Replace the first instance of $from with $to in $content
 *       Usage:
 *        echo str_replace_first('abc', '123', 'abcdef abcdef abcdef', &$count); 
 *        outputs '123def abcdef abcdef'
 *        $count will be zero if no replacement occured
 */
function str_replace_first($from, $to, $content, &$count)
{
    $from = '/'.preg_quote($from, '/').'/';
    $s2 =  preg_replace($from, $to, $content, 1, $count);
    return $s2;
}

/**
 *  Replace a New line + a number of spaces with $chr and 2 x that number 
 *  of spaces in string $str
 *    $num is the number of spaces
 *    $chr is the character
 *    $str is the string
 *    
 */
function tfdon_rep_num_chrs ($num, $chr ,$str) {
   // build string with lf + $num times space  
   $rplc = $chr;
   $spc = "\n";
   for ($x=$num; $x >= 0; $x-- ) {
      $rplc = $rplc . "  "; 
      $spc = $spc . " ";        
   }
   return str_replace($spc, $rplc, $str);
}
?>