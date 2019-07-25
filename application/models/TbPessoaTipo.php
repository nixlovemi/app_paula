<?php
function pegaTodasPessoaTipo($filtro)
{
  $arrRetorno = [];
  $arrRetorno["erro"]          = false;
  $arrRetorno["msg"]           = "";
  $arrRetorno["arrPessoaTipo"] = [];

  $uctAtivo = $filtro["pet_ativo"] ?? "";

  $CI = pega_instancia();
  $CI->load->database();

  $CI->db->select('pet_id, pet_descricao, pet_cliente, pet_ativo');
  $CI->db->from('tb_pessoa_tipo');
  if($uctAtivo != ""){
    $CI->db->where('pet_ativo =', $uctAtivo);
  }
  $CI->db->order_by('pet_descricao', 'ASC');

  $query = $CI->db->get();
  if(!$query){
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "Erro ao retornar todos os Tipos de Pessoa!";
  } else {
    foreach ($query->result() as $row){
      $PessoaTipo = [];
      $PessoaTipo["pet_id"]        = $row->pet_id;
      $PessoaTipo["pet_descricao"] = $row->pet_descricao;
      $PessoaTipo["pet_cliente"]   = $row->pet_cliente;
      $PessoaTipo["pet_ativo"]     = $row->pet_ativo;

      $arrRetorno["arrPessoaTipo"][] = $PessoaTipo;
    }
  }

  return $arrRetorno;
}