<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class GrupoTimelineSalvo extends MY_Controller {

  var $cliente_logado;

  public function __construct()
  {
    $admin = false;
    $grupo = true;
    parent::__construct($admin, $grupo);
    $this->load->helper("utils_helper");

    $this->cliente_logado = $this->session->usuario_info ?? array();
  }

  public function jsonFavoritar()
  {
    $variaveisPost = processaPost();
    $vGrtId = $variaveisPost->id ?? "";
    $vGrpId = pegaGrupoPessoaLogadoId() ?? "";
    if ($vGrpId == NULL) {
      require_once(APPPATH . "/models/TbGrupoTimeline.php");
      $retGrt = pegaGrupoTimeline($vGrtId);
      $GrupoTimeline = ($retGrt["erro"]) ? array() : $retGrt["GrupoTimeline"];
      $vGruId = $GrupoTimeline["grt_gru_id"] ?? "";
      $vGrpId = $_SESSION["usuario_grps"][$vGruId] ?? NULL;
    }

    $GrupoTimelineSalvo = [];
    $GrupoTimelineSalvo["gts_grt_id"] = $vGrtId;
    $GrupoTimelineSalvo["gts_grp_id"] = $vGrpId;

    require_once(APPPATH . "/models/TbGrupoTimelineSalvo.php");
    $retAdd = insereGrupoTimelineSalvo($GrupoTimelineSalvo);

    if ($retAdd["erro"]) {
      $arrRet["msg"] = $retAdd["msg"];
      $arrRet["msg_titulo"] = "Aviso!";
      $arrRet["msg_tipo"] = "warning";
    } else {
      $arrRet["msg"] = $retAdd["msg"];
      $arrRet["msg_titulo"] = "Sucesso!";
      $arrRet["msg_tipo"] = "success";
      $arrRet["callback"] = "jqueryMostraFavoritado($vGrtId)";
    }

    echo json_encode($arrRet);
  }

}
