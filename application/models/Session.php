<?php
require_once(APPPATH."/helpers/utils_helper.php");

function getArrSession()
{
  return $arrSession = array(
    "template_menu" => array(),
    "usuario_info"  => "",
    "grp_id"        => "",
    "foto"          => "",
    "ehLoginGrupo"  => false,
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
  $arrSession["foto"] = $usuario_info->foto;

  // pra checar session
  $arrSession["credenciais"] = rand (51, 99);

  salva_session($arrSession);
}

function fecha_session()
{
  $arrSession = getArrSession();
  salva_session($arrSession);
  session_destroy();
}