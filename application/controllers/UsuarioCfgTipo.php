<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UsuarioCfgTipo extends CI_Controller
{

  public function __construct()
  {
    CI_Controller::__construct();
    $this->load->helper("utils_helper");
  }

  public function index()
  {
    $this->template->load(TEMPLATE_STR, 'TbUsuarioCfgTipo/index', array(
      "titulo" => gera_titulo_template("Tipo de Configuração"),
    ));
  }
}