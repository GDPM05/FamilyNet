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
|	https://codeigniter.com/userguide3/general/routing.html
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
|	$route['translate_uri_dashes'] = FALSE;
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
$route['default_controller'] = 'Login/login';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
$route['google_auth'] = 'auth/google_auth';
$route['callback'] = 'auth/callback';
$route['logout'] = 'auth/logout';
$route['signup'] = 'Signup';
$route['signup/upload'] = 'Signup/upload';
$route['complete_signup'] = 'Signup/complete';
$route['complete_signup_validation'] = 'Signup/complete_validation';
$route['main'] = 'Main';
$route['profile'] = 'Profile';
$route['direct_msg'] = 'Directmsg';
$route['signup/verify'] = 'Signup/verify';
$route['logout'] = 'Login/logout';
$route['search'] = 'Search';
$route['search/(:num)'] = 'Search';
$route['fetch'] = 'Search/fetch';
$route['see_profile/(:any)'] = 'Profile/load_profile';
$route['send_invite/(:num)'] = 'Profile/send_friend_invitation';
$route['invites/(:any)/(:num)'] = 'Profile/update_invite/$1/$2';
$route['notification'] = 'Notification';
$route['notification/(:num)'] = 'Notification';
$route['get_messages/(:num)/(:num)'] = 'Directmsg/get_messages';
$route['fetch_user/(:num)'] = 'Directmsg/fetch_user';
$route['send_message'] = 'Directmsg/send_message';
$route['family_menu'] = 'Family';
$route['new_family'] = 'Family/new_family';