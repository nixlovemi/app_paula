<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class GrpConfig extends MY_Controller
{
  var $cliente_logado;

  public function __construct()
  {
      $admin = false;
      $grupo = true;
      parent::__construct($admin, $grupo);
      $this->load->helper("utils_helper");

      $this->cliente_logado = $this->session->usuario_info ?? array();
  }

  public function index()
  {
    $UsuarioLog = $_SESSION["usuario_info"] ?? array();
    $pesFoto    = pegaFotoLogado();
    $pesNome    = $UsuarioLog->usuario ?? "";
    $pesEmail   = $UsuarioLog->email ?? "";

    $this->template->load(TEMPLATE_STR, 'GrpConfig/index', array(
      "titulo"   => gera_titulo_template("Configuração"),
      "pesFoto"  => $pesFoto,
      "pesNome"  => $pesNome,
      "pesEmail" => $pesEmail,
      "postUrl"  => BASE_URL . "GrpConfig/postIndex",
      "voltaUrl" => BASE_URL . "SisGrupo",
    ));
  }

  public function postIndex()
  {
    $variaveisPost  = processaPost();
    $vNome          = $variaveisPost->nome ?? "";
    $vNovaSenha     = $variaveisPost->nova_senha ?? "";
    $vRepitaSenha   = $variaveisPost->repita_senha ?? "";
    $vAnteriorSenha = $variaveisPost->anterior_senha ?? "";

    require_once(APPPATH."/models/Config.php");
    $ret = salvaConfigPessoaUsuario(array(
      "nome"           => $vNome,
      "nova_senha"     => $vNovaSenha,
      "repita_senha"   => $vRepitaSenha,
      "anterior_senha" => $vAnteriorSenha,
    ));

    if($ret["erro"]){
      geraNotificacao("Aviso!", $ret["msg"], "warning");
    } else {
      $UsuarioLog               = $_SESSION["usuario_info"] ?? array();
      $UsuarioLog->usuario      = $vNome;
      $UsuarioLog->senha        = $vNovaSenha;
      $_SESSION["usuario_info"] = $UsuarioLog;

      geraNotificacao("Sucesso!", "Informações atualizadas!", "success");
    }

    redirect(BASE_URL . 'GrpConfig');
  }
}