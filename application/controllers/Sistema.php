<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sistema extends MY_Controller
{
  public function __construct()
  {
    $admin = false;
    parent::__construct($admin);
    $this->load->helper("utils_helper");
  }

  public function index()
  {
    $this->template->load(TEMPLATE_STR, 'Sistema/index', array(
      "titulo" => gera_titulo_template("Área Inicial"),
    ));
  }
}