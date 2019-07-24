<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UsuarioCfgTipo extends MY_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->helper("utils_helper");
  }

  public function index()
  {
    require_once(APPPATH."/models/TbUsuarioCfgTipo.php");
    $htmlLista = pegaListaUsuCfgTipo(true, true, true);

    $this->template->load(TEMPLATE_STR, 'TbUsuarioCfgTipo/index', array(
      "titulo"    => gera_titulo_template("Tipo de Configuração"),
      "htmlLista" => $htmlLista,
    ));
  }

  public function novo()
  {
    $UsuarioCfgTipo = $this->session->flashdata('UsuarioCfgTipo') ?? array();

    $this->template->load(TEMPLATE_STR, 'TbUsuarioCfgTipo/novo', array(
      "titulo"         => gera_titulo_template("Tipo de Configuração - Novo"),
      "UsuarioCfgTipo" => $UsuarioCfgTipo,
    ));
  }

  public function postNovo()
  {
    $variaveisPost  = processaPost();
    $vDescricao     = $variaveisPost->descricao ?? "";

    $UsuarioCfgTipo = [];
    $UsuarioCfgTipo["uct_descricao"] = $vDescricao;
    $this->session->set_flashdata('UsuarioCfgTipo', $UsuarioCfgTipo);

    require_once(APPPATH."/models/TbUsuarioCfgTipo.php");
    $retInserir = insereUsuCfgTipo($UsuarioCfgTipo);
    
    if($retInserir["erro"]){
      geraNotificacao("Aviso!", $retInserir["msg"], "warning");
      redirect(BASE_URL . 'UsuarioCfgTipo/novo');
    } else {
      geraNotificacao("Sucesso!", $retInserir["msg"], "success");
      redirect(BASE_URL . 'UsuarioCfgTipo');
    }
  }

  public function visualizar($id)
  {
    require_once(APPPATH."/models/TbUsuarioCfgTipo.php");
    $ret = pegaUsuCfgTipo($id);

    if($ret["erro"]){
      geraNotificacao("Aviso!", $ret["msg"], "warning");
      redirect(BASE_URL . 'UsuarioCfgTipo');
    } else {
      $this->template->load(TEMPLATE_STR, 'TbUsuarioCfgTipo/visualizar', array(
        "titulo"         => gera_titulo_template("Tipo de Configuração - Visualizar"),
        "UsuarioCfgTipo" => $ret["UsuarioCfgTipo"],
      ));
    }
  }

  public function editar($id)
  {
    require_once(APPPATH."/models/TbUsuarioCfgTipo.php");
    $ret = pegaUsuCfgTipo($id);

    if($ret["erro"]){
      geraNotificacao("Aviso!", $ret["msg"], "warning");
      redirect(BASE_URL . 'UsuarioCfgTipo');
    } else {
      $UsuarioCfgTipo = $this->session->flashdata('UsuarioCfgTipo') ?? $ret["UsuarioCfgTipo"];

      $this->template->load(TEMPLATE_STR, 'TbUsuarioCfgTipo/editar', array(
        "titulo"         => gera_titulo_template("Tipo de Configuração - Editar"),
        "UsuarioCfgTipo" => $UsuarioCfgTipo,
      ));
    }
  }

  public function postEditar()
  {
    $variaveisPost = processaPost();
    $vId           = $variaveisPost->id ?? "";
    $vDescricao    = $variaveisPost->descricao ?? "";
    $vAtivo        = $variaveisPost->ativo ?? "";

    $UsuarioCfgTipo = [];
    $UsuarioCfgTipo["uct_id"]        = $vId;
    $UsuarioCfgTipo["uct_descricao"] = $vDescricao;
    $UsuarioCfgTipo["uct_ativo"]     = $vAtivo;
    $this->session->set_flashdata('UsuarioCfgTipo', $UsuarioCfgTipo);

    require_once(APPPATH."/models/TbUsuarioCfgTipo.php");
    $retInserir = editaUsuCfgTipo($UsuarioCfgTipo);

    if($retInserir["erro"]){
      geraNotificacao("Aviso!", $retInserir["msg"], "warning");
      redirect(BASE_URL . 'UsuarioCfgTipo/editar/' . $vId);
    } else {
      geraNotificacao("Sucesso!", $retInserir["msg"], "success");
      redirect(BASE_URL . 'UsuarioCfgTipo');
    }
  }
}