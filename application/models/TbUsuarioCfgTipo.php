<?php
require_once(APPPATH."/helpers/utils_helper.php");

function pegaUsuCfgTipo($uctId)
{
  $arrRetorno = [];
  $arrRetorno["erro"]           = false;
  $arrRetorno["msg"]            = "";
  $arrRetorno["UsuarioCfgTipo"] = [];

  if(!is_numeric($uctId)){
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "ID inválido para buscar Tipo de Configuração!";

    return $arrRetorno;
  }

  $CI = pega_instancia();
  $CI->load->database();

  $CI->db->select('uct_id, uct_descricao, uct_ativo');
  $CI->db->from('tb_usuario_cfg_tipo');
  $CI->db->where('uct_id =', $uctId);

  $query = $CI->db->get();
  $row   = $query->row();

  if (!isset($row)) {
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "Erro ao encontrar Tipo de Configuração!";

    return $arrRetorno;
  }

  $UsuarioCfgTipo     = [];
  $UsuarioCfgTipo["uct_id"]        = $row->uct_id;
  $UsuarioCfgTipo["uct_descricao"] = $row->uct_descricao;
  $UsuarioCfgTipo["uct_ativo"]     = $row->uct_ativo;

  $arrRetorno["msg"]               = "Tipo de Configuração encontrado com sucesso!";
  $arrRetorno["UsuarioCfgTipo"]    = $UsuarioCfgTipo;
  return $arrRetorno;
}

function pegaTodasUsuCfgTipo($filtro)
{
  $arrRetorno = [];
  $arrRetorno["erro"]              = false;
  $arrRetorno["msg"]               = "";
  $arrRetorno["arrUsuarioCfgTipo"] = [];

  $uctAtivo = $filtro["uct_ativo"] ?? "";

  $CI = pega_instancia();
  $CI->load->database();

  $CI->db->select('uct_id, uct_descricao, uct_ativo');
  $CI->db->from('tb_usuario_cfg_tipo');
  if($uctAtivo != ""){
    $CI->db->where('uct_ativo =', $uctAtivo);
  }
  $CI->db->order_by('uct_descricao', 'ASC');

  $query = $CI->db->get();
  if(!$query){
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "Erro ao retornar todos os Tipos de Configuração!";
  } else {
    foreach ($query->result() as $row){
      $UsuarioCfgTipo = [];
      $UsuarioCfgTipo["uct_id"]        = $row->uct_id;
      $UsuarioCfgTipo["uct_descricao"] = $row->uct_descricao;
      $UsuarioCfgTipo["uct_ativo"]     = $row->uct_ativo;

      $arrRetorno["arrUsuarioCfgTipo"][] = $UsuarioCfgTipo;
    }
  }

  return $arrRetorno;
}

function pegaListaUsuCfgTipo($detalhes=false, $edicao=false, $exclusao=false)
{
  $CI = pega_instancia();
  $CI->load->database();

  require_once(FCPATH."/assets/Lista_CI/Lista_CI.php");
  $Lista_CI = new Lista_CII($CI->db);
  $Lista_CI->setId("ListaUsuCfgTipo");
  $Lista_CI->addField("uct_id AS id");
  $Lista_CI->addField("uct_descricao AS \"Descrição\"", "L");
  $Lista_CI->addField("ativo AS \"Ativo\"");
  if($detalhes){
    $url = base_url() . "UsuarioCfgTipo/visualizar";
    $Lista_CI->addField(" CONCAT('<a href=\"$url/', uct_id, '\"><i class=\"material-icons text-success\">visibility</i></a>') AS \"Visualizar\" ", "C", "3%");
  }
  if($edicao){
    $url = base_url() . "UsuarioCfgTipo/editar";
    $Lista_CI->addField(" CONCAT('<a href=\"$url/', uct_id, '\"><i class=\"material-icons text-success\">create</i></a>') AS \"Editar\" ", "C", "3%");
  }
  if($exclusao){
    //delete
    //block
  }
  $Lista_CI->addFrom("v_tb_usuario_cfg_tipo");
  $Lista_CI->changeOrderCol(2);

  $Lista_CI->addFilter("uct_id", "id", "numeric");
  $Lista_CI->addFilter("uct_descricao", "Descrição");
  $Lista_CI->addFilter("ativo", "Ativo");

  return $Lista_CI->getHtml();
}

function validaInsereUsuCfgTipo($UsuarioCfgTipo)
{
  $strValida = "";

  $vDescricao = $UsuarioCfgTipo["uct_descricao"] ?? "";
  if(strlen($vDescricao) <= 2){
    $strValida .= "<br />&nbsp;&nbsp;* Informe uma descrição válida (entre 3 e 80 caracteres).";
  }
  
  $vAtivo = $UsuarioCfgTipo["uct_ativo"] ?? "";
  if(!($vAtivo == 0 || $vAtivo == 1)){
    $strValida .= "<br />&nbsp;&nbsp;* Informação 'ativo' é inválida.";
  }
  
  if($strValida != ""){
    $strValida  = "Corrija essas informações antes de prosseguir:<br />$strValida";
  }
  
  return $strValida;
}

function validaEditaUsuCfgTipo($UsuarioCfgTipo)
{
  $strValida = "";

  $vId = $UsuarioCfgTipo["uct_id"] ?? "";
  if(!is_numeric($vId)){
    $strValida .= "<br />&nbsp;&nbsp;* Informe um ID válido.";
  }

  $vDescricao = $UsuarioCfgTipo["uct_descricao"] ?? "";
  if(strlen($vDescricao) <= 2){
    $strValida .= "<br />&nbsp;&nbsp;* Informe uma descrição válida (entre 3 e 80 caracteres).";
  }

  $vAtivo = $UsuarioCfgTipo["uct_ativo"] ?? "";
  if(!($vAtivo == 0 || $vAtivo == 1)){
    $strValida .= "<br />&nbsp;&nbsp;* Informação 'ativo' é inválida.";
  }

  if($strValida != ""){
    $strValida  = "Corrija essas informações antes de prosseguir:<br />$strValida";
  }

  return $strValida;
}

function insereUsuCfgTipo($UsuarioCfgTipo)
{
  $arrRetorno         = [];
  $arrRetorno["erro"] = false;
  $arrRetorno["msg"]  = "";

  $strValida = validaInsereUsuCfgTipo($UsuarioCfgTipo);
  if($strValida != ""){
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = $strValida;

    return $arrRetorno;
  }

  $vDescricao = $UsuarioCfgTipo["uct_descricao"] ?? "";
  $vAtivo     = $UsuarioCfgTipo["uct_ativo"] ?? 1;

  $CI = pega_instancia();
  $CI->load->database();

  $data = array(
    "uct_descricao" => $vDescricao,
    "uct_ativo"     => $vAtivo,
  );
  $ret = $CI->db->insert('tb_usuario_cfg_tipo', $data);

  if(!$ret){
    $error = $CI->db->error();

    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "Erro ao inserir Tipo de Configuração. Mensagem: " . $error["message"];
  } else {
    $arrRetorno["erro"] = false;
    $arrRetorno["msg"]  = "Tipo de Configuração inserido com sucesso.";
  }

  return $arrRetorno;
}

function editaUsuCfgTipo($UsuarioCfgTipo)
{
  $arrRetorno         = [];
  $arrRetorno["erro"] = false;
  $arrRetorno["msg"]  = "";

  $strValida = validaEditaUsuCfgTipo($UsuarioCfgTipo);
  if($strValida != ""){
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = $strValida;

    return $arrRetorno;
  }

  $vId        = $UsuarioCfgTipo["uct_id"] ?? "";
  $vDescricao = $UsuarioCfgTipo["uct_descricao"] ?? "";
  $vAtivo     = (int) $UsuarioCfgTipo["uct_ativo"] ?? 1;

  $CI = pega_instancia();
  $CI->load->database();

  $data = array(
    "uct_descricao" => $vDescricao,
    "uct_ativo"     => $vAtivo,
  );
  $CI->db->where('uct_id', $vId);
  $ret = $CI->db->update('tb_usuario_cfg_tipo', $data);

  if(!$ret){
    $error = $CI->db->error();

    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "Erro ao editar Tipo de Configuração. Mensagem: " . $error["message"];
  } else {
    $arrRetorno["erro"] = false;
    $arrRetorno["msg"]  = "Tipo de Configuração editado com sucesso.";
  }

  return $arrRetorno;
}