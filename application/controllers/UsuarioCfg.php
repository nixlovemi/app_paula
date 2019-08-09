<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UsuarioCfg extends MY_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->helper("utils_helper");
  }

  public function jsonAdd()
  {
    $variaveisPost  = processaPost();
    $vUscUsuId = $variaveisPost->usuId ?? "";
    $vUscUctId = $variaveisPost->cfgId ?? "";
    $vUscValor = $variaveisPost->valor ?? "";

    $UsuarioCfg = [];
    $UsuarioCfg["usc_usu_id"] = $vUscUsuId;
    $UsuarioCfg["usc_uct_id"] = $vUscUctId;
    $UsuarioCfg["usc_valor"]  = $vUscValor;

    require_once(APPPATH."/models/TbUsuarioCfg.php");
    $ret = insereUsuarioCfg($UsuarioCfg);

    $arrRet = [];
    if($ret["erro"]){
      $arrRet["msg"]        = $ret["msg"];
      $arrRet["msg_titulo"] = "Aviso!";
      $arrRet["msg_tipo"]   = "warning";
    } else {
      $htmlLista = pegaListaUsuarioCfg($vUscUsuId, false, false, true);

      $arrRet["html"]          = $htmlLista;
      $arrRet["html_selector"] = "#spnListaUsuarioConfig";

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

    require_once(APPPATH."/models/TbUsuarioCfg.php");
    $ret = deletaUsuarioCfg($id);

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