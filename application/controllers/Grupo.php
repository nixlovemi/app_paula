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
    $filtro     = array("pes_pes_id"=>pegaUsuarioLogadoId());
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
    $Grupo["gru_pes_id"]     = pegaUsuarioLogadoId();
    $this->session->set_flashdata('Grupo', $Grupo);

    require_once(APPPATH."/models/TbGrupo.php");
    $retInserir = insereGrupo($Grupo);

    if($retInserir["erro"]){
      geraNotificacao("Aviso!", $retInserir["msg"], "warning");
      redirect(BASE_URL . 'Grupo/novo');
    } else {
      $gruIdInserido = $retInserir["gruId"] ?? "";

      // insere as pessoas do grupo
      $arrGrupoPessoa = [];
      foreach($vArrPessoas as $idPessoa){
        $arrGrupoPessoa[] = array(
          "grp_gru_id" => $gruIdInserido,
          "grp_pes_id" => $idPessoa,
        );
      }
      $arrGrupoPessoa[] = array(
        "grp_gru_id" => $gruIdInserido,
        "grp_pes_id" => pegaUsuarioLogadoId(),
      );

      require_once(APPPATH."/models/TbGrupoPessoa.php");
      insereGrupoPessoaBatch($arrGrupoPessoa);
      // ==========================

      geraNotificacao("Sucesso!", $retInserir["msg"], "success");
      redirect(BASE_URL . 'Grupo/editar/' . $gruIdInserido);
    }
  }
  
  public function editar($id)
  {
    require_once(APPPATH."/models/TbGrupo.php");
    $ret   = pegaGrupo($id);
    $Grupo = $this->session->flashdata('Grupo') ?? $ret["Grupo"];
    
    require_once(APPPATH."/models/TbGrupoPessoa.php");
    $htmlGP = pegaListaGrupoPessoa($id, false, true, false);

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

  public function postEditar()
  {
    require_once(APPPATH."/helpers/utils_helper.php");
    $variaveisPost = processaPost();

    $vId      = $variaveisPost->id ?? "";
    $vInicio  = $variaveisPost->inicio ?? "";
    $vTermino = $variaveisPost->termino ?? "";
    $vAtivo   = $variaveisPost->ativo ?? 1;

    $Grupo = [];
    $Grupo["gru_id"]         = $vId;
    $Grupo["gru_dt_inicio"]  = acerta_data($vInicio);
    $Grupo["gru_dt_termino"] = acerta_data($vTermino);
    $Grupo["gru_ativo"]      = (int)$vAtivo;
    $this->session->set_flashdata('Grupo', $Grupo);

    require_once(APPPATH."/models/TbGrupo.php");
    $retEditar = editaGrupo($Grupo);

    if($retEditar["erro"]){
      geraNotificacao("Aviso!", $retEditar["msg"], "warning");
      redirect(BASE_URL . 'Grupo/editar/' . $vId);
    } else {
      geraNotificacao("Sucesso!", $retEditar["msg"], "success");
      redirect(BASE_URL . 'Grupo/editar/' . $vId);
    }
  }

  public function visualizar($id)
  {
    require_once(APPPATH."/models/TbGrupo.php");
    $ret = pegaGrupo($id);

    if($ret["erro"]){
      geraNotificacao("Aviso!", $ret["msg"], "warning");
      redirect(BASE_URL . 'Grupo');
    } else {
      require_once(APPPATH."/models/TbGrupoPessoa.php");
      $htmlGP = pegaListaGrupoPessoa($id, true, false, false);

      $this->template->load(TEMPLATE_STR, 'TbGrupo/visualizar', array(
        "titulo" => gera_titulo_template("Grupo - Visualizar"),
        "Grupo"  => $ret["Grupo"],
        "htmlGP" => $htmlGP,
      ));
    }
  }

  public function infoPessoa($gru_id, $pes_id, $editar)
  {
    require_once(APPPATH."/models/TbGrupoPessoa.php");
    $ret = pegaGrupoPessoaPesGru($pes_id, $gru_id, false);

    if($ret["erro"]){
      geraNotificacao("Aviso!", $ret["msg"], "warning");
      redirect(BASE_URL . 'Grupo/editar/' . $gru_id);
    } else {
      $GrupoPessoa = $ret["GrupoPessoa"] ?? array();
      $vPetCliente = $GrupoPessoa["pet_cliente"] ?? 0;
      if($vPetCliente == 0){
        geraNotificacao("Aviso!", "Pessoa não é cliente para exibir essas informações!", "warning");
        redirect(BASE_URL . 'Grupo/editar/' . $gru_id);
      } else {
        // info do grupo
        require_once(APPPATH."/models/TbGrupo.php");
        $retG = pegaGrupo($gru_id, false);

        // info dos lancamentos
        require_once(APPPATH."/models/TbGrupoPessoaInfo.php");
        $retI               = pegaGrupoPessoaInfoPesGru($pes_id, $gru_id);
        $GrupoPessoaInfo    = $retI["GrupoPessoaInfo"] ?? array();
        $retGrp             = agrupaGrupoPessoaInfoLancamentos($GrupoPessoaInfo);
        $GrupoPessoaInfoGrp = $retGrp["GrupoPessoaInfoGrp"] ?? array();

        // lista das pesagens
        require_once(APPPATH."/models/TbGrupoPessoaInfo.php");
        $vGrpId   = $GrupoPessoa["grp_id"] ?? "";
        $htmlPeso = pegaListaGrupoPessoaInfo($vGrpId, false, $editar, $editar);

        $this->template->load(TEMPLATE_STR, 'TbGrupo/infoPessoa', array(
          "titulo"             => gera_titulo_template("Grupo - Informação do Participante"),
          "editar"             => $editar,
          "GrupoPessoa"        => $GrupoPessoa,
          "Grupo"              => $retG["Grupo"] ?? array(),
          "GrupoPessoaInfoGrp" => $GrupoPessoaInfoGrp,
          "htmlPeso"           => $htmlPeso
        ));
      }
    }
  }

  public function timeline($gruId)
  {
    require_once(APPPATH."/models/TbGrupo.php");
    $ret = pegaGrupo($gruId);
    if($ret["erro"]){
      geraNotificacao("Aviso!", $ret["msg"], "warning");
      redirect(BASE_URL . 'Grupo');
    } else {
      $Grupo      = $ret["Grupo"] ?? array();
      $vGruPesId  = $Grupo["gru_pes_id"] ?? "";
      $vUsuLogado = pegaUsuarioLogadoId();

      if($vGruPesId != $vUsuLogado){
        geraNotificacao("Aviso!", "Esse grupo não pertence a você!", "warning");
        redirect(BASE_URL . 'Grupo');
      } else {
        require_once(APPPATH."/models/TbGrupoPessoa.php");
        $retGP = pegaGrupoPessoaPesGru($vUsuLogado, $gruId);

        if($retGP["erro"]){
          geraNotificacao("Aviso!", $retGP["msg"], "warning");
          redirect(BASE_URL . 'Grupo');
        } else {
          $GrupoPessoa        = $retGP["GrupoPessoa"] ?? array();
          $_SESSION["grp_id"] = $GrupoPessoa["grp_id"] ?? "";
          $_SESSION["foto"]   = $GrupoPessoa["pes_foto"] ?? NULL;

          require_once(APPPATH."/models/TbGrupoTimeline.php");
          geraHtmlViewGrupoTimeline($GrupoPessoa);
        }
      }
    }
  }

  public function indexInfo($grpId, $programado=0)
  {
    $vGrpId = $grpId ?? "";

    require_once(APPPATH."/models/TbGrupoPessoa.php");
    $retGP       = pegaGrupoPessoa($vGrpId);
    $GrupoPessoa = (!$retGP["erro"] && isset($retGP["GrupoPessoa"])) ? $retGP["GrupoPessoa"]: array();
    $vGruPesId   = $GrupoPessoa["gru_pes_id"] ?? "";
    $vUsuLogado  = pegaUsuarioLogadoId();

    $vGrpLogado  = pegaGrupoPessoaLogadoId();
    $ehGrp       = ($grpId == $vGrpLogado);
    $ehStaff     = ehStaffGrupo($vGrpLogado);
    if(!$ehStaff || !$ehGrp){
      $programado = 0;
    }

    if($vGruPesId != $vUsuLogado){
      geraNotificacao("Aviso!", "Esse grupo não pertence a você!", "warning");
      redirect(BASE_URL . 'Grupo');
    } else {
      require_once(APPPATH."/models/TbGrupoTimeline.php");
      geraHtmlViewGrupoTimeline($GrupoPessoa, array(
        "postagem_propria"  => true,
        "apenas_programado" => ($programado == 1)
      ));
    }
  }

  public function favoritos($grpId)
  {
    $vGrpId = $grpId ?? "";

    require_once(APPPATH."/models/TbGrupoPessoa.php");
    $retGP       = pegaGrupoPessoa($vGrpId);
    $GrupoPessoa = (!$retGP["erro"] && isset($retGP["GrupoPessoa"])) ? $retGP["GrupoPessoa"]: array();
    $vGruPesId   = $GrupoPessoa["gru_pes_id"] ?? "";
    $vUsuLogado  = pegaUsuarioLogadoId();

    if($vGruPesId != $vUsuLogado){
      geraNotificacao("Aviso!", "Esse grupo não pertence a você!", "warning");
      redirect(BASE_URL . 'Grupo');
    } else {
      require_once(APPPATH."/models/TbGrupoTimeline.php");
      geraHtmlViewGrupoTimeline($GrupoPessoa, array(
        "postagem_propria" => false,
        "apenas_favoritos" => true,
      ));
    }
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
      redirect(BASE_URL . 'Grupo');
      return;
    }

    require_once(APPPATH."/models/TbGrupoTimeline.php");
    geraHtmlViewGrupoTimeline($GrupoPessoa, array(
      "apenas_privado" => true
    ));
  }
}