<?php
class RouteProcess{
    function afterroute(){
        $this->CI =&get_instance();
        $this->output->set_header('X-FRAME-OPTIONS: DENY');
        $this->output->set_header('X-XSS-Protection: 1; mode=block');
        $this->output->set_header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        $this->output->set_header('X-XSS-Protection: 1; mode=block');
        $this->output->set_header('X-Frame-Options: deny');
        $this->output->set_header('X-Content-Type-Options: nosniff');
        $this->output->set_header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline';");
        echo $this->CI->output->get_output();
    }
}