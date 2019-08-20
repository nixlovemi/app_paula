<?php
require_once(APPPATH."/helpers/utils_helper.php");

/**
 * aqui só altera pessoa ou usuário
 */
function salvaConfigPessoaUsuario($arrInfo)
{
  $vNome          = $arrInfo["nome"] ?? "";
  $vNovaSenha     = $arrInfo["nova_senha"] ?? "";
  $vRepitaSenha   = $arrInfo["repita_senha"] ?? "";
  $vAnteriorSenha = $arrInfo["anterior_senha"] ?? "";

  $UsuarioLog     = $_SESSION["usuario_info"] ?? array();
  $ehCliente      = $UsuarioLog->cliente > 0;
  $idLogado       = $UsuarioLog->id ?? "";

  if($ehCliente){
    require_once(APPPATH."/models/TbPessoa.php");
    $ret                = pegaPessoa($idLogado);
    $Pessoa             = ($ret["erro"]) ? array(): $ret["Pessoa"];
    $Pessoa["pes_nome"] = $vNome;
    $vSenhaAtual        = $Pessoa["pes_senha"];
  } else {
    require_once(APPPATH."/models/TbUsuario.php");
    $ret                 = pegaUsuario($idLogado);
    $Usuario             = ($ret["erro"]) ? array(): $ret["Usuario"];
    $Usuario["usu_nome"] = $vNome;
    $vSenhaAtual         = $Usuario["usu_senha"];
  }

  // checagem da nova senha
  if($vNovaSenha!="" && $vRepitaSenha!="" && $vAnteriorSenha!=""){
    $retValidaSenha = valida_senha($vNovaSenha);
    if($retValidaSenha["erro"]){
      return array(
        "erro" => true,
        "msg"  => $retValidaSenha["msg"]
      );
    } else {
      $senhasIguais  = ($vNovaSenha == $vRepitaSenha);
      $senhaAnterior = (encripta_string($vAnteriorSenha) == $vSenhaAtual);

      if(!$senhasIguais){
        return array(
          "erro" => true,
          "msg"  => "A senha digitada e a senha repetida não são iguais!"
        );
      } else if(!$senhaAnterior) {
        return array(
          "erro" => true,
          "msg"  => "A senha anterior não confere com a registrada no sistema!"
        );
      } else {
        if($ehCliente){
          $Pessoa["pes_senha"] = encripta_string($vNovaSenha);
        } else {
          $Usuario["usu_senha"] = encripta_string($vNovaSenha);
        }
      }
    }
  }
  // ======================

  if($ehCliente){
    $retEditar = editaPessoa($Pessoa, false);
    $urlRet    = "GrpConfig";
  } else {
    $retEditar = editaUsuario($Usuario, false);
    $urlRet    = "UsuConfig";
  }

  return $retEditar;
}