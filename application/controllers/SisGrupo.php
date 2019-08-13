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
    $GrupoTimeline = $this->session->flashdata('GrupoTimeline') ?? array();

    require_once(APPPATH."/models/TbGrupoTimeline.php");
    $vGrpId = $this->session->grp_id ?? NULL;

    require_once(APPPATH."/models/TbGrupoPessoa.php");
    $retGP       = pegaGrupoPessoa($vGrpId);
    $GrupoPessoa = (!$retGP["erro"] && isset($retGP["GrupoPessoa"])) ? $retGP["GrupoPessoa"]: array();
    $vGruId      = $GrupoPessoa["grp_gru_id"] ?? NULL;
    $vGruDesc    = $GrupoPessoa["gru_descricao"] ?? NULL;

    $retPost      = pegaPostagensGrupo($vGruId);
    $arrPostagens = (!$retPost["erro"] && isset($retPost["postagens"])) ? $retPost["postagens"]: array();
    $arrSalvos    = (!$retPost["erro"] && isset($retPost["salvos"])) ? $retPost["salvos"]: array();

    $retResp = pegaRespostasGrupoTimeline($arrPostagens);
    $arrResp = (!$retResp["erro"] && isset($retResp["respostas"])) ? $retResp["respostas"]: array();

    require_once(APPPATH."/models/TbGrupoTimelineArquivos.php");
    $retGTA      = pegaArquivos($arrPostagens);
    $arrArquivos = ($retGTA["erro"]) ? array(): $retGTA["arquivos"];
    
    $retGpss  = pegaGrupoPessoasGru($vGruId, true);
    $arrStaff = ($retGpss["erro"]) ? array(): $retGpss["GruposPessoas"];

    // view posts
    $htmlPosts = $this->load->view('TbGrupoTimeline/postagens', array(
      "vGruDescricao" => $vGruDesc,
      "arrPostagens"  => $arrPostagens,
      "arrSalvos"     => $arrSalvos,
      "arrArquivos"   => $arrArquivos,
      "arrResp"       => $arrResp,
    ), true);

    $this->template->load(TEMPLATE_STR, 'SisGrupo/index', array(
      "titulo"        => gera_titulo_template("Área Inicial"),
      "arrStaff"      => $arrStaff,
      "htmlPosts"     => $htmlPosts,
      "GrupoTimeline" => $GrupoTimeline,
    ));
  }

  public function jsonPegaHtmlAnexo()
  {
    $arrRet = [];

    $variaveisPost = processaPost();
    $vLinhaHtml    = $variaveisPost->linhaHtml ?? "";
    $vidForm       = $variaveisPost->idForm ?? "";
    $vidAnexo      = $variaveisPost->idAnexo ?? "";
    $vLinkYt       = $variaveisPost->linkYt ?? "";

    // valida se é link do YT
    if($vLinkYt != "" && !eh_link_youtube($vLinkYt)){
      $arrRet["msg"]        = "Informe um link válido do Youtube!";
      $arrRet["msg_titulo"] = "Alerta!";
      $arrRet["msg_tipo"]   = "warning";
    } else {
      $html = $this->load->view('SisGrupo/novoAnexo', array(
        "frmId"   => $vidForm,
        "idAnexo" => $vidAnexo,
        "linkYt"  => $vLinkYt,
      ), true);

      $arrRet["html"]          = $html;
      $arrRet["html_selector"] = $vLinhaHtml;
      $arrRet["html_append"]   = true;
      $arrRet["callback"]      = " $('$vidForm #anexo$vidAnexo').click(); ";
    }

    echo json_encode($arrRet);
  }

  public function indexInfo($grpId)
  {
    $vGrpId = $grpId ?? "";
    
    require_once(APPPATH."/models/TbGrupoPessoa.php");
    $retGP       = pegaGrupoPessoa($vGrpId);
    $GrupoPessoa = (!$retGP["erro"] && isset($retGP["GrupoPessoa"])) ? $retGP["GrupoPessoa"]: array();
    $vGruId      = $GrupoPessoa["grp_gru_id"] ?? "";
    $vGruLogado  = pegaGrupoLogadoId();
    $vGruDesc    = $GrupoPessoa["gru_descricao"] ?? NULL;

    // valida grupo logado
    if($vGruId != $vGruLogado){
      geraNotificacao("Aviso!", "Esse conteúdo não faz parte do seu grupo!", "warning");
      redirect(BASE_URL . 'SisGrupo');
      return;
    }

    require_once(APPPATH."/models/TbGrupoTimeline.php");
    $retPost = pegaPostagensGrupo($vGruId, $vGrpId);

    $arrPostagens = (!$retPost["erro"] && isset($retPost["postagens"])) ? $retPost["postagens"]: array();
    $arrSalvos    = (!$retPost["erro"] && isset($retPost["salvos"])) ? $retPost["salvos"]: array();

    require_once(APPPATH."/models/TbGrupoTimelineArquivos.php");
    $retGTA      = pegaArquivos($arrPostagens);
    $arrArquivos = ($retGTA["erro"]) ? array(): $retGTA["arquivos"];

    $retGpss  = pegaGrupoPessoasGru($vGruId, true);
    $arrStaff = ($retGpss["erro"]) ? array(): $retGpss["GruposPessoas"];

    // view posts
    $htmlPosts = $this->load->view('TbGrupoTimeline/postagens', array(
      "vGruDescricao" => $vGruDesc,
      "arrPostagens"  => $arrPostagens,
      "arrSalvos"     => $arrSalvos,
      "arrArquivos"   => $arrArquivos,
      "vPesNome"      => $GrupoPessoa["pes_nome"] ?? "",
    ), true);

    $this->template->load(TEMPLATE_STR, 'SisGrupo/indexInfo', array(
      "titulo"    => gera_titulo_template("Área Inicial"),
      "arrStaff"  => $arrStaff,
      "htmlPosts" => $htmlPosts,
    ));
  }
}