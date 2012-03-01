<?php if (phpversion() < 5) die ('Your PHP version is '.phpversion().'. This Framework running on PHP 5 or above only.');
/**
 * Srikandi PHP Framework
 *
 * @package      Srikandi
 * @category     Framework
 * @author       dwi.setiyadi@gmail.com
 * @version      0.3
 */
 
// Framework Configuration
date_default_timezone_set('Asia/Jakarta');
$environtment = 'development'; // option: development, production
$application = 'application';
$engine = 'engine';

// Assign super global
define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
define('ROOT', str_replace('\\', '/', getcwd().'/'));
define('APP', ROOT.$application.'/');
define('SYS', ROOT.$engine.'/');

unset($application, $system);
require_once SYS.'core/bootstrap.php';
/* end of file */