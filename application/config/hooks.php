<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/user_guide/general/hooks.html
|
*/

$hook['pre_controller'] = array(
    'class'    => 'Authenticate',
    'function' => 'check_user_login',
    'filename' => 'Authenticate.php',
    'filepath' => 'hooks',
    'params'   => array()
);

/*$hook['display_override'] = array(
    'class'    => 'RouteProcess',
    'function' => 'afterroute',
    'filename' => 'RouteProcess.php',
    'filepath' => 'hooks',
    'params'   => array()
);*/
