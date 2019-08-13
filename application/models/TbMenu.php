<?php
require_once(APPPATH."/helpers/utils_helper.php");

function geraArrMenuUsuario($id, $admin=false, $grupo=false)
{
  //@todo deixei o ID sem usar pra se for fazer permissoes por user
  $CI = pega_instancia();
  $CI->load->database();

  $arrMenu = [];

  $CI->db->select('men_id, men_descricao, men_icone, men_controller, men_action');
  $CI->db->from('tb_menu');
  $CI->db->where('men_ativo =', 1);
  if($grupo){
    $CI->db->where('men_grupo =', 1);
    $CI->db->where('men_admin =', 0);
  } else {
    $CI->db->where('men_admin =', ($admin) ? 1: 0);
    $CI->db->where('men_grupo =', 0);
  }
  $CI->db->order_by('men_descricao', 'ASC');

  $query = $CI->db->get();
  foreach ($query->result() as $row){
    $arrMenu[] = array(
      "id"         => $row->men_id,
      "descricao"  => $row->men_descricao,
      "icone"      => $row->men_icone,
      "controller" => $row->men_controller,
      "action"     => $row->men_action,
    );
  }

  return $arrMenu;
}