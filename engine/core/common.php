<?php if (! defined('ROOT')) die ('No direct script access allowed');
/**
 * Srikandi PHP Framework
 *
 * @package        	Srikandi
 * @category    	Framework
 * @author			dwi.setiyadi@gmail.com
 */

function config($string = 'No item selected.') {
	if ( ! file_exists(APP.'config.php')) die('Configuration file doesn\'t exist.');
	include APP.'config.php';
	if (isset($items)) {
		if (isset($items[$string])) {
			return $items[$string];
		} else {
			return '';
		}
	} else {
		die('Error configuration file format, no items variable.');
	}
}

function siteUrl($params = '') {
	$params = trim($params, '/');
	$indexpage = trim(config('indexPage'), '/');
	
	$url = baseUrl().$indexpage;
	$url = $url.'/'.$params;
	if ($params != '') {
		$urlsuffix = trim(config('urlSuffix'), '/');
		if ($urlsuffix != '') {
			$url = $url.'.'.$urlsuffix;
		}
	}
	return $url;
}

function baseUrl() {
	$base_url = ((isset($_SERVER ['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
	$base_url .= "://".$_SERVER['HTTP_HOST'];
	$base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']);
	$base_url = trim($base_url, '/').'/';
	return $base_url;
}

function removeInvisibleCharacters($str = '', $url_encoded = TRUE) {
	$non_displayables = array();
	
	if ($url_encoded) {
		$non_displayables[] = '/%0[0-8bcef]/';	// url encoded 00-08, 11, 12, 14, 15
		$non_displayables[] = '/%1[0-9a-f]/';	// url encoded 16-31
	}
	
	$non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';	// 00-08, 11, 12, 14-31, 127

	do
	{
		$str = preg_replace($non_displayables, '', $str, -1, $count);
	}
	while ($count);

	return $str;
}

function sanitizeUri($str = '') {
	if ($str != '' && config('allowedUriString') != '') {
		if (! preg_match("|^[".preg_quote(config('allowedUriString'))."]+$|i", $str)) {
			print_r('Your URI request was not permitted.');
			exit();
		}
	}
	
	$bad = array ('$', '(', ')', '%28', '%29' );
	$good = array ('&#36;', '&#40;', '&#41;', '&#40;', '&#41;' );
	return str_replace($bad, $good, $str);
}

function sanitize($var = '', $type) {
	switch ($type) {
		case 'int': // integer
			$var = (int) $var;
			break;
		case 'str': // trim string
			$var = trim($var);
			break;
		case 'nohtml': // trim string, no HTML allowed
			$var = htmlentities(trim($var), ENT_QUOTES);
			break;
		case 'plain': // trim string, no HTML allowed, plain text
			$var =  htmlentities(trim($var), ENT_NOQUOTES);
			break;
		case 'upper_word': // trim string, upper case words
			$var = ucwords(strtolower(trim($var)));
			break;
		case 'ucfirst': // trim string, upper case first word
			$var = ucfirst(strtolower(trim($var)));
			break;
		case 'lower': // trim string, lower case words
			$var = strtolower(trim($var));
			break;
		case 'urle': // trim string, url encoded
			$var = urlencode(trim($var));
			break;
		case 'trim_urle': // trim string, url decoded
			$var = urldecode(trim($var));
			break;
		case 'telephone': // True/False for a telephone number
			foreach ($var as $x) {
				if ( ! ((ctype_digit($x) || ($x=='+') || ($x=='*') || ($x=='p')))) {
					return false;
				}
			}
			return true;
			break;
		case 'pin': // True/False for a PIN
			if ((strlen($var) != 13) || (ctype_digit($var)!=true)) {
				return false;
			}
			return true;
			break;
		case 'id_card': // True/False for an ID CARD
			if ((ctype_alpha(substr($var, 0, 2)) != true ) || (ctype_digit(substr($var, 2, 6)) != true) || (strlen($var) != 8)) {
				return false;
			}
			return true;
			break;
		case 'sql': // True/False if the given string is SQL injection safe
			//  insert code here, I usually use ADODB -> qstr() but depending on your needs you can use mysql_real_escape();
			return mysql_real_escape_string($var);
			break;
		case 'general':
			$var = trim($var);
			$var = htmlspecialchars($var);
			$var = mysql_real_escape_string($var);
			break;
		default:
			$var = trim($var);
			$var = htmlspecialchars($var);
			$var = mysql_real_escape_string($var);
			break;
	}
	
	return $var;
}

/* end of file */