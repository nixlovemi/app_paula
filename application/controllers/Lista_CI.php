<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lista_CI extends MY_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->helper("utils_helper");
  }

  private function executeFilter($arrInfo)
  {
    $json_lista_ci = $arrInfo["json_lista_ci"] ?? "";
    $filter        = $arrInfo["filter"] ?? "";
    $filter_val    = $arrInfo["filter_val"] ?? "";
    $changePage    = $arrInfo["changePage"] ?? 0;
    $orderBy       = $arrInfo["orderBy"] ?? "";

    $CI = pega_instancia();
    $CI->load->database();
    $Lista_CI = new Lista_CII($CI->db);
    $Lista_CI->configFromJsonStr($json_lista_ci);
    if($changePage > 0){
      $Lista_CI->changePage($changePage);
    }
    if($orderBy != ""){
      $Lista_CI->changeOrderCol($orderBy);
    }
    if($filter != "" && $filter_val != ""){
      $Lista_CI->filterPage($filter, $filter_val);
    } else if($changePage <= 0) {
      $Lista_CI->filterPage();
    }

    echo $Lista_CI->getHtmlTable();

    // esquema pra reload na lista qdo altera etc
    // template que controla uma parte; outra demo.js
    $arrRecListCi = $this->session->recarregaListaCi ?? array();
    $arrParamLst  = array(
      "json_lista_ci" => $json_lista_ci,
      "filter"        => $filter,
      "filter_val"    => $filter_val,
      "changePage"    => $changePage,
      "orderBy"       => $orderBy,
    );
    $jsonParamLst = json_encode($arrParamLst);
    $arrRecListCi[$Lista_CI->getId()] = $Lista_CI->base64url_encode($jsonParamLst);

    $this->session->set_tempdata('recarregaListaCi', $arrRecListCi, 300);
    // ==========================================
  }

  public function index()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      require_once(FCPATH . "assets/Lista_CI/Lista_CI.php");
      $variaveisPost = processaPost();

      $json_lista_ci = $variaveisPost->json_lista_ci ?? "";
      $filter        = $variaveisPost->filter ?? "";
      $filter_val    = $variaveisPost->filter_val ?? "";
      $changePage    = $variaveisPost->changePage ?? 0;
      $orderBy       = $variaveisPost->orderBy ?? "";

      $arrInfo       = array(
        "json_lista_ci" => $json_lista_ci,
        "filter"        => $filter,
        "filter_val"    => $filter_val,
        "changePage"    => $changePage,
        "orderBy"       => $orderBy,
      );
      $this->executeFilter($arrInfo);
    }
  }

  public function reload()
  {
    require_once(FCPATH . "assets/Lista_CI/Lista_CI.php");
    $variaveisPost = processaPost();
    $jsonParamLst  = $variaveisPost->jsonParamLst ?? "";

    $CI = pega_instancia();
    $CI->load->database();
    $Lista_CI = new Lista_CII($CI->db);

    $decodedJson  = $Lista_CI->base64url_decode($jsonParamLst);
    $arrInfo      = json_decode($decodedJson, true);

    if(!empty($arrInfo)){
      $this->executeFilter($arrInfo);
    }
  }
}