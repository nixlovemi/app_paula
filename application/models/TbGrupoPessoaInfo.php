<?php
function pegaGrupoPessoaInfo($id, $apenasCamposTabela=false)
{
  $arrRetorno = [];
  $arrRetorno["erro"]            = false;
  $arrRetorno["msg"]             = "";
  $arrRetorno["GrupoPessoaInfo"] = [];

  if(!is_numeric($id)){
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "ID inválido para buscar medidas do participante do grupo!";

    return $arrRetorno;
  }

  $CI = pega_instancia();
  $CI->load->database();

  // so exibe de quem cadastrou
  $UsuarioLog = $CI->session->usuario_info ?? array();
  // ==========================

  $camposTabela = "gpi_id, gpi_grp_id, gpi_data, gpi_altura, gpi_peso, gpi_peso_objetivo, gpi_inicial";
  if(!$apenasCamposTabela){
    $camposTabela .= ", grp_id, grp_gru_id, grp_pes_id, grp_ativo, ativo, gru_usu_id, gru_descricao, pes_nome, pes_email, pet_descricao, pet_cliente";
  }

  $CI->db->select($camposTabela);
  $CI->db->from('v_tb_grupo_pessoa_info');
  $CI->db->where('gpi_id =', $id);
  if($UsuarioLog->admin == 0){
    $CI->db->where('gru_usu_id =', $UsuarioLog->id);
  }

  $query = $CI->db->get();
  if(!$query){
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "Erro ao encontrar medidas do participante do grupo!";

    return $arrRetorno;
  }

  foreach ($query->result() as $row) {
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

/**
 * pega o $GrupoPessoaInfo e quebra em dois indices (primeira, demais)
 */
function agrupaGrupoPessoaInfoLancamentos($GrupoPessoaInfo)
{
  $arrRetorno = [];
  $arrRetorno["erro"]                           = false;
  $arrRetorno["msg"]                            = "";
  $arrRetorno["GrupoPessoaInfoGrp"]             = [];
  $arrRetorno["GrupoPessoaInfoGrp"]["primeira"] = []; #uma info apenas
  $arrRetorno["GrupoPessoaInfoGrp"]["demais"]   = []; #array de info

  foreach($GrupoPessoaInfo as $gpInfo){
    $vInicial   = $gpInfo["gpi_inicial"] ?? 0;
    $ehPrimeira = $vInicial == 1;

    if($ehPrimeira){
      $arrRetorno["GrupoPessoaInfoGrp"]["primeira"] = $gpInfo;
    } else {
      $arrRetorno["GrupoPessoaInfoGrp"]["demais"][] = $gpInfo;
    }
  }

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
  if(!valida_data($vData)){
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

  $CI->db->select('grp_gru_id, grp_pes_id');
  $CI->db->from('tb_grupo_pessoa');
  $CI->db->where('grp_id =', $vGrpId);
  
  $query = $CI->db->get();
  $row   = $query->row();

  if (!isset($row)) {
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "Erro ao encontrar Grupo e Participante!";

    return $arrRetorno;
  }

  $vGruId = $row->grp_gru_id ?? "";
  $vPesId = $row->grp_pes_id ?? "";
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