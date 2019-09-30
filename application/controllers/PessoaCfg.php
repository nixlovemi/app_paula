<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PessoaCfg extends MY_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->helper("utils_helper");
  }

  public function jsonAdd()
  {
    $variaveisPost  = processaPost();
    $vUscPesId = $variaveisPost->pesId ?? "";
    $vUscUctId = $variaveisPost->cfgId ?? "";
    $vUscValor = $variaveisPost->valor ?? "";

    $PessoaCfg = [];
    $PessoaCfg["psc_pes_id"] = $vUscPesId;
    $PessoaCfg["psc_pct_id"] = $vUscUctId;
    $PessoaCfg["psc_valor"]  = $vUscValor;

    require_once(APPPATH."/models/TbPessoaCfg.php");
    $ret = inserePessoaCfg($PessoaCfg);

    $arrRet = [];
    if($ret["erro"]){
      $arrRet["msg"]        = $ret["msg"];
      $arrRet["msg_titulo"] = "Aviso!";
      $arrRet["msg_tipo"]   = "warning";
    } else {
      $htmlLista = pegaListaPessoaCfg($vUscPesId, false, false, true);

      $arrRet["html"]          = $htmlLista;
      $arrRet["html_selector"] = "#spnListaClienteConfig";

      $arrRet["msg"]        = $ret["msg"];
      $arrRet["msg_titulo"] = "Sucesso!";
      $arrRet["msg_tipo"]   = "success";
    }

    echo json_encode($arrRet);
  }

  public function jsonDelete()
  {
    $variaveisPost = processaPost();
    $id        = $variaveisPost->id ?? "";
    $listaCiId = $variaveisPost->lista_ci_id ?? "";

    require_once(APPPATH."/models/TbPessoaCfg.php");
    $ret = deletaPessoaCfg($id);

    $arrRet = [];
    if($ret["erro"]){
      $arrRet["msg"]        = $ret["msg"];
      $arrRet["msg_titulo"] = "Aviso!";
      $arrRet["msg_tipo"]   = "warning";
    } else {
      $arrRet["msg"]        = $ret["msg"];
      $arrRet["msg_titulo"] = "Sucesso!";
      $arrRet["msg_tipo"]   = "success";

      if($listaCiId != ""){
        $arrRet["callback"] = "reload_list('$listaCiId');";
      }
    }

    echo json_encode($arrRet);
  }
}