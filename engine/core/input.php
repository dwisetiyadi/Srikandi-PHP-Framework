<?php if (! defined('ROOT')) die ('No direct script access allowed');
/**
 * Srikandi PHP Framework
 *
 * @package        	Srikandi
 * @category    	Framework
 * @author			dwi.setiyadi@gmail.com
 */

class Input
{
	public function get($string = '') {
		$url = $_SERVER['QUERY_STRING'];
		$url = removeInvisibleCharacters($url);
		$url = explode('&', $url);
		
		$params = array();
		foreach($url as $val) {
			if ($val != '') {
				$param = explode('=', $val);
				if (count($param) > 0) $params[sanitizeUri($param[0])] = sanitizeUri($param[1]);
			}
		}
		
		if (isset($params[$string])) {
			return $params[$string];
		} else {
			return '';
		}
	}
	
	public function post($string = '', $type = 'general') {
		if (isset($_POST[$string])) {
			$data = sanitize($_POST[$string], $type);
			return $data;
		}
		return '';
	}
}

/* end of file */