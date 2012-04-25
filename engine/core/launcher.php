<?php if (! defined('ROOT')) die ('No direct script access allowed');
/**
 * Srikandi PHP Framework
 *
 * @package        	Srikandi
 * @category    	Framework
 * @author		dwi.setiyadi@gmail.com
 */

class Launcher
{
	public function model($name = '') {
		require_once(SYS.'core/model.php');
		if (file_exists(APP.'models/'.$name.'.php')) {
			require_once(APP.'models/'.$name.'.php');
		} else {
			die('Unable to load the model file <em>'.$name.'.php<em>');
		}
		
		$calledClass = $name;
		
		if ( ! class_exists($name)) {
			die('Your model name class '.$name.' was not found.');
		} else {
			return $this->$calledClass = new $name();
		}
	}
	
	public function view($name = '', $data = array(), $return = FALSE) {
		if (is_array($data)) extract($data);
		
		if (is_array($name)) {
			$location = '';
			foreach ($name as $val) {
				$location .= $val.'/';
			}
			$location = trim($location, '/');
			$view_path = APP.'views/'.$location.'.php';
		} else {
			$view_path = APP.'views/'.$name.'.php';
		}
		
		if ( ! file_exists($view_path)) {
			die('Unable to load the requested view file <em>'.$name.'.php</em>');
		}
		
		ob_start();
		include $view_path;
		
		if ($return === TRUE) {		
			$buffer = ob_get_contents();
			@ob_end_clean();
			return $buffer;
		}
		
		if (ob_get_level() > $this->_ob_level + 1) {
			ob_end_flush();
		}
	}
	
	public function library($name = '', $config = '') {
		$name = strtolower($name);
		
		if (file_exists(SYS.'libraries/'.$name.'.php')) {
			require_once(SYS.'libraries/'.$name.'.php');
		} elseif (file_exists(APP.'libraries/'.$name.'.php')) {
			require_once(APP.'libraries/'.$name.'.php');
		} else {
			die('Unable to load library file <em>'.$name.'.php<em>');
		}
		
		$calledClass = $name;
		
		if ( ! class_exists($name)) {
			die('Your library name class '.$name.' was not found.');
		} else {
			if ($config == '') {
				return $this->$calledClass = new $name();
			} else {
				return $this->$calledClass = new $name($config);
			}
		}
	}
	
	public function helper($name = '') {
		if ( ! file_exists(APP.'helpers/'.$name.'.php')) {
			die('Unable to load helper file <em>'.$name.'.php</em>');
		}
		return require_once(APP.'helpers/'.$name.'.php');
	}
}

/* end of file */