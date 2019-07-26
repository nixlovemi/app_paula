<?php
//!! lembrar de exibir apenas pra pessoa logada
//=============================================

function pegaListaGrupo($detalhes=false, $edicao=false, $exclusao=false)
{
  $CI = pega_instancia();
  $CI->load->database();

  // so exibe de quem cadastrou
  $UsuarioLog = $CI->session->usuario_info ?? array();
  // ==========================

  require_once(FCPATH."/assets/Lista_CI/Lista_CI.php");
  $Lista_CI = new Lista_CII($CI->db);
  $Lista_CI->setId("ListaGrupo");
  $Lista_CI->addField("gru_id AS id");
  $Lista_CI->addField("gru_descricao AS \"Descrição\"", "L");
  $Lista_CI->addField("gru_dt_inicio AS \"Início\"", "C", "", "D");
  $Lista_CI->addField("gru_dt_termino AS \"Término\"", "C", "", "D");
  $Lista_CI->addField("ativo AS \"Ativo\"");
  if($detalhes){
  }
  if($edicao){
    $url = base_url() . "Grupo/editar/{gru_id}";
    $Lista_CI->addField("REPLACE('<a href=\"$url\"><i class=\"material-icons text-success\">create</i></a>', '{gru_id}', gru_id) AS \"Editar\" ", "C", "3%");
  }
  if($exclusao){
  }
  $Lista_CI->addFrom("v_tb_grupo");

  if($UsuarioLog->admin == 0){
    $Lista_CI->addWhere("gru_usu_id = " . $UsuarioLog->id);
  }
  $Lista_CI->changeOrderCol(2);

  $Lista_CI->addFilter("gru_id", "id", "numeric");
  $Lista_CI->addFilter("gru_descricao", "Descrição");
  $Lista_CI->addFilter("str_dt_inicio", "Início");
  $Lista_CI->addFilter("str_dt_termino", "Término");
  $Lista_CI->addFilter("ativo", "Ativo");

  return $Lista_CI->getHtml();
}

function pegaGrupo($gruId, $apenasCamposTabela=false)
{
  $arrRetorno = [];
  $arrRetorno["erro"]  = false;
  $arrRetorno["msg"]   = "";
  $arrRetorno["Grupo"] = [];

  if(!is_numeric($gruId)){
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "ID inválido para buscar Grupo!";

    return $arrRetorno;
  }

  $CI = pega_instancia();
  $CI->load->database();

  // so exibe de quem cadastrou
  $UsuarioLog = $CI->session->usuario_info ?? array();
  // ==========================

  $camposTabela = "gru_id, gru_usu_id, gru_descricao, gru_dt_inicio, gru_dt_termino, gru_ativo";
  if(!$apenasCamposTabela){
    $camposTabela .= ", usu_nome";
  }

  $CI->db->select($camposTabela);
  $CI->db->from('tb_grupo');
  $CI->db->join('tb_usuario', 'usu_id = gru_usu_id', 'left');
  $CI->db->where('gru_id =', $gruId);
  if($UsuarioLog->admin == 0){
    $CI->db->where('gru_usu_id =', $UsuarioLog->id);
  }

  $query = $CI->db->get();
  $row   = $query->row();

  if (!isset($row)) {
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "Erro ao encontrar Grupo!";

    return $arrRetorno;
  }

  $Grupo = [];
  $Grupo["gru_id"]         = $row->gru_id;
  $Grupo["gru_usu_id"]     = $row->gru_usu_id;
  $Grupo["gru_descricao"]  = $row->gru_descricao;
  $Grupo["gru_dt_inicio"]  = $row->gru_dt_inicio;
  $Grupo["gru_dt_termino"] = $row->gru_dt_termino;
  $Grupo["gru_ativo"]      = $row->gru_ativo;
  if(!$apenasCamposTabela){
    $Grupo["usu_nome"]     = $row->usu_nome;
  }

  $arrRetorno["msg"]   = "Grupo encontrado com sucesso!";
  $arrRetorno["Grupo"] = $Grupo;
  return $arrRetorno;
}

function validaInsereGrupo($Grupo)
{
  require_once(APPPATH."/helpers/utils_helper.php");
  $strValida = "";

  // validacao basica dos campos
  $vUsuId = $Grupo["gru_usu_id"] ?? "";
  if(!is_numeric($vUsuId)){
    $strValida .= "<br />&nbsp;&nbsp;* Informação 'usuário de cadastro' é inválida.";
  }

  $vDesc = $Grupo["gru_descricao"] ?? "";
  if(strlen($vDesc) <= 2){
    $strValida .= "<br />&nbsp;&nbsp;* Informe uma descrição válida (entre 3 e 80 caracteres).";
  }

  $vDtIni = $Grupo["gru_dt_inicio"] ?? "";
  if(!valida_data($vDtIni)){
    $strValida .= "<br />&nbsp;&nbsp;* Informe uma data de início válida.";
  }

  $vDtFim = $Grupo["gru_dt_termino"] ?? "";
  if(!valida_data($vDtFim)){
    $strValida .= "<br />&nbsp;&nbsp;* Informe uma data de término válida.";
  }

  if($vDtIni >= $vDtFim){
    $strValida .= "<br />&nbsp;&nbsp;* Data de início tem que ser inferior a data de término.";
  }

  if($vDtFim <= date("Y-m-d")){
    $strValida .= "<br />&nbsp;&nbsp;* Data de término tem que ser superior a data de hoje.";
  }

  $vAtivo = $Grupo["gru_ativo"] ?? "";
  if(!($vAtivo == 0 || $vAtivo == 1)){
    $strValida .= "<br />&nbsp;&nbsp;* Informação 'ativo' é inválida.";
  }
  // ===========================
  
  // valida descricao duplicada
  $CI = pega_instancia();
  $CI->load->database();

  $CI->db->select('COUNT(*) AS cnt');
  $CI->db->from('tb_grupo');
  $CI->db->where('gru_usu_id =', $vUsuId);
  $CI->db->where('gru_descricao =', $vDesc);

  $query = $CI->db->get();
  $row   = $query->row();
  if (!isset($row) || $row->cnt > 0) {
    $strValida .= "<br />&nbsp;&nbsp;* Você já tem um grupo cadastrado com a descrição '" . $vDesc . "'";
  }
  // ==========================

  if($strValida != ""){
    $strValida  = "Corrija essas informações antes de prosseguir:<br />$strValida";
  }

  return $strValida;
}

function insereGrupo($Grupo)
{
  $arrRetorno          = [];
  $arrRetorno["erro"]  = false;
  $arrRetorno["msg"]   = "";
  $arrRetorno["gruId"] = "";

  $strValida = validaInsereGrupo($Grupo);
  if($strValida != ""){
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = $strValida;

    return $arrRetorno;
  }

  $vUsuId = $Grupo["gru_usu_id"] ?? NULL;
  $vDesc  = $Grupo["gru_descricao"] ?? "";
  $vDtIni = $Grupo["gru_dt_inicio"] ?? "";
  $vDtFim = $Grupo["gru_dt_termino"] ?? "";
  $vAtivo = $Grupo["gru_ativo"] ?? 1;

  $CI = pega_instancia();
  $CI->load->database();

  $data = array(
    "gru_usu_id"     => $vUsuId,
    "gru_descricao"  => $vDesc,
    "gru_dt_inicio"  => $vDtIni,
    "gru_dt_termino" => $vDtFim,
    "gru_ativo"      => $vAtivo,
  );
  $ret = $CI->db->insert('tb_grupo', $data);

  if(!$ret){
    $error = $CI->db->error();

    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "Erro ao inserir Grupo. Mensagem: " . $error["message"];
  } else {
    $arrRetorno["erro"]  = false;
    $arrRetorno["msg"]   = "Grupo inserido com sucesso.";
    $arrRetorno["gruId"] = $CI->db->insert_id();
  }

  return $arrRetorno;
}