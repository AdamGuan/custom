<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Model extends CI_Model {
	public $my_config;

	public function __construct() {
		parent :: __construct(); 
		
		//get config
		$this -> load -> config('my_config', true, true);
		$this -> my_config = $this -> config -> item('my_config');
	} 

} 
/**
 * End of file MY_Model.php
 */
/**
 * Location: ./application/core/MY_Model.php
 */