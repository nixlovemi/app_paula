<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SisGrupo extends MY_Controller
{
  public function __construct()
  {
    $admin = false;
    $grupo = true;
    parent::__construct($admin, $grupo);
    $this->load->helper("utils_helper");
  }

  public function index()
  {
    $this->template->load(TEMPLATE_STR, 'SisGrupo/index', array(
      "titulo" => gera_titulo_template("Área Inicial"),
    ));
  }
}