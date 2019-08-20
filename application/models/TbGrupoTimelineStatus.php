<?php
require_once(APPPATH."/helpers/utils_helper.php");

function pegaInfoStaffNotificacao($grpId)
{
  $arrRetorno = [];
  $arrRetorno["erro"]        = false;
  $arrRetorno["msg"]         = "";
  $arrRetorno["notificacao"] = "";

  require_once(APPPATH."/models/TbGrupoPessoa.php");
  $retGP = pegaGrupoPessoa($grpId);
  if($retGP["erro"]){
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = $retGP["msg"];
  } else {
    $GrupoPessoa = $retGP["GrupoPessoa"] ?? array();
    $vGruId      = $GrupoPessoa["grp_gru_id"] ?? "";

    $CI = pega_instancia();
    $CI->load->database();

    $CI->db->select('grt_grp_id, COUNT(*) AS cnt');
    $CI->db->from('v_tb_grupo_timeline');
    $CI->db->join('tb_grupo_timeline_status gts', 'gts.gts_grt_id = grt_id', 'left');
    $CI->db->where('gts_id IS NULL');
    $CI->db->where('grt_gru_id = ', $vGruId);
    $CI->db->where('grt_publico = ', 1);
    $CI->db->where('grt_ativo = ', 1);
    $CI->db->where('grt_resposta_id IS NULL');
    $CI->db->where('pet_descricao <> ', 'Cliente');
    $CI->db->group_by("grt_grp_id");

    $query = $CI->db->get();

    if(!$query){
      $arrRetorno["erro"] = true;
      $arrRetorno["msg"]  = "Erro ao pegar notificações!";
      return $arrRetorno;
    }

    foreach ($query->result() as $row) {
      if (isset($row)) {
        $vGrpId = $row->grt_grp_id;
        $vCount = $row->cnt;

        $arrRetorno["notificacao"][$vGrpId] = $vCount;
      }
    }

    return $arrRetorno;
  }
}