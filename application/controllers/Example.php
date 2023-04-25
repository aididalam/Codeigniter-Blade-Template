<?php
defined('BASEPATH') or exit('No direct script access allowed');

use \Aidid\BladeView\BladeView;


class Example extends CI_Controller {
	public $bladeview;
	public function __construct() {
		parent::__construct();
		$this->bladeview = new BladeView();
	}

	public function index() {
		$this->bladeview->render('templated-view');
	}

	public function viewWithVariable() {
		$foo = "Foo";
		$bar = "Bar";
		$this->bladeview->render('view-with-variable', compact('foo', 'bar'));
	}

	public function directRender() {
		$string = "{{\$foo}} {{\$bar}}";
		$data = array(
			"foo" => "<b>Hello</b>",
			"bar" => "World",
		);
		$this->bladeview->render($string, $data, false); //3rd parameter false is important
	}
}
