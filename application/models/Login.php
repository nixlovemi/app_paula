<?php
require_once(APPPATH."/helpers/utils_helper.php");

function executaLogin($email, $senha, $adminLogin = false)
{
  $arrRet            = [];
  $arrRet["erro"]    = true;
  $arrRet["msg"]     = "";
  $arrRet["infoUsr"] = "";

  // validação ===================
  $retValEmail = valida_email($email);
  if (($retValEmail == false && !$adminLogin) || ($adminLogin && strlen($email) < 1)) {
    $arrRet["erro"] = true;
    $arrRet["msg"]  = "Informe um usuário/email válido!";

    return array_ret_para_retorno($arrRet);
  }

  if (strlen($senha) < 1) {
    $arrRet["erro"] = true;
    $arrRet["msg"]  = "Informe a senha!";

    return array_ret_para_retorno($arrRet);
  }
  // =============================

  $CI = pega_instancia();
  $CI->load->database();

  if ($adminLogin) {
    $CI->db->select('usa_id AS id, usa_usuario AS usuario, usa_senha AS senha, usa_ativo AS ativo, 1 AS admin');
    $CI->db->from('tb_usuario_admin');
    $CI->db->where('usa_usuario =', $email);
    $CI->db->where('usa_senha =', encripta_string($senha));
  } else {
    $CI->db->select('usu_id AS id, usu_email AS usuario, usu_senha AS senha, usu_nome AS nome, usu_ativo AS ativo, 0 AS admin');
    $CI->db->from('tb_usuario');
    $CI->db->where('usu_email =', $email);
    $CI->db->where('usu_senha =', encripta_string($senha));
  }

  $query = $CI->db->get();
  $row   = $query->row();

  if (!isset($row)) {
    $arrRet["erro"] = true;
    $arrRet["msg"]  = "Usuário ou senha inválidos!";

    return array_ret_para_retorno($arrRet);
  }
  if ($row->ativo <> 1) {
    $arrRet["erro"] = true;
    $arrRet["msg"]  = "Usuário está inativo!";

    return array_ret_para_retorno($arrRet);
  }
  if(!$adminLogin){
    require_once(APPPATH."/models/TbUsuarioCfg.php");
    $retVal   = pegaCfgValidade($row->id);
    $validade = ($retVal["erro"]) ? "2000-01-01": $retVal["valor"];

    if($validade < date("Y-m-d")){
      $arrRet["erro"] = true;
      $arrRet["msg"]  = "A validade do seu usuário está expirada! Entre em contato com o suporte.";

      return array_ret_para_retorno($arrRet);
    }
  }

  $arrRet["erro"]    = false;
  $arrRet["msg"]     = "Login executado com sucesso!";
  $arrRet["infoUsr"] = json_encode($row);

  return array_ret_para_retorno($arrRet);
}
