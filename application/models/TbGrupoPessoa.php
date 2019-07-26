<?php
//!! lembrar de exibir apenas pra pessoa logada
//=============================================

function pegaListaGrupoPessoa($vGruId, $detalhes=false, $edicao=false, $exclusao=false)
{
  $CI = pega_instancia();
  $CI->load->database();

  // so exibe de quem cadastrou
  $UsuarioLog = $CI->session->usuario_info ?? array();
  // ==========================

  require_once(FCPATH."/assets/Lista_CI/Lista_CI.php");
  $Lista_CI = new Lista_CII($CI->db);
  $Lista_CI->setId("ListaGrupoPessoa");
  $Lista_CI->addField("pes_nome AS \"Pessoa\"", "L");
  $Lista_CI->addField("pes_email AS \"Email\"", "L");
  $Lista_CI->addField("pet_descricao AS \"Tipo\"", "L");
  $Lista_CI->addField("ativo AS \"Ativo\"");
  if($detalhes){
  }
  if($edicao){
  }
  if($exclusao){
  }
  $Lista_CI->addFrom("v_tb_grupo_pessoa");

  $Lista_CI->addWhere("grp_gru_id = " . $vGruId);
  if($UsuarioLog->admin == 0){
    $Lista_CI->addWhere("gru_usu_id = " . $UsuarioLog->id);
  }
  $Lista_CI->changeOrderCol(1);

  $Lista_CI->addFilter("pes_nome", "Pessoa");
  $Lista_CI->addFilter("pes_email", "Email");
  $Lista_CI->addFilter("pet_descricao", "Tipo");
  $Lista_CI->addFilter("ativo", "Ativo");

  return $Lista_CI->getHtml();
}

function validaInsereGrupoPessoa($GrupoPessoa)
{
  require_once(APPPATH."/helpers/utils_helper.php");
  $strValida   = "";
  $idUsuLogado = pegaUsuarioLogadoId();

  // validacao basica dos campos
  $vGruId = $GrupoPessoa["grp_gru_id"] ?? "";
  if(!is_numeric($vGruId)){
    $strValida .= "<br />&nbsp;&nbsp;* Grupo inválido para adicionar pessoa.";
  }

  $vPesId = $GrupoPessoa["grp_pes_id"] ?? "";
  if(!is_numeric($vPesId)){
    $strValida .= "<br />&nbsp;&nbsp;* Pessoa inválida para inserir no grupo.";
  }

  $vAtivo = $GrupoPessoa["grp_ativo"] ?? "";
  if(!($vAtivo == 0 || $vAtivo == 1)){
    $strValida .= "<br />&nbsp;&nbsp;* Informação 'ativo' é inválida.";
  }
  // ===========================

  // valida grupo válido
  require_once(APPPATH."/models/TbGrupo.php");
  $retG = pegaGrupo($vGruId, true);

  if($retG["erro"]){
    $strValida .= "<br />&nbsp;&nbsp;* " . $retG["msg"];
  } else {
    $Grupo = $retG["Grupo"];
    if($Grupo["gru_ativo"] == 0){
      $strValida .= "<br />&nbsp;&nbsp;* Este grupo não está ativo.";
    }
    if($Grupo["gru_dt_termino"] < date("Y-m-d")){
      $strValida .= "<br />&nbsp;&nbsp;* Este grupo já está terminado.";
    }
    if($Grupo["gru_usu_id"] != $idUsuLogado){
      $strValida .= "<br />&nbsp;&nbsp;* Este grupo não faz parte do seu cadastro.";
    }
  }
  // ===================

  // valida pessoa válida
  require_once(APPPATH."/models/TbPessoa.php");
  $retP = pegaPessoa($vPesId, true);

  if($retP["erro"]){
    $strValida .= "<br />&nbsp;&nbsp;* " . $retG["msg"];
  } else {
    $Pessoa = $retP["Pessoa"];
    if($Pessoa["pes_ativo"] == 0){
      $strValida .= "<br />&nbsp;&nbsp;* Esta pessoa não está ativa.";
    }
    if($Pessoa["pes_usu_id"] != $idUsuLogado){
      $strValida .= "<br />&nbsp;&nbsp;* Esta pessoa não faz parte do seu cadastro.";
    }
  }
  // ====================

  // valida pessoa já inserida
  $CI = pega_instancia();
  $CI->load->database();

  $CI->db->select('COUNT(*) AS cnt');
  $CI->db->from('tb_grupo_pessoa');
  $CI->db->where('grp_gru_id =', $vGruId);
  $CI->db->where('grp_pes_id =', $vPesId);

  $query = $CI->db->get();
  $row   = $query->row();
  if (!isset($row) || $row->cnt > 0) {
    $strValida .= "<br />&nbsp;&nbsp;* Essa pessoa já participa desse grupo.";
  }
  // =========================

  if($strValida != ""){
    $strValida  = "Corrija essas informações antes de prosseguir:<br />$strValida";
  }

  return $strValida;
}

function insereGrupoPessoaBatch($arrGrupoPessoa)
{
  $arrRetorno          = [];
  $arrRetorno["erro"]  = false;
  $arrRetorno["msg"]   = "";

  $cntErro = 0;
  $data    = [];
  foreach($arrGrupoPessoa as $GrupoPessoa){
    $strValida = validaInsereGrupoPessoa($GrupoPessoa);
    if($strValida != ""){
      $cntErro++;
    } else {
      $vGruId = $GrupoPessoa["grp_gru_id"] ?? NULL;
      $vPesId = $GrupoPessoa["grp_pes_id"] ?? NULL;
      $vAtivo = $GrupoPessoa["grp_ativo"] ?? 1;

      $data[] = array(
        "grp_gru_id" => $vGruId,
        "grp_pes_id" => $vPesId,
        "grp_ativo"  => $vAtivo,
      );
    }
  }

  $CI = pega_instancia();
  $CI->load->database();

  if(count($data) > 0){
    $retInsert = $CI->db->insert_batch('tb_grupo_pessoa', $data);
    if($retInsert === false){
      $arrRetorno["erro"] = true;
      $arrRetorno["msg"]  = "Erro ao inserir as pessoas no Grupo.";
    } else {
      $arrRetorno["erro"]  = false;
      $arrRetorno["msg"]   = "Pessoas inseridas no grupo com sucesso.";
    }
  } else {
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "Nenhuma pessoa encontrada para inserir no Grupo.";
  }

  return $arrRetorno;
}