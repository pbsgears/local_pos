<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
| $route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'Login';
$route['LoginPage'] = 'Login';

$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
//$route['Login/(:any)'] = "/Login  /index/$1";
$route['pin_login/(:num)'] = "Login/login_pin/$1";
$route['reset_password/(:any)/(:num)/(:num)'] = "Login/reset_password/$1/$2/$3";
$route['sendMail'] = "SendEmail/sendEmail";
$route['restaurant'] = "Pos_restaurant/pos_terminal_1";
$route['dashboard'] = "Pos_restaurant/pos_terminal_1";
$route['restaurant2'] = "Pos_restaurant/pos_terminal_2";
$route['m-pos'] = "Pos_restaurant/pos_terminal_mobile";
//$route['restaurant'] = "Pos_restaurant";
$route['terminal'] = "Pos_restaurant";

//$route['kitchen/:num'] = "Pos_kitchen/kitchen2/0";
$route['kitchen/:num'] = "Pos_kitchen/kitchen_autoPrint/0";
$route['kot/:num'] = "Pos_kitchen/kitchen_autoPrint/0";
$route['kitchen2'] = "Pos_kitchen";
$route['kot_manual/:num'] = "Pos_kitchen/kitchen_manual_process"; /*Manual Process */
$route['kot_manual/:num/:num'] = "Pos_kitchen/kitchen_manual_process"; /*Manual Process - don't need refresh call  */
$route['touch_restaurant'] = "Pos_restaurant/touchWindow";
$route['batch/updateMenuID'] = "Batch_process/batch_update_menuSalesItems_menuID_menuCategoryID";
$route['batch/updateProfitSales'] = "Batch_process/batch_update_sales_vs_profit";
$route['modules'] = "Employee/moduleView";
$route['modules/(:any)'] = "Employee/modulesdetails/$1";
$route['mfq_poamount'] = "MFQ_Job_Card/fetch_po_unit_cost";
$route['policy'] = "CompanyPolicy/policy";
$route['gears'] = "Login/gears";




