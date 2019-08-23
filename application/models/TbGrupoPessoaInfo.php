<?php
define("CAMPOS_TABELA_GRUPO_PESSOA_INFO", "gpi_id, gpi_grp_id, gpi_data, gpi_altura, gpi_peso, gpi_peso_objetivo, gpi_inicial");
define("CAMPOS_ADICIONAIS_TABELA_GRUPO_PESSOA_INFO", ", grp_id, grp_gru_id, grp_pes_id, grp_ativo, ativo, gru_usu_id, gru_descricao, pes_nome, pes_email, pet_descricao, pet_cliente");

/**
 * meio que uma função PRIVATE
 */
function pegaArrGrupoPessoaInfoRow($row, $apenasCamposTabela=false)
{
  $GrupoPessoaInfo = [];

  if (isset($row)) {
    $GrupoPessoaInfo["gpi_id"]            = $row->gpi_id;
    $GrupoPessoaInfo["gpi_grp_id"]        = $row->gpi_grp_id;
    $GrupoPessoaInfo["gpi_data"]          = $row->gpi_data;
    $GrupoPessoaInfo["gpi_altura"]        = $row->gpi_altura;
    $GrupoPessoaInfo["gpi_peso"]          = $row->gpi_peso;
    $GrupoPessoaInfo["gpi_peso_objetivo"] = $row->gpi_peso_objetivo;
    $GrupoPessoaInfo["gpi_inicial"]       = $row->gpi_inicial;
    if(!$apenasCamposTabela){
      $GrupoPessoaInfo["grp_id"]          = $row->grp_id;
      $GrupoPessoaInfo["grp_gru_id"]      = $row->grp_gru_id;
      $GrupoPessoaInfo["grp_pes_id"]      = $row->grp_pes_id;
      $GrupoPessoaInfo["grp_ativo"]       = $row->grp_ativo;
      $GrupoPessoaInfo["ativo"]           = $row->ativo;
      $GrupoPessoaInfo["gru_usu_id"]      = $row->gru_usu_id;
      $GrupoPessoaInfo["gru_descricao"]   = $row->gru_descricao;
      $GrupoPessoaInfo["pes_nome"]        = $row->pes_nome;
      $GrupoPessoaInfo["pes_email"]       = $row->pes_email;
      $GrupoPessoaInfo["pet_descricao"]   = $row->pet_descricao;
      $GrupoPessoaInfo["pet_cliente"]     = $row->pet_cliente;
    }
  }

  return $GrupoPessoaInfo;
}

function pegaListaGrupoPessoaInfo($grpId, $detalhes=false, $edicao=false, $exclusao=false)
{
  $CI = pega_instancia();
  $CI->load->database();

  // so exibe de quem cadastrou
  $UsuarioLog = $CI->session->usuario_info ?? array();
  // ==========================

  $nomeLista = "ListaGrupoPessoaInfo_$grpId";
  
  require_once(FCPATH."/assets/Lista_CI/Lista_CI.php");
  $Lista_CI = new Lista_CII($CI->db);
  $Lista_CI->setId($nomeLista);
  $Lista_CI->addField("gpi_data AS \"Data\"", "L", "", "D");
  $Lista_CI->addField("gpi_peso AS \"Peso (KG)\"", "L", "", "NB3");
  if($detalhes){
  }
  if($edicao){
    $Lista_CI->addField("REPLACE('<a href=\"javascript:;\" onclick=\"jsonEditaGrupoPessoaInfo({gpi_id}) \"><i class=\"material-icons text-info\">create</i></a>', '{gpi_id}', gpi_id) AS \"Editar\" ", "C", "3%");
  }
  if($exclusao){
    $Lista_CI->addField("REPLACE('<a href=\"javascript:;\" onclick=\"confirm_delete(''$nomeLista'', ''GrupoPessoaInfo'', ''jsonDelete'', ''id={gpi_id}'')\"><i class=\"material-icons text-info\">delete</i></a>', '{gpi_id}', gpi_id) AS \"Excluir\" ", "C", "3%");
  }
  $Lista_CI->addFrom("v_tb_grupo_pessoa_info");
  $Lista_CI->addWhere("gpi_inicial = 0");
  $Lista_CI->addWhere("grp_id = $grpId");

  if($UsuarioLog->admin == 0){
    $Lista_CI->addWhere("gru_usu_id = " . $UsuarioLog->id);
  }
  $Lista_CI->changeOrderCol(1);

  return $Lista_CI->getHtml();
}

function pegaGrupoPessoaInfo($grpId, $apenasCamposTabela=false)
{
  $arrRetorno = [];
  $arrRetorno["erro"]            = false;
  $arrRetorno["msg"]             = "";
  $arrRetorno["GrupoPessoaInfo"] = [];

  if(!is_numeric($grpId)){
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "ID inválido para buscar medidas do participante do grupo!";

    return $arrRetorno;
  }

  $CI = pega_instancia();
  $CI->load->database();

  // so exibe de quem cadastrou
  $UsuarioLog = $CI->session->usuario_info ?? array();
  // ==========================

  $camposTabela = CAMPOS_TABELA_GRUPO_PESSOA_INFO;
  if(!$apenasCamposTabela){
    $camposTabela .= CAMPOS_ADICIONAIS_TABELA_GRUPO_PESSOA_INFO;
  }

  $CI->db->select($camposTabela);
  $CI->db->from('v_tb_grupo_pessoa_info');
  $CI->db->where('gpi_grp_id =', $grpId);
  if($UsuarioLog->admin == 0){
    $CI->db->where('gru_usu_id =', $UsuarioLog->id);
  }
  $CI->db->order_by('gpi_data', 'ASC');

  $query = $CI->db->get();
  if(!$query){
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "Erro ao encontrar medidas do participante do grupo!";

    return $arrRetorno;
  }

  foreach ($query->result() as $row) {
    if (isset($row)) {
      $GrupoPessoaInfo                 = pegaArrGrupoPessoaInfoRow($row, $apenasCamposTabela);
      $arrRetorno["GrupoPessoaInfo"][] = $GrupoPessoaInfo;
    }
  }

  $arrRetorno["msg"] = "Medidas do participante do grupo encontrado com sucesso!";
  return $arrRetorno;
}

function pegaGrupoPessoaInfoPesGru($pes_id, $gru_id, $apenasCamposTabela=false)
{
  $CI = pega_instancia();
  $CI->load->database();

  $CI->db->select("grp_id");
  $CI->db->from('tb_grupo_pessoa');
  $CI->db->where('grp_pes_id =', $pes_id);
  $CI->db->where('grp_gru_id =', $gru_id);

  $query = $CI->db->get();
  $row   = $query->row();
  $id    = $row->grp_id ?? "";

  return pegaGrupoPessoaInfo($id, $apenasCamposTabela);
}

function pegaGrupoPessoaInfoId($gpiId, $apenasCamposTabela=false)
{
  $arrRetorno = [];
  $arrRetorno["erro"]            = false;
  $arrRetorno["msg"]             = "";
  $arrRetorno["GrupoPessoaInfo"] = [];

  if(!is_numeric($gpiId)){
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "ID inválido para buscar medida do participante do grupo!";

    return $arrRetorno;
  }

  $CI = pega_instancia();
  $CI->load->database();

  // so exibe de quem cadastrou
  $UsuarioLog = $CI->session->usuario_info ?? array();
  // ==========================

  $camposTabela = CAMPOS_TABELA_GRUPO_PESSOA_INFO;
  if(!$apenasCamposTabela){
    $camposTabela .= CAMPOS_ADICIONAIS_TABELA_GRUPO_PESSOA_INFO;
  }

  $CI->db->select($camposTabela);
  $CI->db->from('v_tb_grupo_pessoa_info');
  $CI->db->where('gpi_id =', $gpiId);
  if($UsuarioLog->admin == 0){
    $CI->db->where('gru_usu_id =', $UsuarioLog->id);
  }
  $CI->db->order_by('gpi_data', 'ASC');

  $query = $CI->db->get();
  if(!$query){
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "Erro ao encontrar medida do participante do grupo!";

    return $arrRetorno;
  }

  $row                           = $query->row();
  $arrRetorno["GrupoPessoaInfo"] = pegaArrGrupoPessoaInfoRow($row, $apenasCamposTabela);
  $arrRetorno["msg"]             = "Medida do participante do grupo encontrado com sucesso!";
  return $arrRetorno;
}

/**
 * pega o $GrupoPessoaInfo e quebra em dois indices (primeira, demais)
 */
function agrupaGrupoPessoaInfoLancamentos($GrupoPessoaInfo)
{
  $alturaAtual = NULL;
  $pesoAtual   = NULL;

  $arrRetorno = [];
  $arrRetorno["erro"]                           = false;
  $arrRetorno["msg"]                            = "";
  $arrRetorno["GrupoPessoaInfoGrp"]             = [];
  $arrRetorno["GrupoPessoaInfoGrp"]["primeira"] = []; #uma info apenas
  $arrRetorno["GrupoPessoaInfoGrp"]["demais"]   = []; #array de info
  $arrRetorno["GrupoPessoaInfoGrp"]["altura"]   = NULL;
  $arrRetorno["GrupoPessoaInfoGrp"]["peso"]     = NULL;

  foreach($GrupoPessoaInfo as $gpInfo){
    $vInicial   = $gpInfo["gpi_inicial"] ?? 0;
    $ehPrimeira = $vInicial == 1;

    if($ehPrimeira){
      $arrRetorno["GrupoPessoaInfoGrp"]["primeira"] = $gpInfo;
      $alturaAtual                                  = $gpInfo["gpi_altura"] ?? NULL;
    } else {
      $arrRetorno["GrupoPessoaInfoGrp"]["demais"][] = $gpInfo;
    }

    $pesoAtual = $gpInfo["gpi_peso"] ?? NULL;
  }

  $arrRetorno["GrupoPessoaInfoGrp"]["altura"] = $alturaAtual;
  $arrRetorno["GrupoPessoaInfoGrp"]["peso"]   = $pesoAtual;
  return $arrRetorno;
}

function validaInsereGrupoPessoaInfo($GrupoPessoaInfo)
{	
  require_once(APPPATH."/helpers/utils_helper.php");
  $strValida   = "";
  $idUsuLogado = pegaUsuarioLogadoId();
  $vPrimeira   = $GrupoPessoaInfo["gpi_inicial"] ?? 0;

  // se nao for lcto inicial, n precisa dessa info
  if($vPrimeira == 0){
    $GrupoPessoaInfo["gpi_altura"]        = NULL;
    $GrupoPessoaInfo["gpi_peso_objetivo"] = NULL;
  }

  // validacao basica dos campos
  $vGrpId = $GrupoPessoaInfo["gpi_grp_id"] ?? "";
  if(!is_numeric($vGrpId)){
    $strValida .= "<br />&nbsp;&nbsp;* Participante inválido para adicionar medidas.";
  }

  $vData = $GrupoPessoaInfo["gpi_data"] ?? "";
  if(!isValidDate($vData, 'Y-m-d')){
    $strValida .= "<br />&nbsp;&nbsp;* Informe uma data válida.";
  }

  $vAltura = $GrupoPessoaInfo["gpi_altura"] ?? "";
  if(!($vAltura >= 40 && $vAltura <= 260) && $vPrimeira == 1){
    $strValida .= "<br />&nbsp;&nbsp;* Informação 'altura' é inválida. (valor válido entre 40 e 260)";
  }

  $vPeso = $GrupoPessoaInfo["gpi_peso"] ?? "";
  if(!($vPeso >= 20 && $vPeso <= 500)){
    $strValida .= "<br />&nbsp;&nbsp;* Informação 'peso' é inválida. (valor válido entre 20 e 500)";
  }

  $vPesoObj = $GrupoPessoaInfo["gpi_peso_objetivo"] ?? "";
  if(!($vPesoObj >= 20 && $vPesoObj <= 500) && $vPrimeira == 1){
    $strValida .= "<br />&nbsp;&nbsp;* Informação 'peso objetivo' é inválida. (valor válido entre 20 e 500)";
  }
  // ===========================

  // grupo e pessoa ============
  $CI = pega_instancia();
  $CI->load->database();

  $CI->db->select('grp_gru_id, grp_pes_id, g.gru_dt_inicio, g.gru_dt_termino');
  $CI->db->from('tb_grupo_pessoa');
  $CI->db->join('tb_grupo g', 'g.gru_id = grp_gru_id', 'left');
  $CI->db->where('grp_id =', $vGrpId);
  
  $query = $CI->db->get();
  $row   = $query->row();

  if (!isset($row)) {
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "Erro ao encontrar Grupo e Participante!";

    return $arrRetorno;
  }

  $vGruId    = $row->grp_gru_id ?? "";
  $vPesId    = $row->grp_pes_id ?? "";
  $vGruDtIni = $row->gru_dt_inicio ?? NULL;
  $vGruDtFim = $row->gru_dt_termino ?? NULL;
  // ===========================

  // valida grupo válido
  require_once(APPPATH."/models/TbGrupo.php");
  $retGrp = validaGrupo($vGruId, $idUsuLogado);
  if($retGrp["erro"]){
    $strValida .= "<br />&nbsp;&nbsp;* " . $retGrp["msg"];
  }
  // ===================
  
  // valida pessoa válida
  require_once(APPPATH."/models/TbPessoa.php");
  $retPes = validaPessoa($vPesId, $idUsuLogado);
  if($retPes["erro"]){
    $strValida .= "<br />&nbsp;&nbsp;* " . $retPes["msg"];
  }
  // ====================

  // valida lcto inicial
  if($vPrimeira == 1){
    $CI->db->select('COUNT(*) AS cnt');
    $CI->db->from('tb_grupo_pessoa_info');
    $CI->db->where('gpi_grp_id =', $vGrpId);
    $CI->db->where('gpi_inicial =', 1);

    $query = $CI->db->get();
    $row   = $query->row();
    if (!isset($row) || $row->cnt > 0) {
      $strValida .= "<br />&nbsp;&nbsp;* Medidas iniciais já informadas para esse participante.";
    }
  }
  // ===================

  // lancamento no periodo do grupo
  if(!($vData >= $vGruDtIni) && $vData <= $vGruDtFim){
    $strValida .= "<br />&nbsp;&nbsp;* Data de lançamento fora do período do grupo!";
  }
  // ==============================

  // lancamento na msm data
  $CI->db->select('COUNT(*) AS cnt');
  $CI->db->from('tb_grupo_pessoa_info');
  $CI->db->where('gpi_grp_id =', $vGrpId);
  $CI->db->where('gpi_data =', $vData);

  $query2 = $CI->db->get();
  $row2   = $query2->row();
  if (!isset($row2) || $row2->cnt > 0) {
    $strValida .= "<br />&nbsp;&nbsp;* Já existe um lançamento para essa data.";
  }
  // ======================

  if($strValida != ""){
    $strValida  = "Corrija essas informações antes de prosseguir:<br />$strValida";
  }

  return $strValida;
}

function insereGrupoPessoaInfo($GrupoPessoaInfo)
{
  $arrRetorno          = [];
  $arrRetorno["erro"]  = false;
  $arrRetorno["msg"]   = "";

  $strValida = validaInsereGrupoPessoaInfo($GrupoPessoaInfo);
  if($strValida != ""){
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = $strValida;

    return $arrRetorno;
  }

  $vGrpId   = $GrupoPessoaInfo["gpi_grp_id"] ?? NULL;
  $vData    = $GrupoPessoaInfo["gpi_data"] ?? NULL;
  $vAltura  = $GrupoPessoaInfo["gpi_altura"] ?? NULL;
  $vPeso    = $GrupoPessoaInfo["gpi_peso"] ?? NULL;
  $vPesoObj = $GrupoPessoaInfo["gpi_peso_objetivo"] ?? NULL;
  $vInicial = $GrupoPessoaInfo["gpi_inicial"] ?? NULL;

  $CI = pega_instancia();
  $CI->load->database();

  $data = array(
    "gpi_grp_id"        => $vGrpId,
    "gpi_data"          => $vData,
    "gpi_altura"        => $vAltura,
    "gpi_peso"          => $vPeso,
    "gpi_peso_objetivo" => $vPesoObj,
    "gpi_inicial"       => $vInicial,
  );
  $ret = $CI->db->insert('tb_grupo_pessoa_info', $data);

  if(!$ret){
    $error = $CI->db->error();

    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "Erro ao inserir medidas do participante. Mensagem: " . $error["message"];
  } else {
    $arrRetorno["erro"]  = false;
    $arrRetorno["msg"]   = "Medidas do participante inseridas com sucesso.";
    $arrRetorno["gruId"] = $CI->db->insert_id();
  }

  return $arrRetorno;
}

function deletaGrupoPessoaInfo($gpiId)
{
  $arrRetorno         = [];
  $arrRetorno["erro"] = false;
  $arrRetorno["msg"]  = "";

  if(!is_numeric($gpiId)){
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "ID inválido para excluir medida do participante!";
  } else {
    $CI = pega_instancia();
    $CI->load->database();

    $CI->db->where('gpi_id', $gpiId);
    $CI->db->delete('tb_grupo_pessoa_info');

    if($CI->db->affected_rows() <= 0){
      $arrRetorno["erro"] = true;
      $arrRetorno["msg"]  = "Erro ao excluir essa Medida do Participante!";
    } else {
      $arrRetorno["erro"] = false;
      $arrRetorno["msg"]  = "Medida do Participante excluída com sucesso!";
    }
  }

  return $arrRetorno;
}

function validaEditaGrupoPessoaInfo($GrupoPessoaInfo)
{
  require_once(APPPATH."/helpers/utils_helper.php");
  $strValida   = "";
  $idUsuLogado = pegaUsuarioLogadoId();
  $vPrimeira   = $GrupoPessoaInfo["gpi_inicial"] ?? 0;

  // se nao for lcto inicial, n precisa dessa info
  if($vPrimeira == 0){
    $GrupoPessoaInfo["gpi_altura"]        = NULL;
    $GrupoPessoaInfo["gpi_peso_objetivo"] = NULL;
  }

  // validacao basica dos campos
  $vGpiId = $GrupoPessoaInfo["gpi_id"] ?? "";
   if(!is_numeric($vGpiId)){
    $strValida .= "<br />&nbsp;&nbsp;* ID inválido para editar medidas.";
  }

  $vGrpId = $GrupoPessoaInfo["gpi_grp_id"] ?? "";
  if(!is_numeric($vGrpId)){
    $strValida .= "<br />&nbsp;&nbsp;* Participante inválido para editar medidas.";
  }

  $vData = $GrupoPessoaInfo["gpi_data"] ?? "";
  if(!isValidDate($vData, 'Y-m-d')){
    $strValida .= "<br />&nbsp;&nbsp;* Informe uma data válida.";
  }

  $vAltura = $GrupoPessoaInfo["gpi_altura"] ?? "";
  if(!($vAltura >= 40 && $vAltura <= 260) && $vPrimeira == 1){
    $strValida .= "<br />&nbsp;&nbsp;* Informação 'altura' é inválida. (valor válido entre 40 e 260)";
  }

  $vPeso = $GrupoPessoaInfo["gpi_peso"] ?? "";
  if(!($vPeso >= 20 && $vPeso <= 500)){
    $strValida .= "<br />&nbsp;&nbsp;* Informação 'peso' é inválida. (valor válido entre 20 e 500)";
  }

  $vPesoObj = $GrupoPessoaInfo["gpi_peso_objetivo"] ?? "";
  if(!($vPesoObj >= 20 && $vPesoObj <= 500) && $vPrimeira == 1){
    $strValida .= "<br />&nbsp;&nbsp;* Informação 'peso objetivo' é inválida. (valor válido entre 20 e 500)";
  }
  // ===========================

  // grupo e pessoa ============
  $CI = pega_instancia();
  $CI->load->database();

  $CI->db->select('grp_gru_id, grp_pes_id, g.gru_dt_inicio, g.gru_dt_termino');
  $CI->db->from('tb_grupo_pessoa');
  $CI->db->join('tb_grupo g', 'g.gru_id = grp_gru_id', 'left');
  $CI->db->where('grp_id =', $vGrpId);

  $query = $CI->db->get();
  $row   = $query->row();

  if (!isset($row)) {
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "Erro ao encontrar Grupo e Participante!";

    return $arrRetorno;
  }

  $vGruId    = $row->grp_gru_id ?? "";
  $vPesId    = $row->grp_pes_id ?? "";
  $vGruDtIni = $row->gru_dt_inicio ?? NULL;
  $vGruDtFim = $row->gru_dt_termino ?? NULL;
  // ===========================

  // valida grupo válido
  require_once(APPPATH."/models/TbGrupo.php");
  $retGrp = validaGrupo($vGruId, $idUsuLogado);
  if($retGrp["erro"]){
    $strValida .= "<br />&nbsp;&nbsp;* " . $retGrp["msg"];
  }
  // ===================

  // valida pessoa válida
  require_once(APPPATH."/models/TbPessoa.php");
  $retPes = validaPessoa($vPesId, $idUsuLogado);
  if($retPes["erro"]){
    $strValida .= "<br />&nbsp;&nbsp;* " . $retPes["msg"];
  }
  // ====================

  // valida lcto inicial
  if($vPrimeira == 1){
    $CI->db->select('COUNT(*) AS cnt');
    $CI->db->from('tb_grupo_pessoa_info');
    $CI->db->where('gpi_grp_id =', $vGrpId);
    $CI->db->where('gpi_inicial =', 1);
    $CI->db->where('gpi_id <>', $vGpiId);

    $query = $CI->db->get();
    $row   = $query->row();
    if (!isset($row) || $row->cnt > 0) {
      $strValida .= "<br />&nbsp;&nbsp;* Medidas iniciais já informadas para esse participante.";
    }
  }
  // ===================

  // lancamento no periodo do grupo
  if(!($vData >= $vGruDtIni) && $vData <= $vGruDtFim){
    $strValida .= "<br />&nbsp;&nbsp;* Data de lançamento fora do período do grupo!";
  }
  // ==============================

  // lancamento na msm data
  $CI->db->select('COUNT(*) AS cnt');
  $CI->db->from('tb_grupo_pessoa_info');
  $CI->db->where('gpi_grp_id =', $vGrpId);
  $CI->db->where('gpi_data =', $vData);
  $CI->db->where('gpi_id <>', $vGpiId);

  $query2 = $CI->db->get();
  $row2   = $query2->row();
  if (!isset($row2) || $row2->cnt > 0) {
    $strValida .= "<br />&nbsp;&nbsp;* Já existe um lançamento para essa data.";
  }
  // ======================

  if($strValida != ""){
    $strValida  = "Corrija essas informações antes de prosseguir:<br />$strValida";
  }

  return $strValida;
}

function editaGrupoPessoaInfo($GrupoPessoaInfo)
{
  $arrRetorno          = [];
  $arrRetorno["erro"]  = false;
  $arrRetorno["msg"]   = "";

  // carrega info do BD
  $vId = $GrupoPessoaInfo["gpi_id"] ?? "";
  $retP = pegaGrupoPessoaInfoId($vId, true);
  if($retP["erro"]){
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = $retP["msg"];
    return $arrRetorno;
  }
  $data = $retP["GrupoPessoaInfo"];
  foreach($GrupoPessoaInfo as $field_name => $field_value){
    if(array_key_exists($field_name, $data)){
      $data[$field_name] = $field_value;
    }
  }
  // ==================

  $strValida = validaEditaGrupoPessoaInfo($data);
  if($strValida != ""){
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = $strValida;
    return $arrRetorno;
  }

  $CI = pega_instancia();
  $CI->load->database();

  $CI->db->where('gpi_id', $vId);
  $ret = $CI->db->update('tb_grupo_pessoa_info', $data);

  if(!$ret){
    $error = $CI->db->error();

    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "Erro ao editar medidas do participante. Mensagem: " . $error["message"];
  } else {
    $arrRetorno["erro"]  = false;
    $arrRetorno["msg"]   = "Medidas do participante editada com sucesso.";
  }

  return $arrRetorno;
}