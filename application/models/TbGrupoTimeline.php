<?php
function validaInsereGrupoTimeline($GrupoTimeline)
{
  require_once(APPPATH."/helpers/utils_helper.php");
  $strValida = "";

  # grt_id 	[grt_gru_id] 	[grt_grp_id] 	[grt_data] 	grt_titulo 	[grt_texto] 	[grt_publico] 	grt_ativo 	[grt_resposta_id]

  // validacao basica dos campos
  $vGruId = $GrupoTimeline["grt_gru_id"] ?? "";
  if(!is_numeric($vGruId)){
    $strValida .= "<br />&nbsp;&nbsp;* Informação 'ID do Grupo' é inválida.";
  }

  $vGrpId = $GrupoTimeline["grt_grp_id"] ?? "";
  if(!is_numeric($vGrpId)){
    $strValida .= "<br />&nbsp;&nbsp;* Informação 'ID do Participante' é inválida.";
  }

  $vData = $GrupoTimeline["grt_data"] ?? "";
  if(!isValidDate($vData)){
    $strValida .= "<br />&nbsp;&nbsp;* Informe uma data válida.";
  }

  $vTexto = $GrupoTimeline["grt_texto"] ?? "";
  if(strlen($vTexto) < 2){
    $strValida .= "<br />&nbsp;&nbsp;* Informação um texto com pelo menos 3 caracteres.";
  }

  $vPublico = $GrupoTimeline["grt_publico"] ?? "";
  if(!($vPublico == 0 || $vPublico == 1)){
    $strValida .= "<br />&nbsp;&nbsp;* Informação 'público' é inválida.";
  }

  $vRespId = $GrupoTimeline["grt_resposta_id"] ?? NULL;
  if($vRespId != NULL && $vRespId < 0){
    $strValida .= "<br />&nbsp;&nbsp;* Informação 'ID do Resposta' é inválida.";
  }
  // ===========================

  // valida grupo
  require_once(APPPATH."/models/TbGrupo.php");
  $retGru = pegaGrupo($vGruId);
  if($retGru["erro"]){
    $strValida .= "<br />&nbsp;&nbsp;* " . $retGru["msg"];
  } else {
    $Grupo     = $retGru["Grupo"] ?? array();
    $vGruAtivo = $Grupo["gru_ativo"] ?? 0;

    if($vGruAtivo <> 1){
      $strValida .= "<br />&nbsp;&nbsp;* Este grupo está inativo e não pode receber postagens.";
    }
  }
  // ============

  // valida grupo pessoa
  require_once(APPPATH."/models/TbGrupoPessoa.php");
  $retGP = pegaGrupoPessoa($vGrpId);
  if($retGP["erro"]){
    $strValida .= "<br />&nbsp;&nbsp;* " . $retGP["msg"];
  } else {
    $GrupoPessoa = $retGP["GrupoPessoa"] ?? array();
    $vGrpAtivo   = $GrupoPessoa["grp_ativo"] ?? 0;

    if($vGrpAtivo <> 1){
      $strValida .= "<br />&nbsp;&nbsp;* Você está inativo e não pode postar nesse grupo.";
    }
  }
  // ===================

  if($strValida != ""){
    $strValida  = "Corrija essas informações antes de prosseguir:<br />$strValida";
  }

  return $strValida;
}

function insereGrupoTimeline($GrupoTimeline)
{
  $arrRetorno          = [];
  $arrRetorno["erro"]  = false;
  $arrRetorno["msg"]   = "";
  $arrRetorno["grtId"] = "";

  $strValida = validaInsereGrupoTimeline($GrupoTimeline);
  if($strValida != ""){
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = $strValida;

    return $arrRetorno;
  }

  # grt_id 	[grt_gru_id] 	[grt_grp_id] 	[grt_data] 	[grt_titulo] 	[grt_texto] 	[grt_publico] 	[grt_ativo] 	[grt_resposta_id]

  $vGruId   = $GrupoTimeline["grt_gru_id"] ?? NULL;
  $vGrpId   = $GrupoTimeline["grt_grp_id"] ?? NULL;
  $vData    = $GrupoTimeline["grt_data"] ?? NULL;
  $vTitulo  = $GrupoTimeline["grt_titulo"] ?? NULL;
  $vTexto   = $GrupoTimeline["grt_texto"] ?? NULL;
  $vPublico = $GrupoTimeline["grt_publico"] ?? 1;
  $vAtivo   = $GrupoTimeline["grt_ativo"] ?? 1;
  $vRespId  = $GrupoTimeline["grt_resposta_id"] ?? NULL;

  $CI = pega_instancia();
  $CI->load->database();

  $data = array(
    "grt_gru_id"      => $vGruId,
    "grt_grp_id"      => $vGrpId,
    "grt_data"        => $vData,
    "grt_titulo"      => $vTitulo,
    "grt_texto"       => $vTexto,
    "grt_publico"     => $vPublico,
    "grt_ativo"       => $vAtivo,
    "grt_resposta_id" => $vRespId,
  );
  $ret = $CI->db->insert('tb_grupo_timeline', $data);

  if(!$ret){
    $error = $CI->db->error();

    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "Erro ao inserir Postagem. Mensagem: " . $error["message"];
  } else {
    $arrRetorno["erro"]  = false;
    $arrRetorno["msg"]   = "Postagem inserida com sucesso.";
    $arrRetorno["grtId"] = $CI->db->insert_id();
  }

  return $arrRetorno;
}

function pegaPostagensGrupo($gruId, $limit=50, $offset=0)
{
  $arrRetorno              = [];
  $arrRetorno["erro"]      = false;
  $arrRetorno["msg"]       = "";
  $arrRetorno["postagens"] = [];
  $arrRetorno["limit"]     = $limit;
  $arrRetorno["offset"]    = $offset;

  // validacoes
  if(!is_numeric($gruId)){
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "ID inválido para buscar postagens!";
    return $arrRetorno;
  }

  require_once(APPPATH."/models/TbGrupo.php");
  $retGrp = pegaGrupo($gruId);
  if($retGrp["erro"]){
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = $retGrp["msg"];
    return $arrRetorno;
  } else {
    $Grupo     = $retGrp["Grupo"] ?? array();
    $vGruAtivo = $Grupo["gru_ativo"] ?? 0;
    if($vGruAtivo <> 1){
      $arrRetorno["erro"] = true;
      $arrRetorno["msg"]  = "Esse grupo está inativo.";
      return $arrRetorno;
    }
  }
  // ==========

  $CI = pega_instancia();
  $CI->load->database();

  $CI->db->select('*');
  $CI->db->from('v_tb_grupo_timeline');
  $CI->db->where('grt_gru_id =', $gruId);
  $CI->db->where('grt_publico =', 1);
  $CI->db->where('grt_ativo =', 1);
  $CI->db->where('grt_resposta_id IS NULL');
  $CI->db->where('pet_cliente =', 1);
  $CI->db->order_by('grt_data', 'DESC');
  $CI->db->limit($limit, $offset);
  $query = $CI->db->get();

  if(!$query){
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "Erro ao encontrar carregar postagens desse grupo!";

    return $arrRetorno;
  }

  foreach ($query->result() as $row) {
    if (isset($row)) {
      $arrRetorno["postagens"][] = (array) $row;
    }
  }

  return $arrRetorno;
}