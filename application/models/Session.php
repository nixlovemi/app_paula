<?php
require_once(APPPATH."/helpers/utils_helper.php");

function getArrSession()
{
  return $arrSession = array(
    "template_menu" => array(),
    "usuario_info"  => "",
    "usuario_grps"  => array(),
    "grp_id"        => "",
  );
}

function salva_session($arrSession)
{
  $CI = pega_instancia();

  foreach($arrSession as $key => $value){
    $CI->session->set_userdata($key, $value);
  }
}

function inicia_session($template_menu=array(), $usuario_info="", $cliente=false, $grpId="")
{
  $arrSession = getArrSession();

  $arrSession["template_menu"] = $template_menu;
  $arrSession["usuario_info"]  = $usuario_info;
  if($cliente){
    $arrSession["grp_id"] = $grpId;
  }

  // pra checar session
  $arrSession["credenciais"] = rand (51, 99);

  salva_session($arrSession);
}

function inicia_usuario_grps()
{
  $Usuario = $_SESSION["usuario_info"] ?? null;
  $vUsuid  = $Usuario->id ?? null;

  $CI = pega_instancia();
  $CI->load->database();

  #@todo talvez passar isso pra um model
  $CI->db->select('grp_id, grp_gru_id, pes_foto');
  $CI->db->from('v_tb_grupo_pessoa');
  $CI->db->where('grp_usu_id = ', $vUsuid);
  $query = $CI->db->get();

  if($query){
    foreach ($query->result() as $row) {
      if (isset($row)) {
        $_SESSION["usuario_grps"][$row->grp_gru_id] = $row->grp_id;
        $_SESSION["foto"]                           = $row->pes_foto;
      }
    }
  }
}

function fecha_session()
{
  $arrSession = getArrSession();
  salva_session($arrSession);
  session_destroy();
}