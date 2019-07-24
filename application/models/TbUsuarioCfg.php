<?php
function validaInsereUsuarioCfg($UsuarioCfg)
{
  $strValida = "";

  $vUsuId = $UsuarioCfg["usc_usu_id"] ?? "";
  if(!is_numeric($vUsuId)){
    $strValida .= "<br />&nbsp;&nbsp;* Informe um usuário válido.";
  }

  $vUctId = $UsuarioCfg["usc_uct_id"] ?? "";
  if(!is_numeric($vUctId)){
    $strValida .= "<br />&nbsp;&nbsp;* Informe uma configuração válida.";
  }

  $vValor = $UsuarioCfg["usc_valor"] ?? "";
  if(strlen(trim($vValor)) <= 0){
    $strValida .= "<br />&nbsp;&nbsp;* Informe um valor válido.";
  }

  if($strValida != ""){
    $strValida  = "Corrija essas informações antes de prosseguir:<br />$strValida";
  }

  return $strValida;
}

function insereUsuarioCfg($UsuarioCfg)
{
  $arrRetorno         = [];
  $arrRetorno["erro"] = false;
  $arrRetorno["msg"]  = "";

  $strValida = validaInsereUsuarioCfg($UsuarioCfg);
  if($strValida != ""){
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = $strValida;

    return $arrRetorno;
  }

  $vUsuId = $UsuarioCfg["usc_usu_id"] ?? "";
  $vUctId = $UsuarioCfg["usc_uct_id"] ?? "";
  $vValor = $UsuarioCfg["usc_valor"] ?? "";

  $CI = pega_instancia();
  $CI->load->database();

  $data = array(
    "usc_usu_id" => $vUsuId,
    "usc_uct_id" => $vUctId,
    "usc_valor"  => $vValor,
  );
  $ret = $CI->db->insert('tb_usuario_cfg', $data);

  if(!$ret){
    $error = $CI->db->error();

    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "Erro ao inserir Configuração do Usuário. Mensagem: " . $error["message"];
  } else {
    $arrRetorno["erro"] = false;
    $arrRetorno["msg"]  = "Configuração do Usuário inserida com sucesso.";
  }

  return $arrRetorno;
}

function deletaUsuarioCfg($uscId)
{
  $arrRetorno         = [];
  $arrRetorno["erro"] = false;
  $arrRetorno["msg"]  = "";

  if(!is_numeric($uscId)){
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "ID inválido para excluir Configuração de Usuário!";
  } else {
    $CI = pega_instancia();
    $CI->load->database();

    $CI->db->where('usc_id', $uscId);
    $CI->db->delete('tb_usuario_cfg');

    if($CI->db->affected_rows() <= 0){
      $arrRetorno["erro"] = true;
      $arrRetorno["msg"]  = "Erro ao excluir essa Configuração de Usuário!";
    } else {
      $arrRetorno["erro"] = false;
      $arrRetorno["msg"]  = "Configuração de Usuário excluída com sucesso!";
    }
  }

  return $arrRetorno;
}

function pegaListaUsuarioCfg($usuario="", $detalhes=false, $edicao=false, $exclusao=false)
{
  $CI = pega_instancia();
  $CI->load->database();

  # usc_id

  require_once(FCPATH."/assets/Lista_CI/Lista_CI.php");
  $Lista_CI = new Lista_CII($CI->db);
  $Lista_CI->setId("ListaUsuarioCfg");
  $Lista_CI->addField("usu_nome AS \"Usuário\"", "L");
  $Lista_CI->addField("uct_descricao AS \"Configuração\"", "L");
  $Lista_CI->addField("usc_valor AS \"Valor\"");
  if($detalhes){
  }
  if($edicao){
  }
  if($exclusao){
    $Lista_CI->addField("REPLACE('<a href=\"javascript:;\" onclick=\"confirm_delete(''ListaUsuarioCfg'', ''UsuarioCfg'', ''jsonDelete'', ''id={usc_id}'', ''".base_url()."'')\"><i class=\"material-icons text-success\">delete</i></a>', '{usc_id}', usc_id) AS \"Excluir\" ", "C", "3%");
  }
  $Lista_CI->addFrom("v_tb_usuario_cfg");
  if(is_numeric($usuario) && $usuario > 0){
    $Lista_CI->addWhere("usc_usu_id = $usuario");
  }
  $Lista_CI->changeOrderCol(2);

  $Lista_CI->addFilter("usu_nome", "Usuário");
  $Lista_CI->addFilter("uct_descricao", "Configuração");

  return $Lista_CI->getHtml();
}