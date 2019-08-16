<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class GrupoTimeline extends MY_Controller
{
  var $cliente_logado;

  public function __construct()
  {
      $admin = false;
      $grupo = true;
      parent::__construct($admin, $grupo);
      $this->load->helper("utils_helper");

      $this->cliente_logado = $this->session->usuario_info ?? array();
  }
}