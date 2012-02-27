<?php if (! defined('ROOT')) die ('No direct script access allowed');

class welcome extends Controller
{
	function __construct() {
		parent::__construct();
	}
	
	function index() {
		echo 'hello world';
		echo '<pre>';
		print_r($this->route());
		echo '</pre>';
	}
}

/* end of file */