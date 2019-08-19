<?php
//!! lembrar de exibir apenas pra pessoa logada
//=============================================

function pegaGrupoPessoa($id, $apenasCamposTabela=false)
{
  $arrRetorno = [];
  $arrRetorno["erro"]        = false;
  $arrRetorno["msg"]         = "";
  $arrRetorno["GrupoPessoa"] = [];

  if(!is_numeric($id)){
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "ID inválido para buscar participante do grupo!";

    return $arrRetorno;
  }

  $CI = pega_instancia();
  $CI->load->database();

  // so exibe de quem cadastrou
  $UsuarioLog    = $CI->session->usuario_info ?? array();
  $vGrpId        = $CI->session->grp_id ?? NULL; # se está na session do grupo
  // ==========================

  $camposTabela = "grp_id, grp_gru_id, grp_pes_id, grp_usu_id, grp_ativo";
  if(!$apenasCamposTabela){
    $camposTabela .= ", ativo, gru_usu_id, gru_descricao, pes_nome, pes_email, pes_foto, pet_descricao, pet_cliente";
  }

  $CI->db->select($camposTabela);
  $CI->db->from('v_tb_grupo_pessoa');
  $CI->db->where('grp_id =', $id);
  if($UsuarioLog->admin == 0 && $vGrpId == NULL){
    $CI->db->where('gru_usu_id =', $UsuarioLog->id);
  }

  $query = $CI->db->get();
  $row   = $query->row();

  if (!isset($row)) {
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "Erro ao encontrar participante do grupo!";

    return $arrRetorno;
  }

  $Grupo = [];
  $Grupo["grp_id"]          = $row->grp_id;
  $Grupo["grp_gru_id"]      = $row->grp_gru_id;
  $Grupo["grp_pes_id"]      = $row->grp_pes_id;
  $Grupo["grp_usu_id"]      = $row->grp_usu_id;
  $Grupo["grp_ativo"]       = $row->grp_ativo;
  if(!$apenasCamposTabela){
    $Grupo["ativo"]         = $row->ativo;
    $Grupo["gru_usu_id"]    = $row->gru_usu_id;
    $Grupo["gru_descricao"] = $row->gru_descricao;
    $Grupo["pes_nome"]      = $row->pes_nome;
    $Grupo["pes_email"]     = $row->pes_email;
    $Grupo["pes_foto"]      = $row->pes_foto;
    $Grupo["pet_descricao"] = $row->pet_descricao;
    $Grupo["pet_cliente"]   = $row->pet_cliente;
  }

  $arrRetorno["msg"]         = "Participante do grupo encontrado com sucesso!";
  $arrRetorno["GrupoPessoa"] = $Grupo;
  return $arrRetorno;
}

function pegaGrupoPessoaPesGru($pes_id, $gru_id, $apenasCamposTabela=false)
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

  return pegaGrupoPessoa($id, $apenasCamposTabela);
}

function pegaGrupoPessoaUsuGru($usu_id, $gru_id, $apenasCamposTabela=false)
{
  $CI = pega_instancia();
  $CI->load->database();

  $CI->db->select("grp_id");
  $CI->db->from('tb_grupo_pessoa');
  $CI->db->where('grp_usu_id =', $usu_id);
  $CI->db->where('grp_gru_id =', $gru_id);

  $query = $CI->db->get();
  $row   = $query->row();
  $id    = $row->grp_id ?? "";

  return pegaGrupoPessoa($id, $apenasCamposTabela);
}

function pegaGruposPessoaId($pes_id)
{
  $arrRetorno = [];
  $arrRetorno["erro"]         = false;
  $arrRetorno["msg"]          = "";
  $arrRetorno["GruposPessoa"] = [];

  require_once(APPPATH."/models/TbPessoa.php");
  $retP = pegaPessoa($pes_id);
  if($retP["erro"]){
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = $retP["msg"];
  } else {
    $Pessoa = $retP["Pessoa"] ?? array();
    $vPesId = $Pessoa["pes_id"] ?? "";

    $CI = pega_instancia();
    $CI->load->database();

    $CI->db->select('grp_id');
    $CI->db->from('v_tb_grupo_pessoa');
    $CI->db->join('tb_grupo g', 'g.gru_id = grp_gru_id', 'left');
    $CI->db->where('grp_pes_id =', $vPesId);
    $CI->db->where('g.gru_ativo =', 1);
    $CI->db->order_by('g.gru_dt_inicio', 'DESC');
    $query = $CI->db->get();

    if(!$query){
      $arrRetorno["erro"] = true;
      $arrRetorno["msg"]  = "Erro ao encontrar os grupos desse participante!";

      return $arrRetorno;
    }

    foreach ($query->result() as $row) {
      if (isset($row)) {
        $vGrpId = $row->grp_id ?? "";

        $retGP = pegaGrupoPessoa($vGrpId, false);
        if(!$retGP["erro"]){
          $GrupoPessoa = $retGP["GrupoPessoa"] ?? array();
          $arrRetorno["GruposPessoa"][] = $GrupoPessoa;
        }
      }
    }
  }

  return $arrRetorno;
}

function pegaGrupoPessoasGru($gru_id, $apenas_staff=false)
{
  $arrRetorno = [];
  $arrRetorno["erro"]          = false;
  $arrRetorno["msg"]           = "";
  $arrRetorno["GruposPessoas"] = [];
  $idUsuLogado                 = pegaUsuarioLogadoId();

  if(!is_numeric($gru_id)){
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "Grupo inválido para buscar pessoas!";
  } else {
    // valida grupo válido
    require_once(APPPATH."/models/TbGrupo.php");
    $retGrp = validaGrupo($gru_id, $idUsuLogado);
    if($retGrp["erro"]){
      $arrRetorno["erro"] = true;
      $arrRetorno["msg"]  = $retGrp["msg"];
    } else {
      $CI = pega_instancia();
      $CI->load->database();

      // so exibe de quem cadastrou
      $UsuarioLog = $CI->session->usuario_info ?? array();
      $vGrpId     = $CI->session->grp_id ?? NULL; # se está na session do grupo
      // ==========================

      $CI->db->select('grp_id');
      $CI->db->from('v_tb_grupo_pessoa');
      $CI->db->where('grp_gru_id =', $gru_id);
      if($UsuarioLog->admin == 0 && $vGrpId == NULL){
        $CI->db->where('gru_usu_id =', $UsuarioLog->id);
      }
      if($apenas_staff){
        $CI->db->where('pet_cliente =', 0);
      }
      $query = $CI->db->get();

      if(!$query){
        $arrRetorno["erro"] = true;
        $arrRetorno["msg"]  = "Erro ao encontrar os participantes desse grupo!";

        return $arrRetorno;
      }

      foreach ($query->result() as $row) {
        if (isset($row)) {
          $vGrpId = $row->grp_id ?? "";

          $retGP = pegaGrupoPessoa($vGrpId, false);
          if(!$retGP["erro"]){
            $GrupoPessoa = $retGP["GrupoPessoa"] ?? array();
            $arrRetorno["GruposPessoas"][] = $GrupoPessoa;
          }
        }
      }
    }
  }

  return $arrRetorno;
}

function pegaListaGrupoPessoa($vGruId, $detalhes=false, $edicao=false, $exclusao=false)
{
  $CI = pega_instancia();
  $CI->load->database();

  // so exibe de quem cadastrou
  $UsuarioLog = $CI->session->usuario_info ?? array();
  // ==========================

  // exibe editar ou detalhar
  if($detalhes && $edicao){
    $detalhes = false;
  }

  require_once(FCPATH."/assets/Lista_CI/Lista_CI.php");
  $Lista_CI = new Lista_CII($CI->db);
  $Lista_CI->setId("ListaGrupoPessoa");
  $Lista_CI->addField("pes_nome AS \"Pessoa\"", "L");
  $Lista_CI->addField("pes_email AS \"Email\"", "L");
  $Lista_CI->addField("pet_descricao AS \"Tipo\"", "L");
  $Lista_CI->addField("ativo AS \"Ativo\"");
  if($detalhes){
    $url = base_url() . "Grupo/infoPessoa/{gru_id}/{pes_id}/0";
    $Lista_CI->addField("CASE pet_cliente WHEN 0 THEN '' ELSE REPLACE(REPLACE('<a href=\"$url\"><i class=\"material-icons text-info\">assignment</i></a>', '{gru_id}', grp_gru_id ), '{pes_id}', grp_pes_id) END AS \"Info\" ", "C", "3%");
  }
  if($edicao){
    $url = base_url() . "Grupo/infoPessoa/{gru_id}/{pes_id}/1";
    $Lista_CI->addField("CASE pet_cliente WHEN 0 THEN '' ELSE REPLACE(REPLACE('<a href=\"$url\"><i class=\"material-icons text-info\">assignment</i></a>', '{gru_id}', grp_gru_id ), '{pes_id}', grp_pes_id) END AS \"Info\" ", "C", "3%");
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

function insereGrupoPessoaDono($gru_id, $usu_id)
{
  $arrRetorno          = [];
  $arrRetorno["erro"]  = false;
  $arrRetorno["msg"]   = "";
  $idUsuLogado         = pegaUsuarioLogadoId();

  // validacao basica dos campos
  $strValida   = "";

  if(!is_numeric($gru_id)){
    $strValida .= "<br />&nbsp;&nbsp;* Grupo inválido para adicionar pessoa.";
  }

  if(!is_numeric($usu_id)){
    $strValida .= "<br />&nbsp;&nbsp;* Usuário inválido para gerenciar grupo.";
  }
  // ===========================

  // valida grupo válido
  require_once(APPPATH."/models/TbGrupo.php");
  $retGrp = validaGrupo($gru_id, $idUsuLogado);
  if($retGrp["erro"]){
    $strValida .= "<br />&nbsp;&nbsp;* " . $retGrp["msg"];
  }
  // ===================
  
  if($strValida != ""){
    $strValida  = "Corrija essas informações antes de prosseguir:<br />$strValida";
      
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = $strValida;
  } else {
    $data = array(
      "grp_gru_id" => $gru_id,
      "grp_usu_id" => $usu_id,
      "grp_ativo"  => (int) 1,
    );

    $CI = pega_instancia();
    $CI->load->database();

    if(count($data) > 0){
      $retInsert = $CI->db->insert('tb_grupo_pessoa', $data);
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
  }

  return $arrRetorno;
}