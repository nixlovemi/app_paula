<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Grupo extends MY_Controller
{
  public function __construct()
  {
    $admin = false;
    parent::__construct($admin);
    $this->load->helper("utils_helper");
  }

  public function index()
  {
    require_once(APPPATH."/models/TbGrupo.php");
    $htmlLista = pegaListaGrupo(true, true, true);

    $this->template->load(TEMPLATE_STR, 'TbGrupo/index', array(
      "titulo"    => gera_titulo_template("Grupo"),
      "htmlLista" => $htmlLista,
    ));
  }

  public function novo()
  {
    $Grupo = $this->session->flashdata('Grupo') ?? array();

    require_once(APPPATH."/models/TbPessoa.php");
    $filtro     = array("pes_usu_id"=>pegaUsuarioLogadoId());
    $retP       = pegaTodasPessoas($filtro);
    $arrPessoas = ($retP["erro"]) ? array(): $retP["arrGrupo"];

    $this->template->load(TEMPLATE_STR, 'TbGrupo/novo', array(
      "titulo"     => gera_titulo_template("Grupo - Novo"),
      "Grupo"      => $Grupo,
      "arrPessoas" => $arrPessoas,
    ));
  }

  public function postNovo()
  {
    require_once(APPPATH."/helpers/utils_helper.php");
    $variaveisPost = processaPost();

    $vDescricao    = $variaveisPost->descricao ?? "";
    $vInicio       = $variaveisPost->inicio ?? "";
    $vTermino      = $variaveisPost->termino ?? "";
    $vArrPessoas   = $variaveisPost->participantes ?? array();

    $Grupo = [];
    $Grupo["gru_descricao"]  = $vDescricao;
    $Grupo["gru_dt_inicio"]  = acerta_data($vInicio);
    $Grupo["gru_dt_termino"] = acerta_data($vTermino);
    $Grupo["gru_usu_id"]     = pegaUsuarioLogadoId();
    $this->session->set_flashdata('Grupo', $Grupo);

    require_once(APPPATH."/models/TbGrupo.php");
    $retInserir = insereGrupo($Grupo);

    if($retInserir["erro"]){
      geraNotificacao("Aviso!", $retInserir["msg"], "warning");
      redirect(BASE_URL . 'Grupo/novo');
    } else {
      // insere as pessoas do grupo
      $arrGrupoPessoa = [];
      foreach($vArrPessoas as $idPessoa){
        $arrGrupoPessoa[] = array(
          "grp_gru_id" => $retInserir["gruId"],
          "grp_pes_id" => $idPessoa,
        );
      }

      require_once(APPPATH."/models/TbGrupoPessoa.php");
      insereGrupoPessoaBatch($arrGrupoPessoa);
      // ==========================

      geraNotificacao("Sucesso!", $retInserir["msg"], "success");
      redirect(BASE_URL . 'Grupo/editar/' . $retInserir["gruId"]);
    }
  }
  
  public function editar($id)
  {
    require_once(APPPATH."/models/TbGrupo.php");
    $ret   = pegaGrupo($id);
    $Grupo = $this->session->flashdata('Grupo') ?? $ret["Grupo"];
    
    require_once(APPPATH."/models/TbGrupoPessoa.php");
    $htmlGP = pegaListaGrupoPessoa($id, false, false, false);

    if($ret["erro"]){
      geraNotificacao("Aviso!", $ret["msg"], "warning");
      redirect(BASE_URL . 'Grupo');
    } else {
      $this->template->load(TEMPLATE_STR, 'TbGrupo/editar', array(
        "titulo" => gera_titulo_template("Grupo - Editar"),
        "Grupo"  => $Grupo,
        "htmlGP" => $htmlGP,
      ));
    }
  }

}