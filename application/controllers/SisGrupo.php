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
    require_once(APPPATH."/models/TbGrupoTimeline.php");
    $vGrpId = $this->session->grp_id ?? NULL;

    require_once(APPPATH."/models/TbGrupoPessoa.php");
    $retGP       = pegaGrupoPessoa($vGrpId);
    $GrupoPessoa = (!$retGP["erro"] && isset($retGP["GrupoPessoa"])) ? $retGP["GrupoPessoa"]: array();

    require_once(APPPATH."/models/TbGrupoTimeline.php");
    geraHtmlViewGrupoTimeline($GrupoPessoa);
  }

  public function indexInfo($grpId, $programado=0)
  {
    $vGrpId = $grpId ?? "";
    
    require_once(APPPATH."/models/TbGrupoPessoa.php");
    $retGP       = pegaGrupoPessoa($vGrpId);
    $GrupoPessoa = (!$retGP["erro"] && isset($retGP["GrupoPessoa"])) ? $retGP["GrupoPessoa"]: array();
    $vGruId      = $GrupoPessoa["grp_gru_id"] ?? "";
    $vGruLogado  = pegaGrupoLogadoId();

    $vGrpLogado  = pegaGrupoPessoaLogadoId();
    $ehGrp       = ($grpId == $vGrpLogado);
    $ehStaff     = ehStaffGrupo($vGrpLogado);
    if(!$ehStaff || !$ehGrp){
      $programado = 0;
    }

    // valida grupo logado
    if($vGruId != $vGruLogado){
      geraNotificacao("Aviso!", "Esse conteúdo não faz parte do seu grupo!", "warning");
      redirect(BASE_URL . 'SisGrupo');
      return;
    }

    require_once(APPPATH."/models/TbGrupoTimeline.php");
    geraHtmlViewGrupoTimeline($GrupoPessoa, array(
      "postagem_propria"  => true,
      "apenas_programado" => ($programado == 1)
    ));
  }

  public function favoritos($grpId)
  {
    $vGrpId = $grpId ?? "";

    require_once(APPPATH."/models/TbGrupoPessoa.php");
    $retGP       = pegaGrupoPessoa($vGrpId);
    $GrupoPessoa = (!$retGP["erro"] && isset($retGP["GrupoPessoa"])) ? $retGP["GrupoPessoa"]: array();
    $vGruId      = $GrupoPessoa["grp_gru_id"] ?? "";
    $vGruLogado  = pegaGrupoLogadoId();
    $vGrpLogado  = pegaGrupoPessoaLogadoId();

    // valida grupo logado
    if($vGruId != $vGruLogado){
      geraNotificacao("Aviso!", "Esse conteúdo não faz parte do seu grupo!", "warning");
      redirect(BASE_URL . 'SisGrupo');
      return;
    }
    if($grpId <> $vGrpLogado){
      geraNotificacao("Aviso!", "Esse conteúdo não pode ser acessado!", "warning");
      redirect(BASE_URL . 'SisGrupo');
      return;
    }

    require_once(APPPATH."/models/TbGrupoTimeline.php");
    geraHtmlViewGrupoTimeline($GrupoPessoa, array(
      "postagem_propria" => false,
      "apenas_favoritos" => true,
    ));
  }

  public function privada()
  {
    $vGrpLogado = pegaGrupoPessoaLogadoId();
    $ehStaff    = ehStaffGrupo($vGrpLogado);

    require_once(APPPATH."/models/TbGrupoPessoa.php");
    $retGP       = pegaGrupoPessoa($vGrpLogado);
    $GrupoPessoa = (!$retGP["erro"] && isset($retGP["GrupoPessoa"])) ? $retGP["GrupoPessoa"]: array();

    if(!$ehStaff){
      geraNotificacao("Aviso!", "Esse conteúdo não pode ser acessado!", "warning");
      redirect(BASE_URL . 'SisGrupo');
      return;
    }

    require_once(APPPATH."/models/TbGrupoTimeline.php");
    geraHtmlViewGrupoTimeline($GrupoPessoa, array(
      "apenas_privado" => true
    ));
  }
}
