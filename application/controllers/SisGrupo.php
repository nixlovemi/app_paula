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

  public function escolheGrupo()
  {
    $vGruposPessoa = $this->session->flashdata('vGruposPessoa') ?? array();
    $vToken        = $this->session->flashdata('vToken') ?? "";

    if(count($vGruposPessoa) <= 0){
      $this->session->set_flashdata('LoginMessage', 'Erro ao buscar os grupos! Faça o login novamente!');
      redirect(BASE_URL . 'Login/grupo');
    } else {
      $this->session->set_flashdata('vToken', $vToken);
      $this->load->view('Login/escolheGrupo', array(
        "vGruposPessoa" => $vGruposPessoa,
      ));
    }
  }

  public function postEscolheGrupo($grpId)
  {
    $vToken = $this->session->flashdata('vToken') ?? "";
    if($vToken == ""){
      $this->session->set_flashdata('LoginMessage', 'Erro ao buscar os grupos do usuário! Faça o login novamente!');
      redirect(BASE_URL . 'Login/grupo');
    } else {
      require_once(APPPATH."/models/TbGrupoPessoa.php");
      $retGRP = pegaGrupoPessoa($grpId);
      if($retGRP["erro"]){
        $this->session->set_flashdata('LoginMessage', $retGRP["msg"]);
        redirect(BASE_URL . 'Login/grupo');
      } else {
        $GrupoPessoa = $retGRP["GrupoPessoa"] ?? array();
        $vPesId      = $GrupoPessoa["grp_pes_id"] ?? 0;
        
        $retGrupos    = pegaGruposPessoaId($vPesId);
        $GruposPessoa = $retGrupos["GruposPessoa"] ?? array();
        $tokenAtual   = encripta_string(json_encode($GruposPessoa));
        
        if($tokenAtual == $vToken){
          $this->session->grp_id = $grpId;
          redirect(BASE_URL . 'SisGrupo');
        } else {
          $this->session->set_flashdata('LoginMessage', 'Erro ao logar no grupo selecionado! Faça o login novamente!');
          redirect(BASE_URL . 'Login/grupo');
        }
      }
    }
  }
}
