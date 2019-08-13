<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Json extends CI_Controller
{
  public function __construct()
  {
    CI_Controller::__construct();
    $this->load->helper("utils_helper");
  }

  public function jsonPegaViewAddGpi()
  {
    $arrRet        = [];
    $variaveisPost = processaPost();

    $vPesId = $variaveisPost->pessoa ?? "";
    $vGruId = $variaveisPost->grupo ?? "";

    require_once(APPPATH."/models/TbGrupoPessoaInfo.php");
    $retGPI = pegaGrupoPessoaInfoPesGru($vPesId, $vGruId);
    if($retGPI["erro"]){
      $arrRet["msg"]        = $retGPI["msg"];
      $arrRet["msg_titulo"] = "Aviso!";
      $arrRet["msg_tipo"]   = "warning";
    } else {
      $GrupoPessoaInfo    = $retGPI["GrupoPessoaInfo"] ?? array();
      $retGrp             = agrupaGrupoPessoaInfoLancamentos($GrupoPessoaInfo);
      $GrupoPessoaInfoGrp = $retGrp["GrupoPessoaInfoGrp"] ?? array();

      $ehPrimeira         = count($GrupoPessoaInfoGrp["primeira"]) <= 0;
      $htmlView           = $this->load->view('TbGrupoPessoaInfo/novo', array(
        "titulo"          => gera_titulo_template("Informação do Participante - Novo"),
        "ehPrimeira"      => $ehPrimeira,
        "pesId"           => $vPesId,
        "gruId"           => $vGruId,
      ), true);

      $htmlAjustado  = processaJsonHtml($htmlView);
      $arrRet["callback"] = "jsonShowAddGrupoPessoaInfo('$htmlAjustado')";
    }

    echo json_encode($arrRet);
  }

  public function jsonPostAddGpi()
  {
    $arrRet        = [];
    $variaveisPost = processaPost();

    $vData     = $variaveisPost->data ?? "";
    $vAltura   = $variaveisPost->altura_cm ?? NULL;
    $vPeso     = $variaveisPost->peso_kg ?? "";
    $vPesoObj  = $variaveisPost->peso_kg_obj ?? NULL;
    $vPrimeira = $variaveisPost->primeira ?? true;
    $vPesId    = $variaveisPost->pessoa ?? "";
    $vGruId    = $variaveisPost->grupo ?? "";

    // valida grupo pessoa
    require_once(APPPATH."/models/TbGrupoPessoa.php");
    $retGP = pegaGrupoPessoaPesGru($vPesId, $vGruId);
    if($retGP["erro"]){
      $arrRet["msg"]        = $retGP["msg"];
      $arrRet["msg_titulo"] = "Aviso!";
      $arrRet["msg_tipo"]   = "warning";
    } else {
      $GrupoPessoa = $retGP["GrupoPessoa"] ?? array();
      $vGrpId      = $GrupoPessoa["grp_id"] ?? "";

      $GrupoPessoaInfo = [];
      $GrupoPessoaInfo["gpi_grp_id"]        = $vGrpId;
      $GrupoPessoaInfo["gpi_data"]          = acerta_data($vData);
      $GrupoPessoaInfo["gpi_altura"]        = $vAltura;
      $GrupoPessoaInfo["gpi_peso"]          = acerta_moeda($vPeso);
      $GrupoPessoaInfo["gpi_peso_objetivo"] = acerta_moeda($vPesoObj);
      $GrupoPessoaInfo["gpi_inicial"]       = (int)$vPrimeira;

      require_once(APPPATH."/models/TbGrupoPessoaInfo.php");
      $ret = insereGrupoPessoaInfo($GrupoPessoaInfo);
      if($ret["erro"]){
        $arrRet["msg"]        = $ret["msg"];
        $arrRet["msg_titulo"] = "Aviso!";
        $arrRet["msg_tipo"]   = "warning";

        $arrRet["callback"] = "jsonAddGrupoPessoaInfo($vPesId, $vGruId);";
      } else {
        geraNotificacao("Sucesso!", $ret["msg"], "success");
        $arrRet["callback"] = "document.location.href = document.location.href;";
      }
    }

    echo json_encode($arrRet);
  }
}