<?php

function pega_instancia()
{
  $CI = & get_instance();
  return $CI;
}

function encripta_string($string)
{
  $retString = md5(SALT_KEY.$string);
  return $retString;
}

function valida_email($email)
{
  $ret = filter_var($email, FILTER_VALIDATE_EMAIL);
  return $ret;
}

function array_ret_para_retorno($arrRet)
{
  return (object) $arrRet;
}

function processaPost()
{
  $postdata = file_get_contents("php://input");
  $jsonVars = [];

  if ($postdata != "") {
    parse_str($postdata, $jsonVars);
  } else {
    parse_str($_REQUEST, $jsonVars);
  }

  return (object) $jsonVars;
}

function gera_titulo_template($titulo, $href="javascript:;")
{
  return "<a class='navbar-brand' href='$href'>$titulo<div class='ripple-container'></div></a>";
}

/**
 * type = ['', 'info', 'danger', 'success', 'warning', 'rose', 'primary'];
*/
function geraNotificacao($titulo, $mensagem, $tipo)
{
  $CI = pega_instancia();
  $CI->session->set_flashdata('geraNotificacao', array("titulo"=>$titulo, "mensagem"=>$mensagem, "tipo"=>$tipo));
}