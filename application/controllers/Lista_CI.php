<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lista_CI extends CI_Controller
{

  public function __construct()
  {
    CI_Controller::__construct();
    $this->load->helper("utils_helper");
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

      echo $Lista_CI->getHtmlTable();
    }
  }
}