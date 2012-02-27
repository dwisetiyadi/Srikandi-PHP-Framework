<?php if (! defined('ROOT')) die ('No direct script access allowed');
/**
 * Srikandi PHP Framework
 *
 * @package        	Srikandi
 * @category    	Framework
 * @author			dwi.setiyadi@gmail.com
 */

class Model
{
	protected $db;
	
	public function database($config = array()) {
		include_once SYS.'database/NotORM.php';
		
		if ( ! isset($config['class'])) $config['class'] = 'notorm';
		if ( ! isset($config['engine'])) $config['engine'] = 'mysql';
		if ( ! isset($config['host'])) $config['host'] = 'localhost';
		if ( ! isset($config['dbname'])) $config['database'] = 'srikandi';
		if ( ! isset($config['dbuser'])) $config['user'] = 'user';
		if ( ! isset($config['dbpass'])) $config['user'] = 'password';
		if ( ! isset($config['connectionName'])) $config['connectionName'] = 'db';
		
		// for PDO database service, tested on MySQL, SQLite, PostgreSQL, MS SQL
		if ($config['class'] === 'notorm') {
			if (method_exists($this, $config['connectionName'])) {
				die('Method name '.$config['connectionName'].' has been used.');
			} else {
				$pdo = new PDO($config['engine'].':host='.$config['host'].';dbname='.$config['dbname'].'', $config['dbuser'], $config['dbpass']);
				if ($config['connectionName'] === 'db') {
					$this->db = new NotORM($pdo);
				} else {
					$this->db->$config['connectionName'] =& new NotORM($pdo);
				}
			}
		}
	}
}

/* End of file */