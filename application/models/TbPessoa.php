<?php
//!! lembrar de exibir apenas pra pessoa logada
//=============================================

function pegaPessoa($pesId, $apenasCamposTabela=false)
{
  $arrRetorno = [];
  $arrRetorno["erro"]   = false;
  $arrRetorno["msg"]    = "";
  $arrRetorno["Pessoa"] = [];

  if(!is_numeric($pesId)){
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "ID inválido para buscar Pessoa!";

    return $arrRetorno;
  }

  $CI = pega_instancia();
  $CI->load->database();

  // so exibe de quem cadastrou
  $UsuarioLog = $CI->session->usuario_info ?? array();
  // ==========================

  $camposTabela = "pes_id, pes_usu_id, pes_pet_id, pes_nome, pes_email, pes_senha, pes_foto, pes_ativo";
  if(!$apenasCamposTabela){
    $camposTabela .= ", usu_nome, pet_descricao";
  }

  $CI->db->select($camposTabela);
  $CI->db->from('tb_pessoa');
  $CI->db->join('tb_usuario', 'usu_id = pes_usu_id', 'left');
  $CI->db->join('tb_pessoa_tipo', 'pet_id = pes_pet_id', 'left');
  $CI->db->where('pes_id =', $pesId);
  if($UsuarioLog->admin == 0){
    $CI->db->where('pes_usu_id =', $UsuarioLog->id);
  }

  $query = $CI->db->get();
  $row   = $query->row();

  if (!isset($row)) {
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "Erro ao encontrar Pessoa!";

    return $arrRetorno;
  }

  $Pessoa = [];
  $Pessoa["pes_id"]        = $row->pes_id;
  $Pessoa["pes_usu_id"]    = $row->pes_usu_id;
  $Pessoa["pes_pet_id"]    = $row->pes_pet_id;
  $Pessoa["pes_nome"]      = $row->pes_nome;
  $Pessoa["pes_email"]     = $row->pes_email;
  $Pessoa["pes_senha"]     = $row->pes_senha;
  $Pessoa["pes_foto"]      = $row->pes_foto;
  $Pessoa["pes_ativo"]     = $row->pes_ativo;
  if(!$apenasCamposTabela){
    $Pessoa["usu_nome"]      = $row->usu_nome;
    $Pessoa["pet_descricao"] = $row->pet_descricao;
  }

  $arrRetorno["msg"]    = "Pessoa encontrado com sucesso!";
  $arrRetorno["Pessoa"] = $Pessoa;
  return $arrRetorno;
}

function pegaTodasPessoas($filtro)
{
  $arrRetorno = [];
  $arrRetorno["erro"]     = false;
  $arrRetorno["msg"]      = "";
  $arrRetorno["arrGrupo"] = [];

  $usuId = $filtro["pes_usu_id"] ?? "";
  $ativo = $filtro["pes_ativo"] ?? 1;

  $CI = pega_instancia();
  $CI->load->database();

  $CI->db->select('pes_id, pes_usu_id, pes_pet_id, pes_nome, pes_email, pes_senha, pes_foto, pes_ativo, usu_nome, pet_descricao');
  $CI->db->from('tb_pessoa');
  $CI->db->join('tb_usuario', 'usu_id = pes_usu_id', 'left');
  $CI->db->join('tb_pessoa_tipo', 'pet_id = pes_pet_id', 'left');
  if($usuId != ""){
    $CI->db->where('pes_usu_id =', $usuId);
  }
  if($ativo != ""){
    $CI->db->where('pes_ativo =', $ativo);
  }
  $CI->db->order_by('pes_nome', 'ASC');

  $query = $CI->db->get();
  if(!$query){
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "Erro ao retornar todos as Pessoas!";
  } else {
    foreach ($query->result() as $row){
      $Pessoa = [];
      $Pessoa["pes_id"]        = $row->pes_id;
      $Pessoa["pes_usu_id"]    = $row->pes_usu_id;
      $Pessoa["pes_pet_id"]    = $row->pes_pet_id;
      $Pessoa["pes_nome"]      = $row->pes_nome;
      $Pessoa["pes_email"]     = $row->pes_email;
      $Pessoa["pes_email"]     = $row->pes_email;
      $Pessoa["pes_foto"]      = $row->pes_foto;
      $Pessoa["pes_ativo"]     = $row->pes_ativo;
      $Pessoa["usu_nome"]      = $row->usu_nome;
      $Pessoa["pet_descricao"] = $row->pet_descricao;

      $arrRetorno["arrGrupo"][] = $Pessoa;
    }
  }

  return $arrRetorno;
}

function pegaListaPessoa($detalhes=false, $edicao=false, $exclusao=false)
{
  $CI = pega_instancia();
  $CI->load->database();

  // so exibe de quem cadastrou
  $UsuarioLog = $CI->session->usuario_info ?? array();
  // ==========================

  require_once(FCPATH."/assets/Lista_CI/Lista_CI.php");
  $Lista_CI = new Lista_CII($CI->db);
  $Lista_CI->setId("ListaPessoa");
  $Lista_CI->addField("pes_id AS id");
  $Lista_CI->addField("pes_nome AS \"Nome\"", "L");
  $Lista_CI->addField("pes_email AS \"Email\"", "L");
  $Lista_CI->addField("pet_descricao AS \"Tipo\"", "L");
  $Lista_CI->addField("ativo AS \"Ativo\"");
  $Lista_CI->addField("REPLACE('<a href=\"javascript:;\" onclick=\"jsonAlteraSenha(''Pessoa'', ''jsonPessoaAlteraSenha'', {pes_id})\"><i class=\"material-icons text-success\">vpn_key</i></a>', '{pes_id}', pes_id) AS \"Alterar Senha\" ", "C", "8%");
  if($detalhes){
    $url = base_url() . "Pessoa/visualizar/{pes_id}";
    $Lista_CI->addField("REPLACE('<a href=\"$url\"><i class=\"material-icons text-success\">visibility</i></a>', '{pes_id}', pes_id) AS \"Visualizar\" ", "C", "3%");
  }
  if($edicao){
    $url = base_url() . "Pessoa/editar/{pes_id}";
    $Lista_CI->addField("REPLACE('<a href=\"$url\"><i class=\"material-icons text-success\">create</i></a>', '{pes_id}', pes_id) AS \"Editar\" ", "C", "3%");
  }
  if($exclusao){
  }
  $Lista_CI->addFrom("v_tb_pessoa");
  
  if($UsuarioLog->admin == 0){
    $Lista_CI->addWhere("pes_usu_id = " . $UsuarioLog->id);
  }
  $Lista_CI->changeOrderCol(2);

  $Lista_CI->addFilter("pes_id", "id", "numeric");
  $Lista_CI->addFilter("pes_nome", "Nome");
  $Lista_CI->addFilter("pes_email", "Email");
  $Lista_CI->addFilter("pet_descricao", "Tipo");
  $Lista_CI->addFilter("ativo", "Ativo");

  return $Lista_CI->getHtml();
}

function validaInserePessoa($Pessoa)
{
  require_once(APPPATH."/helpers/utils_helper.php");
  $strValida = "";

  // validacao basica dos campos
  $vUsuId = $Pessoa["pes_usu_id"] ?? "";
  if(!is_numeric($vUsuId)){
    $strValida .= "<br />&nbsp;&nbsp;* Informação 'usuário de cadastro' é inválida.";
  }

  $vTipo = $Pessoa["pes_pet_id"] ?? "";
  if(!is_numeric($vTipo)){
    $strValida .= "<br />&nbsp;&nbsp;* Informe um tipo para a pessoa.";
  }

  $vNome = $Pessoa["pes_nome"] ?? "";
  if(strlen($vNome) <= 2){
    $strValida .= "<br />&nbsp;&nbsp;* Informe um nome válido (entre 3 e 100 caracteres).";
  }

  $vEmail = $Pessoa["pes_email"] ?? "";
  if(!valida_email($vEmail)){
    $strValida .= "<br />&nbsp;&nbsp;* Informação um email válido.";
  }

  $vSenha = $Pessoa["pes_senha"] ?? "";
  $ret    = valida_senha($vSenha);
  if($ret["erro"]){
    $strValida .= "<br />&nbsp;&nbsp;* " . $ret["msg"];
  }

  $vAtivo = $Pessoa["pes_ativo"] ?? "";
  if(!($vAtivo == 0 || $vAtivo == 1)){
    $strValida .= "<br />&nbsp;&nbsp;* Informação 'ativo' é inválida.";
  }
  // ===========================

  // valida limite ativos
  require_once(APPPATH."/models/TbUsuarioCfg.php");
  $retLim = pegaMaximoUsuarios($vUsuId);
  if($retLim["erro"]){
    $strValida .= "<br />&nbsp;&nbsp;* " . $retLim["msg"];
  } else {
    $maxUsuarios = $retLim["valor"];

    require_once(APPPATH."/models/TbUsuario.php");
    $retTot   = pegaTotalPessoasAtivas($vUsuId);
    $totAtivo = ($retTot["erro"]) ? 999: $retTot["total"];

    if($totAtivo > $maxUsuarios){
      $strValida .= "<br />&nbsp;&nbsp;* Você não pode cadastrar mais pessoas pois seu limite é " . $maxUsuarios;
    }
  }
  // ====================

  // valida mesmo email pro msm usuario
  $CI = pega_instancia();
  $CI->load->database();

  $CI->db->select('COUNT(*) AS cnt');
  $CI->db->from('tb_pessoa');
  $CI->db->where('pes_usu_id =', $vUsuId);
  $CI->db->where('pes_email =', $vEmail);

  $query = $CI->db->get();
  $row   = $query->row();
  if (!isset($row) || $row->cnt > 0) {
    $strValida .= "<br />&nbsp;&nbsp;* Você já tem uma pessoa cadastrada com o email " . $vEmail;
  }
  // ==================================

  if($strValida != ""){
    $strValida  = "Corrija essas informações antes de prosseguir:<br />$strValida";
  }

  return $strValida;
}

function inserePessoa($Pessoa)
{
  $arrRetorno          = [];
  $arrRetorno["erro"]  = false;
  $arrRetorno["msg"]   = "";
  $arrRetorno["pesId"] = "";

  $strValida = validaInserePessoa($Pessoa);
  if($strValida != ""){
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = $strValida;

    return $arrRetorno;
  }	 	 	 	 	 	 

  $vUsuId = $Pessoa["pes_usu_id"] ?? NULL;
  $vPetId = $Pessoa["pes_pet_id"] ?? NULL;
  $vNome  = $Pessoa["pes_nome"] ?? "";
  $vEmail = $Pessoa["pes_email"] ?? "";
  $vSenha = $Pessoa["pes_senha"] ?? "";
  $vFoto  = $Pessoa["pes_foto"] ?? NULL;
  $vAtivo = $Pessoa["pes_ativo"] ?? 1;

  $CI = pega_instancia();
  $CI->load->database();

  $data = array(
    "pes_usu_id" => $vUsuId,
    "pes_pet_id" => $vPetId,
    "pes_nome"   => $vNome,
    "pes_email"  => $vEmail,
    "pes_senha"  => encripta_string($vSenha),
    "pes_foto"   => $vFoto,
    "pes_ativo"  => $vAtivo,
  );
  $ret = $CI->db->insert('tb_pessoa', $data);

  if(!$ret){
    $error = $CI->db->error();

    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "Erro ao inserir Pessoa. Mensagem: " . $error["message"];
  } else {
    $arrRetorno["erro"]  = false;
    $arrRetorno["msg"]   = "Pessoa inserida com sucesso.";
    $arrRetorno["pesId"] = $CI->db->insert_id();
  }

  return $arrRetorno;
}

function validaEditaPessoa($Pessoa)
{
  require_once(APPPATH."/helpers/utils_helper.php");
  $strValida = "";

  // validacao basica dos campos
  $vId = $Pessoa["pes_id"] ?? "";
  if(!is_numeric($vId)){
    $strValida .= "<br />&nbsp;&nbsp;* ID inválido para editar Pessoa.";
  }

  $vUsuId = $Pessoa["pes_usu_id"] ?? "";
  if(!is_numeric($vUsuId)){
    $strValida .= "<br />&nbsp;&nbsp;* Informação 'usuário de cadastro' é inválida.";
  }

  $vTipo = $Pessoa["pes_pet_id"] ?? "";
  if(!is_numeric($vTipo)){
    $strValida .= "<br />&nbsp;&nbsp;* Informe um tipo para a pessoa.";
  }

  $vNome = $Pessoa["pes_nome"] ?? "";
  if(strlen($vNome) <= 2){
    $strValida .= "<br />&nbsp;&nbsp;* Informe um nome válido (entre 3 e 100 caracteres).";
  }

  $vEmail = $Pessoa["pes_email"] ?? "";
  if(!valida_email($vEmail)){
    $strValida .= "<br />&nbsp;&nbsp;* Informação um email válido.";
  }

  $vAtivo = $Pessoa["pes_ativo"] ?? "";
  if(!($vAtivo == 0 || $vAtivo == 1)){
    $strValida .= "<br />&nbsp;&nbsp;* Informação 'ativo' é inválida.";
  }
  // ===========================

  // valida limite ativos
  $retP   = pegaPessoa($vId);
  if($retP["erro"]){
    $strValida .= "<br />&nbsp;&nbsp;* " . $retLim["msg"];
  } else {
    $Pessoa = $retP["Pessoa"];

    #estava inativo e agora vai pra ativo
    if($Pessoa["pes_ativo"] == 0 && $vAtivo == 1){
      require_once(APPPATH."/models/TbUsuarioCfg.php");
      $retLim = pegaMaximoUsuarios($vUsuId);
      if($retLim["erro"]){
        $strValida .= "<br />&nbsp;&nbsp;* " . $retLim["msg"];
      } else {
        $maxUsuarios = $retLim["valor"];

        require_once(APPPATH."/models/TbUsuario.php");
        $retTot   = pegaTotalPessoasAtivas($vUsuId);
        $totAtivo = ($retTot["erro"]) ? 999: $retTot["total"];

        if($totAtivo > $maxUsuarios){
          $strValida .= "<br />&nbsp;&nbsp;* Você não pode cadastrar mais pessoas pois seu limite é " . $maxUsuarios;
        }
      }
    }
  }
  // ====================

  // valida mesmo email pro msm usuario
  $CI = pega_instancia();
  $CI->load->database();

  $CI->db->select('COUNT(*) AS cnt');
  $CI->db->from('tb_pessoa');
  $CI->db->where('pes_usu_id =', $vUsuId);
  $CI->db->where('pes_email =', $vEmail);
  $CI->db->where('pes_id <>', $vId);

  $query = $CI->db->get();
  $row   = $query->row();
  if (!isset($row) || $row->cnt > 0) {
    $strValida .= "<br />&nbsp;&nbsp;* Você já tem uma pessoa cadastrada com o email " . $vEmail;
  }
  // ==================================

  if($strValida != ""){
    $strValida  = "Corrija essas informações antes de prosseguir:<br />$strValida";
  }

  return $strValida;
}

function editaPessoa($Pessoa)
{
  $arrRetorno          = [];
  $arrRetorno["erro"]  = false;
  $arrRetorno["msg"]   = "";

  // carrega info do BD
  $vId = $Pessoa["pes_id"] ?? "";
  $retP = pegaPessoa($vId, true);
  if($retP["erro"]){
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = $retP["msg"];
    return $arrRetorno;
  }
  $data = $retP["Pessoa"];
  foreach($Pessoa as $field_name => $field_value){
    if(array_key_exists($field_name, $data)){
      $data[$field_name] = $field_value;
    }
  }
  // ==================

  $strValida = validaEditaPessoa($data);
  if($strValida != ""){
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = $strValida;
    return $arrRetorno;
  }

  $CI = pega_instancia();
  $CI->load->database();

  $CI->db->where('pes_id', $vId);
  $ret = $CI->db->update('tb_pessoa', $data);

  if(!$ret){
    $error = $CI->db->error();

    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "Erro ao editar Pessoa. Mensagem: " . $error["message"];
  } else {
    $arrRetorno["erro"]  = false;
    $arrRetorno["msg"]   = "Pessoa editada com sucesso.";
  }

  return $arrRetorno;
}

function alteraSenhaPessoa($pesId, $novaSenha)
{
  $arrRetorno          = [];
  $arrRetorno["erro"]  = false;
  $arrRetorno["msg"]   = "";

  // validacao basica dos campos
  if(!is_numeric($pesId)){
    $arrRetorno["erro"]  = true;
    $arrRetorno["msg"]   = "ID inválido para alterar senha da pessoa!";

    return $arrRetorno;
  }

  require_once(APPPATH."/helpers/utils_helper.php");
  $ret = valida_senha($novaSenha);
  if($ret["erro"]){
    $arrRetorno["erro"]  = true;
    $arrRetorno["msg"]   = $ret["msg"];

    return $arrRetorno;
  }
  // ===========================

  $CI = pega_instancia();
  $CI->load->database();

  $data = array(
    "pes_senha" => encripta_string($novaSenha)
  );
  $CI->db->where('pes_id', $pesId);
  $retSenha = $CI->db->update('tb_pessoa', $data);

  if(!$retSenha){
    $error = $CI->db->error();

    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "Erro ao alterar senha da pessoa. Mensagem: " . $error["message"];
  } else {
    $arrRetorno["erro"] = false;
    $arrRetorno["msg"]  = "Senha da pessoa alterada com sucesso.";
  }

  return $arrRetorno;
}