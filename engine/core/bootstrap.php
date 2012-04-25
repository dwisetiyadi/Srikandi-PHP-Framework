<?php if (! defined('ROOT')) die ('No direct script access allowed');
/**
 * Srikandi PHP Framework
 *
 * @package        	Srikandi
 * @category    	Framework
 * @author		dwi.setiyadi@gmail.com
 */

switch ($environtment) {
	case 'development':
		ini_set('display_errors','On');
		error_reporting(E_ALL);
	break;
	case 'production':
		ini_set('display_errors','Off');
		error_reporting(0);
	break;
	default:
		exit('The application environment is not set correctly.');
}

require_once(SYS.'core/common.php');
require_once(SYS.'core/controller.php');

function &getInstance() {
	return controller::getInstance();
}

$control = new Controller();

if (file_exists(APP.'controllers/'.$control->route()->controller.'.php')) {
	include APP.'controllers/'.$control->route()->controller.'.php';
} else {
	if ($environtment === 'development') {
		die('The file '.$control->route()->controller.'.php was not found');
	} else {
		die('<h1>404</h1>Page not found</h1>');
	}
}

//call controller class
$controller = $control->route()->controller;
if (class_exists($controller)) {
	$controller = new $controller();
} else {
	if ($environtment === 'development') {
		die('The class '.$controller.' on '.$router->controller.'.php was not found');
	} else {
		die('<h1>404</h1>Page not found</h1>');
	}
}

//call action
if (method_exists($controller, '_remap')) {
	$controller->_remap($control->route()->action);
} else {
	if (! in_array($control->route()->action, array_map('strtolower', get_class_methods($controller)))) {
		if ($environtment === 'development') {
			die('The <em>'.$control->route()->controller.'</em> controller with <em>'.$control->route()->action.'</em> action was not found.');
		} else {
			die('<h1>404</h1>Page not found</h1>');
		}
	}
	call_user_func_array(array($controller, $control->route()->action), $control->route()->params);
}

unset($environtment, $controller);

/* end of file */