<?php
require_once(APPPATH."/helpers/utils_helper.php");

function pegaPesCfgTipo($pctId)
{
  $arrRetorno = [];
  $arrRetorno["erro"]          = false;
  $arrRetorno["msg"]           = "";
  $arrRetorno["PessoaCfgTipo"] = [];

  if(!is_numeric($pctId)){
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "ID inválido para buscar Tipo de Configuração!";

    return $arrRetorno;
  }

  $CI = pega_instancia();
  $CI->load->database();

  $CI->db->select('pct_id, pct_descricao, pct_ativo');
  $CI->db->from('tb_pessoa_cfg_tipo');
  $CI->db->where('pct_id =', $pctId);

  $query = $CI->db->get();
  $row   = $query->row();

  if (!isset($row)) {
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "Erro ao encontrar Tipo de Configuração!";

    return $arrRetorno;
  }

  $PessoaCfgTipo     = [];
  $PessoaCfgTipo["pct_id"]        = $row->pct_id;
  $PessoaCfgTipo["pct_descricao"] = $row->pct_descricao;
  $PessoaCfgTipo["pct_ativo"]     = $row->pct_ativo;

  $arrRetorno["msg"]           = "Tipo de Configuração encontrado com sucesso!";
  $arrRetorno["PessoaCfgTipo"] = $PessoaCfgTipo;
  return $arrRetorno;
}

function pegaTodasPesCfgTipo($filtro)
{
  $arrRetorno = [];
  $arrRetorno["erro"]             = false;
  $arrRetorno["msg"]              = "";
  $arrRetorno["arrPessoaCfgTipo"] = [];

  $pctAtivo = $filtro["pct_ativo"] ?? "";

  $CI = pega_instancia();
  $CI->load->database();

  $CI->db->select('pct_id, pct_descricao, pct_ativo');
  $CI->db->from('tb_pessoa_cfg_tipo');
  if($pctAtivo != ""){
    $CI->db->where('pct_ativo =', $pctAtivo);
  }
  $CI->db->order_by('pct_descricao', 'ASC');

  $query = $CI->db->get();
  if(!$query){
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "Erro ao retornar todos os Tipos de Configuração!";
  } else {
    foreach ($query->result() as $row){
      $PessoaCfgTipo = [];
      $PessoaCfgTipo["pct_id"]        = $row->pct_id;
      $PessoaCfgTipo["pct_descricao"] = $row->pct_descricao;
      $PessoaCfgTipo["pct_ativo"]     = $row->pct_ativo;

      $arrRetorno["arrPessoaCfgTipo"][] = $PessoaCfgTipo;
    }
  }

  return $arrRetorno;
}

function pegaListaPesCfgTipo($detalhes=false, $edicao=false, $exclusao=false)
{
  $CI = pega_instancia();
  $CI->load->database();

  require_once(FCPATH."/assets/Lista_CI/Lista_CI.php");
  $Lista_CI = new Lista_CII($CI->db);
  $Lista_CI->setId("ListaPesCfgTipo");
  $Lista_CI->addField("pct_id AS id");
  $Lista_CI->addField("pct_descricao AS \"Descrição\"", "L");
  $Lista_CI->addField("ativo AS \"Ativo\"");
  if($detalhes){
    $url = base_url() . "PessoaCfgTipo/visualizar/{pct_id}";
    $Lista_CI->addField("REPLACE('<a href=\"$url\"><i class=\"material-icons text-info\">visibility</i></a>', '{pct_id}', pct_id) AS \"Visualizar\" ", "C", "3%");
  }
  if($edicao){
    $url = base_url() . "PessoaCfgTipo/editar/{pct_id}";
    $Lista_CI->addField("REPLACE('<a href=\"$url\"><i class=\"material-icons text-info\">create</i></a>', '{pct_id}', pct_id) AS \"Editar\" ", "C", "3%");
  }
  if($exclusao){
    //delete
    //block
  }
  $Lista_CI->addFrom("v_tb_pessoa_cfg_tipo");
  $Lista_CI->changeOrderCol(2);

  $Lista_CI->addFilter("pct_id", "id", "numeric");
  $Lista_CI->addFilter("pct_descricao", "Descrição");
  $Lista_CI->addFilter("ativo", "Ativo");

  return $Lista_CI->getHtml();
}

function validaInserePesCfgTipo($PessoaCfgTipo)
{
  $strValida = "";

  // validacao basica dos campos
  $vDescricao = $PessoaCfgTipo["pct_descricao"] ?? "";
  if(strlen($vDescricao) <= 2){
    $strValida .= "<br />&nbsp;&nbsp;* Informe uma descrição válida (entre 3 e 80 caracteres).";
  }

  $vAtivo = $PessoaCfgTipo["pct_ativo"] ?? "";
  if(!($vAtivo == 0 || $vAtivo == 1)){
    $strValida .= "<br />&nbsp;&nbsp;* Informação 'ativo' é inválida.";
  }
  // ===========================

  // descricao duplicada
  $CI = pega_instancia();
  $CI->load->database();

  $CI->db->select('COUNT(*) AS cnt');
  $CI->db->from('tb_pessoa_cfg_tipo');
  $CI->db->where('pct_descricao =', $vDescricao);

  $query = $CI->db->get();
  $row   = $query->row();
  if (!isset($row) || $row->cnt > 0) {
    $strValida .= "<br />&nbsp;&nbsp;* Você já tem um tipo de configuração com essa descrição.";
  }
  // ===================

  if($strValida != ""){
    $strValida  = "Corrija essas informações antes de prosseguir:<br />$strValida";
  }

  return $strValida;
}

function validaEditaPesCfgTipo($PessoaCfgTipo)
{
  $strValida = "";

  // validacao basica dos campos
  $vId = $PessoaCfgTipo["pct_id"] ?? "";
  if(!is_numeric($vId)){
    $strValida .= "<br />&nbsp;&nbsp;* Informe um ID válido.";
  }

  $vDescricao = $PessoaCfgTipo["pct_descricao"] ?? "";
  if(strlen($vDescricao) <= 2){
    $strValida .= "<br />&nbsp;&nbsp;* Informe uma descrição válida (entre 3 e 80 caracteres).";
  }

  $vAtivo = $PessoaCfgTipo["pct_ativo"] ?? "";
  if(!($vAtivo == 0 || $vAtivo == 1)){
    $strValida .= "<br />&nbsp;&nbsp;* Informação 'ativo' é inválida.";
  }
  // ===========================

  // descricao duplicada
  $CI = pega_instancia();
  $CI->load->database();

  $CI->db->select('COUNT(*) AS cnt');
  $CI->db->from('tb_pessoa_cfg_tipo');
  $CI->db->where('pct_descricao =', $vDescricao);
  $CI->db->where('pct_id <>', $vId);

  $query = $CI->db->get();
  $row   = $query->row();
  if (!isset($row) || $row->cnt > 0) {
    $strValida .= "<br />&nbsp;&nbsp;* Você já tem um tipo de configuração com essa descrição.";
  }
  // ===================

  if($strValida != ""){
    $strValida  = "Corrija essas informações antes de prosseguir:<br />$strValida";
  }

  return $strValida;
}

function inserePesCfgTipo($PessoaCfgTipo)
{
  $arrRetorno         = [];
  $arrRetorno["erro"] = false;
  $arrRetorno["msg"]  = "";

  $strValida = validaInserePesCfgTipo($PessoaCfgTipo);
  if($strValida != ""){
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = $strValida;

    return $arrRetorno;
  }

  $vDescricao = $PessoaCfgTipo["pct_descricao"] ?? "";
  $vAtivo     = $PessoaCfgTipo["pct_ativo"] ?? 1;

  $CI = pega_instancia();
  $CI->load->database();

  $data = array(
    "pct_descricao" => $vDescricao,
    "pct_ativo"     => $vAtivo,
  );
  $ret = $CI->db->insert('tb_pessoa_cfg_tipo', $data);

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

function editaPesCfgTipo($PessoaCfgTipo)
{
  $arrRetorno         = [];
  $arrRetorno["erro"] = false;
  $arrRetorno["msg"]  = "";

  $strValida = validaEditaPesCfgTipo($PessoaCfgTipo);
  if($strValida != ""){
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = $strValida;

    return $arrRetorno;
  }

  $vId        = $PessoaCfgTipo["pct_id"] ?? "";
  $vDescricao = $PessoaCfgTipo["pct_descricao"] ?? "";
  $vAtivo     = (int) $PessoaCfgTipo["pct_ativo"] ?? 1;

  $CI = pega_instancia();
  $CI->load->database();

  $data = array(
    "pct_descricao" => $vDescricao,
    "pct_ativo"     => $vAtivo,
  );
  $CI->db->where('pct_id', $vId);
  $ret = $CI->db->update('tb_pessoa_cfg_tipo', $data);

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