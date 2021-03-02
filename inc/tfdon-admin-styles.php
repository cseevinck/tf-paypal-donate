<?php
if ( ! defined( 'ABSPATH' ) ) exit; 
add_action('admin_head', 'tfdon_custom_styles');
add_action('admin_head', 'tfdon_custom_scripts');

function tfdon_custom_styles() {
  echo '<style>
    #tfdon-admin {
      background-color:#b5b5b5;
      margin-bottom: -50px! important; 
    }
    #tfdon-admin h2 {
      margin: 20px! important;
      padding-top: 20px! important;
    }
    #tfdon-admin p {
      margin: 20px! important;
      line-height: 1.3;
      font-size: 14px;
      font-weight: 600;
    }
    #tfdon-admin input.wide {
      width:500px;
    }
    .tfdon_form_section {
      background-color:#b5b5b5; 
      margin-bottom:30px;
      padding:30px;
    }
    input[type=text] {
      background-image: none! important;
    }
    .form-table {
      margin: 20px;
      width: 80%;
    }
    .tfdon_bottom {
      padding-bottom: 30px;
    {
  </style>';
}

function tfdon_custom_scripts() {
  echo '
  <script>
    jQuery(document).ready(function($) {
      $(".tfdon_form_section .enable").each(function() {
        $row = $(this).closest("tr").siblings();
        
        if ($(this).is(":checked")) {
          console.log("show");
          $row.show();
        }
        else {
          console.log("hide");
          $row.hide();
        }
      });
    
      $(".tfdon_form_section .enable").click(function() {
        $row = $(this).closest("tr").siblings();
        $row.toggle();
      });
    });
  </script>';
}
?>