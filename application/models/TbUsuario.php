<?php
function pegaUsuario($usuId, $apenasCamposTabela=false)
{
  $arrRetorno = [];
  $arrRetorno["erro"]    = false;
  $arrRetorno["msg"]     = "";
  $arrRetorno["Usuario"] = [];

  if(!is_numeric($usuId)){
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "ID inválido para buscar Usuário!";

    return $arrRetorno;
  }

  $CI = pega_instancia();
  $CI->load->database();

  $campos = "usu_id, usu_email, usu_senha, usu_nome, usu_foto, usu_sexo, usu_nascimento, usu_telefone, usu_celular, usu_cid_id, usu_ativo, usu_usa_id";
  if(!$apenasCamposTabela){
    $campos .= ",usa_usuario, cid_descricao, est_descricao";
  }
  $CI->db->select($campos);
  $CI->db->from('tb_usuario');
  $CI->db->join('tb_usuario_admin', 'usa_id = usu_usa_id', 'left');
  $CI->db->join('v_tb_cidade', 'cid_id = usu_cid_id', 'left');
  $CI->db->where('usu_id =', $usuId);

  $query = $CI->db->get();
  $row   = $query->row();

  if (!isset($row)) {
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "Erro ao encontrar Usuário!";

    return $arrRetorno;
  }

  $Usuario = [];
  $Usuario["usu_id"]         = $row->usu_id;
  $Usuario["usu_email"]      = $row->usu_email;
  $Usuario["usu_senha"]      = $row->usu_senha;
  $Usuario["usu_nome"]       = $row->usu_nome;
  $Usuario["usu_foto"]       = $row->usu_foto;
  $Usuario["usu_sexo"]       = $row->usu_sexo;
  $Usuario["usu_nascimento"] = $row->usu_nascimento;
  $Usuario["usu_telefone"]   = $row->usu_telefone;
  $Usuario["usu_celular"]    = $row->usu_celular;
  $Usuario["usu_cid_id"]     = $row->usu_cid_id;
  $Usuario["usu_ativo"]      = $row->usu_ativo;
  $Usuario["usu_usa_id"]     = $row->usu_usa_id;
  if(!$apenasCamposTabela){
    $Usuario["usa_usuario"]   = $row->usa_usuario;
    $Usuario["cid_descricao"] = $row->cid_descricao;
    $Usuario["est_descricao"] = $row->est_descricao;
  }

  $arrRetorno["msg"]      = "Usuário encontrado com sucesso!";
  $arrRetorno["Usuario"]  = $Usuario;
  return $arrRetorno;
}

function pegaListaUsuario($detalhes=false, $edicao=false, $exclusao=false)
{
  $CI = pega_instancia();
  $CI->load->database();

  require_once(FCPATH."/assets/Lista_CI/Lista_CI.php");
  $Lista_CI = new Lista_CII($CI->db);
  $Lista_CI->setId("ListaUsuario");
  $Lista_CI->addField("usu_id AS id");
  $Lista_CI->addField("usu_nome AS \"Nome\"", "L");
  $Lista_CI->addField("usu_email AS \"Email\"", "L");
  $Lista_CI->addField("ativo AS \"Ativo\"");
  $Lista_CI->addField("REPLACE('<a href=\"javascript:;\" onclick=\"jsonAlteraSenha(''Usuario'', ''jsonUsuarioAlteraSenha'', {usu_id})\"><i class=\"material-icons text-info\">vpn_key</i></a>', '{usu_id}', usu_id) AS \"Alterar Senha\" ", "C", "8%");
  if($detalhes){
    $url = base_url() . "Usuario/visualizar";
    $Lista_CI->addField(" CONCAT('<a href=\"$url/', usu_id, '\"><i class=\"material-icons text-info\">visibility</i></a>') AS \"Visualizar\" ", "C", "3%");
  }
  if($edicao){
    $url = base_url() . "Usuario/editar";
    $Lista_CI->addField(" CONCAT('<a href=\"$url/', usu_id, '\"><i class=\"material-icons text-info\">create</i></a>') AS \"Editar\" ", "C", "3%");
  }
  if($exclusao){
    //delete
    //block
  }
  $Lista_CI->addFrom("v_tb_usuario");
  $Lista_CI->changeOrderCol(2);

  $Lista_CI->addFilter("usu_id", "id", "numeric");
  $Lista_CI->addFilter("usu_nome", "Nome");
  $Lista_CI->addFilter("usu_email", "Email");
  $Lista_CI->addFilter("ativo", "Ativo");

  return $Lista_CI->getHtml();
}

function validaInsereUsuario($Usuario)
{
  require_once(APPPATH."/helpers/utils_helper.php");
  $strValida = "";

  // validacao basica dos campos
  $vNome = $Usuario["usu_nome"] ?? "";
  if(strlen($vNome) <= 2){
    $strValida .= "<br />&nbsp;&nbsp;* Informe um nome válido (entre 3 e 100 caracteres).";
  }

  $vEmail = $Usuario["usu_email"] ?? "";
  if(!valida_email($vEmail)){
    $strValida .= "<br />&nbsp;&nbsp;* Informe um email válido.";
  }

  $vAtivo = $Usuario["usu_ativo"] ?? "";
  if(!($vAtivo == 0 || $vAtivo == 1)){
    $strValida .= "<br />&nbsp;&nbsp;* Informação 'ativo' é inválida.";
  }
  
  $vSenha = $Usuario["usu_senha"] ?? "";
  $ret    = valida_senha($vSenha);
  if($ret["erro"]){
    $strValida .= "<br />&nbsp;&nbsp;* " . $ret["msg"];
  }

  $vSexo = $Usuario["usu_sexo"] ?? "";
  if($vSexo != "" && $vSexo != "M" && $vSexo != "F"){
    $strValida .= "<br />&nbsp;&nbsp;* Informação 'sexo' é inválida.";
  }
  
  $vNascimento = $Usuario["usu_nascimento"] ?? "";
  if($vNascimento != "" && !isValidDate($vNascimento, 'Y-m-d')){
    $strValida .= "<br />&nbsp;&nbsp;* Informação 'nascimento' é inválida.";
  }
  
  $vCidId = $Usuario["usu_cid_id"] ?? "";
  if(!$vCidId>0){
    $strValida .= "<br />&nbsp;&nbsp;* Informe uma cidade válida.";
  }

  $vUsaId = $Usuario["usu_usa_id"] ?? "";
  if(!is_numeric($vUsaId)){
    $strValida .= "<br />&nbsp;&nbsp;* Informação 'usuário de cadastro' é inválida.";
  }
  // ===========================

  // email cadastrado
  $CI = pega_instancia();
  $CI->load->database();

  $CI->db->select('COUNT(*) AS cnt');
  $CI->db->from('tb_usuario');
  $CI->db->where('usu_email =', $vEmail);

  $query = $CI->db->get();
  $row   = $query->row();
  if (!isset($row) || $row->cnt > 0) {
    $strValida .= "<br />&nbsp;&nbsp;* Você já tem um usuário cadastrada com o email " . $vEmail;
  }
  // ================

  if($strValida != ""){
    $strValida  = "Corrija essas informações antes de prosseguir:<br />$strValida";
  }

  return $strValida;
}

function insereUsuario($Usuario)
{
  $arrRetorno          = [];
  $arrRetorno["erro"]  = false;
  $arrRetorno["msg"]   = "";
  $arrRetorno["usuId"] = "";

  $strValida = validaInsereUsuario($Usuario);
  if($strValida != ""){
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = $strValida;

    return $arrRetorno;
  }

  $vNome       = $Usuario["usu_nome"] ?? "";
  $vEmail      = $Usuario["usu_email"] ?? "";
  $vAtivo      = $Usuario["usu_ativo"] ?? 1;
  $vSenha      = $Usuario["usu_senha"] ?? "";
  $vNascimento = $Usuario["usu_nascimento"] ?? NULL;
  $vTelefone   = $Usuario["usu_telefone"] ?? NULL;
  $vCelular    = $Usuario["usu_celular"] ?? NULL;
  $vSexo       = $Usuario["usu_sexo"] ?? NULL;
  $vCidId      = $Usuario["usu_cid_id"] ?? NULL;
  $vUsaId      = $Usuario["usu_usa_id"] ?? "";

  $CI = pega_instancia();
  $CI->load->database();

  $data = array(
    "usu_nome"       => $vNome,
    "usu_email"      => $vEmail,
    "usu_ativo"      => $vAtivo,
    "usu_senha"      => encripta_string($vSenha),
    "usu_nascimento" => $vNascimento,
    "usu_telefone"   => $vTelefone,
    "usu_celular"    => $vCelular,
    "usu_sexo"       => $vSexo,
    "usu_cid_id"     => $vCidId,
    "usu_usa_id"     => $vUsaId,
  );
  $ret = $CI->db->insert('tb_usuario', $data);

  if(!$ret){
    $error = $CI->db->error();

    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "Erro ao inserir Usuário. Mensagem: " . $error["message"];
  } else {
    $arrRetorno["erro"]  = false;
    $arrRetorno["msg"]   = "Usuário inserido com sucesso.";
    $arrRetorno["usuId"] = $CI->db->insert_id();
  }

  return $arrRetorno;
}

function validaEditaUsuario($Usuario)
{
  require_once(APPPATH."/helpers/utils_helper.php");
  $strValida = "";

  // validacao basica dos campos
  $vId = $Usuario["usu_id"] ?? "";
  if(!is_numeric($vId)){
    $strValida .= "<br />&nbsp;&nbsp;* ID inválido para editar Usuário.";
  }

  $vNome = $Usuario["usu_nome"] ?? "";
  if(strlen($vNome) <= 2){
    $strValida .= "<br />&nbsp;&nbsp;* Informe um nome válido (entre 3 e 100 caracteres).";
  }

  $vEmail = $Usuario["usu_email"] ?? "";
  if(!valida_email($vEmail)){
    $strValida .= "<br />&nbsp;&nbsp;* Informação um email válido.";
  }

  $vSexo = $Usuario["usu_sexo"] ?? "";
  if($vSexo != "" && $vSexo != "M" && $vSexo != "F"){
    $strValida .= "<br />&nbsp;&nbsp;* Informação 'sexo' é inválida.";
  }

  $vNascimento = $Usuario["usu_nascimento"] ?? "";
  if($vNascimento != "" && !isValidDate($vNascimento, 'Y-m-d')){
    $strValida .= "<br />&nbsp;&nbsp;* Informação 'nascimento' é inválida.";
  }

  $vCidId = $Usuario["usu_cid_id"] ?? "";
  if(!$vCidId>0){
    $strValida .= "<br />&nbsp;&nbsp;* Informe uma cidade válida.";
  }

  $vAtivo = $Usuario["usu_ativo"] ?? "";
  if(!($vAtivo == 0 || $vAtivo == 1)){
    $strValida .= "<br />&nbsp;&nbsp;* Informação 'ativo' é inválida.";
  }

  $vUsaId = $Usuario["usu_usa_id"] ?? "";
  if(!is_numeric($vUsaId)){
    $strValida .= "<br />&nbsp;&nbsp;* Informação 'usuário de cadastro' é inválida.";
  }
  // ===========================

  // email cadastrado
  $CI = pega_instancia();
  $CI->load->database();

  $CI->db->select('COUNT(*) AS cnt');
  $CI->db->from('tb_usuario');
  $CI->db->where('usu_email =', $vEmail);
  $CI->db->where('usu_id <>', $vId);

  $query = $CI->db->get();
  $row   = $query->row();
  if (!isset($row) || $row->cnt > 0) {
    $strValida .= "<br />&nbsp;&nbsp;* Você já tem um usuário cadastrada com o email " . $vEmail;
  }
  // ================

  if($strValida != ""){
    $strValida  = "Corrija essas informações antes de prosseguir:<br />$strValida";
  }

  return $strValida;
}

function editaUsuario($Usuario)
{
  $arrRetorno          = [];
  $arrRetorno["erro"]  = false;
  $arrRetorno["msg"]   = "";

  // carrega info do BD
  $vId = $Usuario["usu_id"] ?? "";
  $retP = pegaUsuario($vId, true);
  if($retP["erro"]){
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = $retP["msg"];
    return $arrRetorno;
  }
  $data = $retP["Usuario"];
  foreach($Usuario as $field_name => $field_value){
    if(array_key_exists($field_name, $data)){
      $data[$field_name] = $field_value;
    }
  }
  // ==================

  $strValida = validaEditaUsuario($data);
  if($strValida != ""){
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = $strValida;
    return $arrRetorno;
  }

  $CI = pega_instancia();
  $CI->load->database();

  $CI->db->where('usu_id', $vId);
  $ret = $CI->db->update('tb_usuario', $data);

  if(!$ret){
    $error = $CI->db->error();

    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "Erro ao editar Usuário. Mensagem: " . $error["message"];
  } else {
    $arrRetorno["erro"]  = false;
    $arrRetorno["msg"]   = "Usuário editado com sucesso.";
  }

  return $arrRetorno;
}

function alteraSenhaUsuario($usuId, $novaSenha)
{
  $arrRetorno          = [];
  $arrRetorno["erro"]  = false;
  $arrRetorno["msg"]   = "";

  // validacao basica dos campos
  if(!is_numeric($usuId)){
    $arrRetorno["erro"]  = true;
    $arrRetorno["msg"]   = "ID inválido para alterar senha do usuário!";

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
    "usu_senha" => encripta_string($novaSenha)
  );
  $CI->db->where('usu_id', $usuId);
  $retSenha = $CI->db->update('tb_usuario', $data);

  if(!$retSenha){
    $error = $CI->db->error();

    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "Erro ao alterar senha do usuário. Mensagem: " . $error["message"];
  } else {
    $arrRetorno["erro"] = false;
    $arrRetorno["msg"]  = "Senha do usuário alterada com sucesso.";
  }

  return $arrRetorno;
}

function pegaTotalPessoasAtivas($usuId)
{
  $arrRetorno          = [];
  $arrRetorno["erro"]  = false;
  $arrRetorno["msg"]   = "";
  $arrRetorno["total"] = "";

  if(!is_numeric($usuId)){
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "ID inválido para buscar total de pessoas ativas!";
    return $arrRetorno;
  }

  $CI = pega_instancia();
  $CI->load->database();

  $CI->db->select('COUNT(*) AS cnt');
  $CI->db->from('tb_pessoa');
  $CI->db->where('pes_usu_id =', $usuId);
  $CI->db->where('pes_ativo =', 1);

  $query    = $CI->db->get();
  $row      = $query->row();
  $totAtivo = $row->cnt;

  $arrRetorno["total"] = $totAtivo;
  return $arrRetorno;
}