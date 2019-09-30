<?php
function validaInserePessoaCfg($PessoaCfg)
{
  $strValida = "";

  // validacao basica dos campos
  $vPesId = $PessoaCfg["psc_pes_id"] ?? "";
  if(!is_numeric($vPesId)){
    $strValida .= "<br />&nbsp;&nbsp;* Informe uma pessoa válida.";
  }

  $vPctId = $PessoaCfg["psc_pct_id"] ?? "";
  if(!is_numeric($vPctId)){
    $strValida .= "<br />&nbsp;&nbsp;* Informe uma configuração válida.";
  }

  $vValor = $PessoaCfg["psc_valor"] ?? "";
  if(strlen(trim($vValor)) <= 0){
    $strValida .= "<br />&nbsp;&nbsp;* Informe um valor válido.";
  }
  // ===========================

  // configuracao duplicada
  $CI = pega_instancia();
  $CI->load->database();

  $CI->db->select('COUNT(*) AS cnt');
  $CI->db->from('tb_pessoa_cfg');
  $CI->db->where('psc_pes_id =', $vPesId);
  $CI->db->where('psc_pct_id =', $vPctId);

  $query = $CI->db->get();
  $row   = $query->row();
  if (!isset($row) || $row->cnt > 0) {
    $strValida .= "<br />&nbsp;&nbsp;* Você já tem essa configuração para essa pessoa.";
  }
  // ======================

  if($strValida != ""){
    $strValida  = "Corrija essas informações antes de prosseguir:<br />$strValida";
  }

  return $strValida;
}

function inserePessoaCfg($PessoaCfg)
{
  $arrRetorno         = [];
  $arrRetorno["erro"] = false;
  $arrRetorno["msg"]  = "";

  $strValida = validaInserePessoaCfg($PessoaCfg);
  if($strValida != ""){
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = $strValida;

    return $arrRetorno;
  }

  $vPesId = $PessoaCfg["psc_pes_id"] ?? "";
  $vPctId = $PessoaCfg["psc_pct_id"] ?? "";
  $vValor = $PessoaCfg["psc_valor"] ?? "";

  $CI = pega_instancia();
  $CI->load->database();

  $data = array(
    "psc_pes_id" => $vPesId,
    "psc_pct_id" => $vPctId,
    "psc_valor"  => $vValor,
  );
  $ret = $CI->db->insert('tb_pessoa_cfg', $data);

  if(!$ret){
    $error = $CI->db->error();

    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "Erro ao inserir Configuração da Pessoa. Mensagem: " . $error["message"];
  } else {
    $arrRetorno["erro"] = false;
    $arrRetorno["msg"]  = "Configuração da Pessoa inserida com sucesso.";
  }

  return $arrRetorno;
}

function deletaPessoaCfg($pscId)
{
  $arrRetorno         = [];
  $arrRetorno["erro"] = false;
  $arrRetorno["msg"]  = "";

  if(!is_numeric($pscId)){
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "ID inválido para excluir Configuração da Pessoa!";
  } else {
    $CI = pega_instancia();
    $CI->load->database();

    $CI->db->where('psc_id', $pscId);
    $CI->db->delete('tb_pessoa_cfg');

    if($CI->db->affected_rows() <= 0){
      $arrRetorno["erro"] = true;
      $arrRetorno["msg"]  = "Erro ao excluir essa Configuração da Pessoa!";
    } else {
      $arrRetorno["erro"] = false;
      $arrRetorno["msg"]  = "Configuração da Pessoa excluída com sucesso!";
    }
  }

  return $arrRetorno;
}

function pegaListaPessoaCfg($pessoa="", $detalhes=false, $edicao=false, $exclusao=false)
{
  $CI = pega_instancia();
  $CI->load->database();

  require_once(FCPATH."/assets/Lista_CI/Lista_CI.php");
  $Lista_CI = new Lista_CII($CI->db);
  $Lista_CI->setId("ListaPessoaCfg");
  $Lista_CI->addField("pes_nome AS \"Pessoa\"", "L");
  $Lista_CI->addField("pct_descricao AS \"Configuração\"", "L");
  $Lista_CI->addField("psc_valor AS \"Valor\"");
  if($detalhes){
  }
  if($edicao){
  }
  if($exclusao){
    $Lista_CI->addField("REPLACE('<a href=\"javascript:;\" onclick=\"confirm_delete(''ListaPessoaCfg'', ''PessoaCfg'', ''jsonDelete'', ''id={psc_id}'')\"><i class=\"material-icons text-info\">delete</i></a>', '{psc_id}', psc_id) AS \"Excluir\" ", "C", "3%");
  }
  $Lista_CI->addFrom("v_tb_pessoa_cfg");
  if(is_numeric($pessoa) && $pessoa > 0){
    $Lista_CI->addWhere("psc_pes_id = $pessoa");
  }
  $Lista_CI->changeOrderCol(2);
  $Lista_CI->setAutoReload(false);

  $Lista_CI->addFilter("pes_nome", "Pessoa");
  $Lista_CI->addFilter("uct_descricao", "Configuração");

  return $Lista_CI->getHtml();
}

function pegaMaximoUsuarios($pesId)
{
  $arrRetorno          = [];
  $arrRetorno["erro"]  = false;
  $arrRetorno["msg"]   = "";
  $arrRetorno["valor"] = "";

  if(!is_numeric($pesId)){
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "ID inválido para buscar informação 'Máximo Usuários'!";

    return $arrRetorno;
  }

  $CI = pega_instancia();
  $CI->load->database();

  $CI->db->select('psc_valor');
  $CI->db->from('tb_pessoa_cfg');
  $CI->db->where('psc_pes_id =', $pesId);
  $CI->db->where('psc_pct_id =', 2);

  $query = $CI->db->get();
  $row   = $query->row();

  if (!isset($row)) {
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "Erro ao encontrar informação 'Máximo Usuários'!";

    return $arrRetorno;
  }

  $arrRetorno["msg"]   = "'Máximo Usuários' encontrado com sucesso!";
  $arrRetorno["valor"] = (int) $row->psc_valor;
  return $arrRetorno;
}

function pegaCfgValidade($pesId)
{
  $arrRetorno          = [];
  $arrRetorno["erro"]  = false;
  $arrRetorno["msg"]   = "";
  $arrRetorno["valor"] = "";

  if(!is_numeric($pesId)){
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "ID inválido para buscar informação 'Validade'!";

    return $arrRetorno;
  }

  $CI = pega_instancia();
  $CI->load->database();

  $CI->db->select('psc_valor');
  $CI->db->from('tb_pessoa_cfg');
  $CI->db->where('psc_pes_id =', $pesId);
  $CI->db->where('psc_pct_id =', 1);

  $query = $CI->db->get();
  $row   = $query->row();

  if (!isset($row)) {
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "Erro ao encontrar informação 'Validade'!";

    return $arrRetorno;
  }

  $arrRetorno["msg"]   = "'Validade' encontrada com sucesso!";
  $arrRetorno["valor"] = acerta_data($row->psc_valor);
  return $arrRetorno;
}