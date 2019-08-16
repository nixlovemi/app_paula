<?php
require_once(APPPATH."/helpers/utils_helper.php");

function validaInsereGrupoTimelineSalvo($GrupoTimelineSalvo)
{
  require_once(APPPATH."/helpers/utils_helper.php");
  $strValida   = "";
  $idUsuLogado = pegaUsuarioLogadoId();

  // validacao basica dos campos
  $vGrtId = $GrupoTimelineSalvo["gts_grt_id"] ?? "";
  if(!is_numeric($vGrtId)){
    $strValida .= "<br />&nbsp;&nbsp;* Postagem inválida para favoritar.";
  }

  $vGrpId = $GrupoTimelineSalvo["gts_grp_id"] ?? "";
  if(!is_numeric($vGrpId)){
    $strValida .= "<br />&nbsp;&nbsp;* Pessoa inválida para favoritar postagem.";
  }
  // ===========================

  require_once(APPPATH."/models/TbGrupoPessoa.php");
  $retGrpes    = pegaGrupoPessoa($vGrpId);
  $GrupoPessoa = $retGrpes["GrupoPessoa"] ?? array();
  $vGruId      = $GrupoPessoa["grp_gru_id"] ?? "";
  $vPesId      = $GrupoPessoa["grp_pes_id"] ?? "";

  $idGruLogado = pegaGrupoLogadoId();
  $ehAdminGrp  = false;
  if($idGruLogado == NULL){
    $arrLoop = $_SESSION["usuario_grps"] ?? array();
    foreach($arrLoop as $lgGru => $lgGrp){
      if($lgGru == $vGruId){
        $idGruLogado = $lgGru;
        $ehAdminGrp  = true;
      }
    }
  }

  // vê se grupo condiz com a session
  if($idGruLogado != $vGruId){
      $strValida .= "<br />&nbsp;&nbsp;* Você não pode favoritar postagens de outros grupos!";
  }
  // ================================

  // valida grupo válido
  require_once(APPPATH."/models/TbGrupo.php");
  $retGrp = validaGrupo($vGruId, $idUsuLogado);
  if($retGrp["erro"]){
    $strValida .= "<br />&nbsp;&nbsp;* " . $retGrp["msg"];
  }
  // ===================

  // valida pessoa válida
  if(!$ehAdminGrp){
    require_once(APPPATH."/models/TbPessoa.php");
    $retPes = validaPessoa($vPesId, $idUsuLogado);
    if($retPes["erro"]){
      $strValida .= "<br />&nbsp;&nbsp;* " . $retPes["msg"];
    }
  }
  // ====================

  // valida postagem já favoritada
  $CI = pega_instancia();
  $CI->load->database();

  $CI->db->select('COUNT(*) AS cnt');
  $CI->db->from('tb_grupo_timeline_salvo');
  $CI->db->where('gts_grt_id =', $vGrtId);
  $CI->db->where('gts_grp_id =', $vGrpId);

  $query = $CI->db->get();
  $row   = $query->row();
  if (!isset($row) || $row->cnt > 0) {
    $strValida .= "<br />&nbsp;&nbsp;* Essa postagem já está nos seus favoritos.";
  }
  // =============================

  if($strValida != ""){
    $strValida  = "Corrija essas informações antes de prosseguir:<br />$strValida";
  }

  return $strValida;
}

function insereGrupoTimelineSalvo($GrupoTimelineSalvo)
{
  $arrRetorno          = [];
  $arrRetorno["erro"]  = false;
  $arrRetorno["msg"]   = "";
  $arrRetorno["gtsId"] = "";

  $strValida = validaInsereGrupoTimelineSalvo($GrupoTimelineSalvo);
  if($strValida != ""){
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = $strValida;

    return $arrRetorno;
  }

  $vGrtId = $GrupoTimelineSalvo["gts_grt_id"] ?? NULL;
  $vGrpId = $GrupoTimelineSalvo["gts_grp_id"] ?? NULL;

  $CI = pega_instancia();
  $CI->load->database();

  $data = array(
    "gts_grt_id" => $vGrtId,
    "gts_grp_id" => $vGrpId,
  );
  $ret = $CI->db->insert('tb_grupo_timeline_salvo', $data);

  if(!$ret){
    $error = $CI->db->error();

    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "Erro ao favoritar postagem. Mensagem: " . $error["message"];
  } else {
    $arrRetorno["erro"]  = false;
    $arrRetorno["msg"]   = "Postagem favoritada com sucesso.";
    $arrRetorno["gtsId"] = $CI->db->insert_id();
  }

  return $arrRetorno;
}