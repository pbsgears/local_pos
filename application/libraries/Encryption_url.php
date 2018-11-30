<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Encryption_url
{
    var $skey = "crypt_gears_1234"; // you can change it

    public function safe_b64encode($string)
    {

        $data = base64_encode($string);
        $data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);
        return $data;
    }

    public function safe_b64decode($string)
    {
        $data = str_replace(array('-', '_'), array('+', '/'), $string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }

    public function encode($value)
    {

        if (!$value) {
            return false;
        }
        $text = $value;
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->skey, $text, MCRYPT_MODE_ECB, $iv);
        return trim($this->safe_b64encode($crypttext));
    }

    public function decode($value)
    {

        if (!$value) {
            return false;
        }
        $crypttext = $this->safe_b64decode($value);
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->skey, $crypttext, MCRYPT_MODE_ECB, $iv);
        return trim($decrypttext);
    }

    function generate_encrypt_link($full_path, $description = null, $extra = null)
    {
        $cryptCode = $this->encode($full_path);
        $crypt_url = site_url('url/open_link/' . $cryptCode);
        $url_description = !empty($description) ? $description : $crypt_url;
        $generatedHTML = '<a href="' . $crypt_url . '" target="_blank" ' . $extra . '>' . $url_description . '</a>';
        return $generatedHTML;
    }

    function generate_encrypt_link_start($full_path, $extra = null)
    {
        $cryptCode = $this->encode($full_path);
        $crypt_url = site_url('url/open_link/' . $cryptCode);
        $generatedHTML = '<a href="' . $crypt_url . '" target="_blank" ' . $extra . '>';
        return $generatedHTML;
    }

    function generate_encrypt_link_end()
    {
        return '</a>';
    }

    function generate_encrypt_link_only($full_path)
    {
        $cryptCode = $this->encode($full_path);
        $crypt_url = site_url('url/open_link/' . $cryptCode);
        return $crypt_url;
    }

}
