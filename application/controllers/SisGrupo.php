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

    require_once(APPPATH."/models/TbGrupoTimelineArquivos.php");
    $retGTA      = pegaArquivos($arrPostagens);
    $arrArquivos = ($retGTA["erro"]) ? array(): $retGTA["arquivos"];

    $this->template->load(TEMPLATE_STR, 'SisGrupo/index', array(
      "titulo"        => gera_titulo_template("Área Inicial"),
      "GrupoTimeline" => $GrupoTimeline,
      "arrPostagens"  => $arrPostagens,
      "arrArquivos"   => $arrArquivos,
      "vGruDescricao" => $vGruDesc,
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
}