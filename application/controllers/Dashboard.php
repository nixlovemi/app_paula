<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper("utils_helper");
  }

  public function index()
  {
    $this->template->load(TEMPLATE_STR, 'Dashboard/index', array(
      "titulo" => gera_titulo_template("Área Inicial"),
    ));
  }
}