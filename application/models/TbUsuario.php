<?php
function pegaUsuario($usuId)
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

  $CI->db->select('usu_id, usu_email, usu_senha, usu_nome, usu_ativo, usu_usa_id, usa_usuario');
  $CI->db->from('tb_usuario');
  $CI->db->join('tb_usuario_admin', 'usa_id = usu_usa_id', 'left');
  $CI->db->where('usu_id =', $usuId);

  $query = $CI->db->get();
  $row   = $query->row();

  if (!isset($row)) {
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "Erro ao encontrar Usuário!";

    return $arrRetorno;
  }

  $Usuario = [];
  $Usuario["usu_id"]      = $row->usu_id;
  $Usuario["usu_email"]   = $row->usu_email;
  $Usuario["usu_senha"]   = $row->usu_senha;
  $Usuario["usu_nome"]    = $row->usu_nome;
  $Usuario["usu_ativo"]   = $row->usu_ativo;
  $Usuario["usu_usa_id"]  = $row->usu_usa_id;
  $Usuario["usa_usuario"] = $row->usa_usuario;

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
  if($detalhes){
    $url = base_url() . "Usuario/visualizar";
    $Lista_CI->addField(" CONCAT('<a href=\"$url/', usu_id, '\"><i class=\"material-icons text-success\">visibility</i></a>') AS \"Visualizar\" ", "C", "3%");
  }
  if($edicao){
    $url = base_url() . "Usuario/editar";
    $Lista_CI->addField(" CONCAT('<a href=\"$url/', usu_id, '\"><i class=\"material-icons text-success\">create</i></a>') AS \"Editar\" ", "C", "3%");
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

  $vNome = $Usuario["usu_nome"] ?? "";
  if(strlen($vNome) <= 2){
    $strValida .= "<br />&nbsp;&nbsp;* Informe um nome válido (entre 3 e 100 caracteres).";
  }

  $vEmail = $Usuario["usu_email"] ?? "";
  if(!valida_email($vEmail)){
    $strValida .= "<br />&nbsp;&nbsp;* Informação um email válido.";
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

  $vUsaId = $Usuario["usu_usa_id"] ?? "";
  if(!is_numeric($vUsaId)){
    $strValida .= "<br />&nbsp;&nbsp;* Informação 'usuário de cadastro' é inválida.";
  }

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

  $vNome  = $Usuario["usu_nome"] ?? "";
  $vEmail = $Usuario["usu_email"] ?? "";
  $vAtivo = $Usuario["usu_ativo"] ?? 1;
  $vSenha = $Usuario["usu_senha"] ?? "";
  $vUsaId = $Usuario["usu_usa_id"] ?? "";

  $CI = pega_instancia();
  $CI->load->database();

  $data = array(
    "usu_nome"   => $vNome,
    "usu_email"  => $vEmail,
    "usu_ativo"  => $vAtivo,
    "usu_senha"  => encripta_string($vSenha),
    "usu_usa_id" => $vUsaId,
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
