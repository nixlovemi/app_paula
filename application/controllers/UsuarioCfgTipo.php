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
    require_once(APPPATH."/helpers/utils_helper.php");
    $CI = pega_instancia();
    $CI->load->database();

    require_once(FCPATH."/assets/Lista_CI/Lista_CI.php");
    $Lista_CI = new Lista_CII($CI->db);
    $Lista_CI->addField("uct_id AS id");
    $Lista_CI->addField("uct_descricao AS \"Descrição\"", "L");
    $Lista_CI->addField("uct_ativo AS \"Ativo\"");
    $Lista_CI->addFrom("tb_usuario_cfg_tipo");
    $Lista_CI->changeOrderCol(2);

    $Lista_CI->addFilter("uct_id", "id", "numeric");
    $Lista_CI->addFilter("uct_descricao", "Descrição");
    $Lista_CI->addFilter("uct_ativo", "Ativo", "numeric");

    $htmlLista = $Lista_CI->getHtml();

    $this->template->load(TEMPLATE_STR, 'TbUsuarioCfgTipo/index', array(
      "titulo"    => gera_titulo_template("Tipo de Configuração"),
      "htmlLista" => $htmlLista,
    ));
  }

  public function novo()
  {
    $this->template->load(TEMPLATE_STR, 'TbUsuarioCfgTipo/novo', array(
      "titulo"    => gera_titulo_template("Tipo de Configuração - Novo"),
    ));
  }
}