<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PessoaCfgTipo extends MY_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->helper("utils_helper");
  }

  public function index()
  {
    require_once(APPPATH."/models/TbPessoaCfgTipo.php");
    $htmlLista = pegaListaPesCfgTipo(true, true, true);

    $this->template->load(TEMPLATE_STR, 'TbPessoaCfgTipo/index', array(
      "titulo"    => gera_titulo_template("Tipo de Configuração"),
      "htmlLista" => $htmlLista,
    ));
  }

  public function novo()
  {
    $PessoaCfgTipo = $this->session->flashdata('PessoaCfgTipo') ?? array();

    $this->template->load(TEMPLATE_STR, 'TbPessoaCfgTipo/novo', array(
      "titulo"         => gera_titulo_template("Tipo de Configuração - Novo"),
      "PessoaCfgTipo" => $PessoaCfgTipo,
    ));
  }

  public function postNovo()
  {
    $variaveisPost  = processaPost();
    $vDescricao     = $variaveisPost->descricao ?? "";

    $PessoaCfgTipo = [];
    $PessoaCfgTipo["pct_descricao"] = $vDescricao;
    $this->session->set_flashdata('PessoaCfgTipo', $PessoaCfgTipo);

    require_once(APPPATH."/models/TbPessoaCfgTipo.php");
    $retInserir = inserePesCfgTipo($PessoaCfgTipo);

    if($retInserir["erro"]){
      geraNotificacao("Aviso!", $retInserir["msg"], "warning");
      redirect(BASE_URL . 'PessoaCfgTipo/novo');
    } else {
      geraNotificacao("Sucesso!", $retInserir["msg"], "success");
      redirect(BASE_URL . 'PessoaCfgTipo');
    }
  }

  public function visualizar($id)
  {
    require_once(APPPATH."/models/TbPessoaCfgTipo.php");
    $ret = pegaPesCfgTipo($id);

    if($ret["erro"]){
      geraNotificacao("Aviso!", $ret["msg"], "warning");
      redirect(BASE_URL . 'PessoaCfgTipo');
    } else {
      $this->template->load(TEMPLATE_STR, 'TbPessoaCfgTipo/visualizar', array(
        "titulo"         => gera_titulo_template("Tipo de Configuração - Visualizar"),
        "PessoaCfgTipo" => $ret["PessoaCfgTipo"],
      ));
    }
  }

  public function editar($id)
  {
    require_once(APPPATH."/models/TbPessoaCfgTipo.php");
    $ret = pegaPesCfgTipo($id);

    if($ret["erro"]){
      geraNotificacao("Aviso!", $ret["msg"], "warning");
      redirect(BASE_URL . 'PessoaCfgTipo');
    } else {
      $PessoaCfgTipo = $this->session->flashdata('PessoaCfgTipo') ?? $ret["PessoaCfgTipo"];

      $this->template->load(TEMPLATE_STR, 'TbPessoaCfgTipo/editar', array(
        "titulo"         => gera_titulo_template("Tipo de Configuração - Editar"),
        "PessoaCfgTipo" => $PessoaCfgTipo,
      ));
    }
  }

  public function postEditar()
  {
    $variaveisPost = processaPost();
    $vId           = $variaveisPost->id ?? "";
    $vDescricao    = $variaveisPost->descricao ?? "";
    $vAtivo        = $variaveisPost->ativo ?? "";

    $PessoaCfgTipo = [];
    $PessoaCfgTipo["pct_id"]        = $vId;
    $PessoaCfgTipo["pct_descricao"] = $vDescricao;
    $PessoaCfgTipo["pct_ativo"]     = (int)$vAtivo;
    $this->session->set_flashdata('PessoaCfgTipo', $PessoaCfgTipo);

    require_once(APPPATH."/models/TbPessoaCfgTipo.php");
    $retInserir = editaPesCfgTipo($PessoaCfgTipo);

    if($retInserir["erro"]){
      geraNotificacao("Aviso!", $retInserir["msg"], "warning");
      redirect(BASE_URL . 'PessoaCfgTipo/editar/' . $vId);
    } else {
      geraNotificacao("Sucesso!", $retInserir["msg"], "success");
      redirect(BASE_URL . 'PessoaCfgTipo');
    }
  }
}