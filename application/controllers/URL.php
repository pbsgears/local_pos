<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/** ================================
 * -- File Name : URL.php
 * -- Project Name : SME
 * -- Module Name : All Modules impact
 * -- Author : Mohamed Shafri
 * -- Create date : 08 - May 2018
 * -- Description : Common Controller to handle encrypted URLs, We have used Encryption_url Library which used MCrypt which is more secure than base64 URL encode
 *
 * --REVISION HISTORY
 * Date: 08-05-2018 By: Mohamed Shafri: init.
 *
 *
 *
 */
class Url extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        if (!isset($this->common_data['status']) || empty(trim($this->common_data['status']))) {
            header('Location: ' . site_url('Login/logout'));
            exit;
        } else {

        }
    }

    function test()
    {
        echo $this->generate_encrypt_link('http://localhost/gs_sme/index.php/url/test');
    }


    function open_link($cryptURL)
    {
        $decryptURL = $this->encryption_url->decode($cryptURL);
        echo $this->generateJS($decryptURL);
    }

    function generateJS($actualPath)
    {
        $path_parts = pathinfo($actualPath);
        return '<html><body><a style="display:none;" href="' . $actualPath . '" download="' . $path_parts['basename'] . '" id="path_link">Download</a> <script> document.getElementById("path_link").click(); setTimeout(function(){close(); }, 900);  </script></body></html>';
    }
}