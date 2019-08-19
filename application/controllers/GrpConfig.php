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
    ));
  }
  public function postIndex()
  {
    $variaveisPost  = processaPost();
    $vNome          = $variaveisPost->nome ?? "";
    $vNovaSenha     = $variaveisPost->nova_senha ?? "";
    $vRepitaSenha   = $variaveisPost->repita_senha ?? "";
    $vAnteriorSenha = $variaveisPost->anterior_senha ?? "";
    $UsuarioLog     = $_SESSION["usuario_info"] ?? array();
    $pesId          = $UsuarioLog->id ?? "";

    require_once(APPPATH."/models/TbPessoa.php");
    $ret    = pegaPessoa($pesId);
    $Pessoa = $this->session->flashdata('Pessoa') ?? $ret["Pessoa"];
    $Pessoa["pes_nome"]   = $vNome;

    // checagem da nova senha
    if($vNovaSenha!="" && $vRepitaSenha!="" && $vAnteriorSenha!=""){
      $retValidaSenha = valida_senha($vNovaSenha);
      if($retValidaSenha["erro"]){
        geraNotificacao("Aviso!", $retValidaSenha["msg"], "warning");
        redirect(BASE_URL . 'GrpConfig');
      } else {
        $senhasIguais  = ($vNovaSenha == $vRepitaSenha);
        $senhaAnterior = (encripta_string($vAnteriorSenha) == $Pessoa["pes_senha"]);

        if(!$senhasIguais){
          geraNotificacao("Aviso!", "A senha digitada e a senha repetida não são iguais!", "warning");
          redirect(BASE_URL . 'GrpConfig');
        } else if(!$senhaAnterior) {
          geraNotificacao("Aviso!", "A senha anterior não confere com a registrada no sistema!", "warning");
          redirect(BASE_URL . 'GrpConfig');
        } else {
          $Pessoa["pes_senha"] = encripta_string($vNovaSenha);
        }
      }
    }
    // ======================

    require_once(APPPATH."/models/TbPessoa.php");
    $retEditar = editaPessoa($Pessoa, false);

    if($retEditar["erro"]){
      geraNotificacao("Aviso!", $retEditar["msg"], "warning");
      redirect(BASE_URL . 'GrpConfig');
    } else {
      $UsuarioLog->usuario      = $Pessoa["pes_nome"];
      $UsuarioLog->senha        = $Pessoa["pes_senha"];
      $_SESSION["usuario_info"] = $UsuarioLog;
      
      geraNotificacao("Sucesso!", $retEditar["msg"], "success");
      redirect(BASE_URL . 'GrpConfig');
    }
  }

}