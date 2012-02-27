<?php if (! defined('ROOT')) die ('No direct script access allowed');
/**
 * Srikandi PHP Framework
 *
 * @package        	Srikandi
 * @category    	Framework
 * @author			dwi.setiyadi@gmail.com
 */

require_once(SYS.'core/launcher.php');
require_once(SYS.'core/input.php');

class Controller
{
	private static $instance;
	protected $launch;
	
	function __construct() {
		self::$instance =& $this;
		$this->launch = new Launcher();
		$this->input = new Input();
	}
	
	public function route() {
		$uriString = $this->rawUri();
		$uriString = removeInvisibleCharacters($uriString);
		$uriString = ($uriString == '/') ? '' : $uriString;
		$uriString = str_replace('.'.config('urlSuffix'), '', $uriString);
		$value['uri'] = $uriString;
		
		$config = config('route');
		if ( ! isset($config['defaultClass'])) die('Default class on route configuration is not defined.');
		
		/* explode URI string */
		if ($uriString != '') {
			if (isset($config[$uriString])) {
				$segments = array();
				foreach (explode("/", $config[$uriString]) as $val ) {
					$val = trim(sanitizeUri($val));
					if ($val != '') {
						$segments[] = strtolower($val);
					}
				}
				array_unshift($segments, NULL);
				unset($segments[0]);
			}
			if ( ! isset($segments)) {
				$segments = array();
				foreach ($config as $key => $val) {
					$key = str_replace(':any', '.+', str_replace(':num', '[0-9]+', $key));
					if (preg_match('#^'.$key.'$#', $uriString)) {
						if (strpos($val, '$') !== FALSE and strpos($key, '(') !== FALSE) {
							$val = preg_replace('#^'.$key.'$#', $val, $uriString);
						}
						$segments = explode('/', $val);
						array_unshift($segments, NULL);
						unset($segments[0]);
						break;
					}
				}
			}
			if (count($segments) == 0) {
				$segments = explode('/', $uriString);
				array_unshift($segments, NULL);
				unset($segments[0]);
			}
		} else {
			$segments[1] = $config['defaultClass'];
			$segments[2] = 'index';
		}
		/* EOF explode URI string */
		
		// get class
		$value['controller'] = $segments[1];
		unset($segments[1]);
		
		// get function
		if (isset($segments[2])) {
			$value['action'] = $segments[2];
			unset($segments[2]);
		} else {
			$value['action'] = 'index';
		}
		
		// get params
		$value['params'] = array();
		if (count($segments) > 0) {
			foreach ($segments as $val) {
				$value['params'][] = $val;
			}
			array_unshift($value['params'], NULL);
			unset($value['params'][0]);
			ksort($value['params']);
		}
		
		return json_decode(json_encode($value));
	}
	
	public function uri($segment = 0) {
		$uriString = $this->rawUri();
		$uriString = removeInvisibleCharacters($uriString);
		$uriString = ($uriString == '/') ? '' : $uriString;
		$uriString = str_replace('.'.config('urlSuffix'), '', $uriString);
		
		/* explode URI string */
		$segments = array();
		foreach (explode("/", $uriString) as $val ) {
			$val = trim(sanitizeUri($val));
			if ($val != '') {
				$segments[] = strtolower($val);
			}
		}
		array_unshift($segments, NULL);
		unset($segments[0]);
		$segments[0] = trim($uriString);
		ksort($segments);
		/* EOF explode URI string */
		
		$segment = (int) $segment;
		if (isset($segments[$segment])) {
			return $segments[$segment];
		} else {
			return '';
		}
	}
	
	private function rawUri() {
		if (strtoupper(config('uriProtocol')) == 'AUTO') {
			if (php_sapi_name() == 'cli' or defined('STDIN')) {
				$uriString = array_slice($_SERVER['argv'], 1);
				return $uriString ? '/' . implode('/', $uriString) : '';
			}
			
			if ($uri = $this->detectUri()) return $uri;
			
			$path = (isset($_SERVER['PATH_INFO'])) ? $_SERVER['PATH_INFO'] : @getenv('PATH_INFO');
			if (trim($path, '/') != '' && $path != "/".SELF) return $path;
			
			$path =  (isset($_SERVER['QUERY_STRING'])) ? $_SERVER['QUERY_STRING'] : @getenv('QUERY_STRING');
			if (trim($path, '/') != '') return $path;
			
			if (is_array($_GET) && count($_GET) == 1 && trim(key($_GET), '/') != '') return key($_GET);
			if ( ! isset($uriString)) return '';
		}

		$uri = strtoupper(config('uriProtocol'));
		if ($uri == 'REQUEST_URI') {
			return $this->detectUri();
		} elseif ($uri == 'CLI') {
			$uriString = array_slice($_SERVER['argv'], 1);
			return $uriString ? '/' . implode('/', $uriString) : '';
		}
	}
	
	private function detectUri() {
		if ( ! isset($_SERVER['REQUEST_URI']) OR ! isset($_SERVER['SCRIPT_NAME'])) return '';

		$uri = $_SERVER['REQUEST_URI'];
		if (strpos($uri, $_SERVER['SCRIPT_NAME']) === 0) {
			$uri = substr($uri, strlen($_SERVER['SCRIPT_NAME']));
		} elseif (strpos($uri, dirname($_SERVER['SCRIPT_NAME'])) === 0) {
			$uri = substr($uri, strlen(dirname($_SERVER['SCRIPT_NAME'])));
		}
		
		if (strncmp($uri, '?/', 2) === 0) $uri = substr($uri, 2);
		
		$parts = preg_split('#\?#i', $uri, 2);
		$uri = $parts[0];
		if (isset($parts[1])) {
			$_SERVER['QUERY_STRING'] = $parts[1];
			parse_str($_SERVER['QUERY_STRING'], $_GET);
		} else {
			$_SERVER['QUERY_STRING'] = '';
			$_GET = array();
		}

		if ($uri == '/' || empty($uri)) return '/';

		$uri = parse_url($uri, PHP_URL_PATH);
		
		return str_replace(array('//', '../'), '/', trim($uri, '/'));
	}

	public static function &getInstance() {
		return self::$instance;
	}
}

/* end of file */