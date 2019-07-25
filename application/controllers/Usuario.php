<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuario extends MY_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->helper("utils_helper");
  }

  public function index()
  {
    require_once(APPPATH."/models/TbUsuario.php");
    $htmlLista = pegaListaUsuario(true, true, true);

    $this->template->load(TEMPLATE_STR, 'TbUsuario/index', array(
      "titulo"    => gera_titulo_template("Usuário"),
      "htmlLista" => $htmlLista,
    ));
  }

  public function novo()
  {
    $Usuario = $this->session->flashdata('Usuario') ?? array();

    $this->template->load(TEMPLATE_STR, 'TbUsuario/novo', array(
      "titulo"  => gera_titulo_template("Usuário - Novo"),
      "Usuario" => $Usuario,
    ));
  }

  public function postNovo()
  {
    $variaveisPost  = processaPost();
    $vNome          = $variaveisPost->nome ?? "";
    $vEmail         = $variaveisPost->email ?? "";
    $vSenha         = $variaveisPost->senha ?? "";

    $Usuario = [];
    $Usuario["usu_nome"]   = $vNome;
    $Usuario["usu_email"]  = $vEmail;
    $Usuario["usu_senha"]  = $vSenha;
    $Usuario["usu_usa_id"] = pegaUsuarioLogadoId();
    $this->session->set_flashdata('Usuario', $Usuario);

    require_once(APPPATH."/models/TbUsuario.php");
    $retInserir = insereUsuario($Usuario);

    if($retInserir["erro"]){
      geraNotificacao("Aviso!", $retInserir["msg"], "warning");
      redirect(BASE_URL . 'Usuario/novo');
    } else {
      geraNotificacao("Sucesso!", $retInserir["msg"], "success");
      redirect(BASE_URL . 'Usuario/editar/' . $retInserir["usuId"]);
    }
  }

  public function visualizar($id)
  {
    require_once(APPPATH."/models/TbUsuario.php");
    $ret = pegaUsuario($id);

    if($ret["erro"]){
      geraNotificacao("Aviso!", $ret["msg"], "warning");
      redirect(BASE_URL . 'Usuario');
    } else {
      $this->template->load(TEMPLATE_STR, 'TbUsuario/visualizar', array(
        "titulo"  => gera_titulo_template("Usuário - Visualizar"),
        "Usuario" => $ret["Usuario"],
      ));
    }
  }
  
  public function editar($id)
  {
    require_once(APPPATH."/models/TbUsuario.php");
    $ret = pegaUsuario($id);
    
    require_once(APPPATH."/models/TbUsuarioCfgTipo.php");
    $filtro              = [];
    $filtro["uct_ativo"] = 1;
    $retUCT              = pegaTodasUsuCfgTipo($filtro);
    $arrUsuCfgTipo       = ($retUCT["erro"]) ? array(): $retUCT["arrUsuarioCfgTipo"];

    if($ret["erro"]){
      geraNotificacao("Aviso!", $ret["msg"], "warning");
      redirect(BASE_URL . 'Usuario');
    } else {
      $Usuario = $this->session->flashdata('Usuario') ?? $ret["Usuario"];

      require_once(APPPATH."/models/TbUsuarioCfg.php");
      $htmlConfigList = pegaListaUsuarioCfg($Usuario["usu_id"], false, false, true);

      $this->template->load(TEMPLATE_STR, 'TbUsuario/editar', array(
        "titulo"         => gera_titulo_template("Usuário - Editar"),
        "Usuario"        => $Usuario,
        "arrUsuCfgTipo"  => $arrUsuCfgTipo,
        "htmlConfigList" => $htmlConfigList,
      ));
    }
  }

  public function postEditar()
  {
    $variaveisPost = processaPost();
    $vId           = $variaveisPost->id ?? "";
    $vNome         = $variaveisPost->nome ?? "";
    $vEmail        = $variaveisPost->email ?? "";
    $vAtivo        = $variaveisPost->ativo ?? "";
    $vCadPor       = $variaveisPost->cadastrado_por ?? "";

    $Usuario = [];
    $Usuario["usu_id"]      = $vId;
    $Usuario["usu_nome"]    = $vNome;
    $Usuario["usu_email"]   = $vEmail;
    $Usuario["usu_ativo"]   = $vAtivo;
    $Usuario["usa_usuario"] = $vCadPor;
    
    $this->session->set_flashdata('Usuario', $Usuario);

    require_once(APPPATH."/models/TbUsuario.php");
    $retEditar = editaUsuario($Usuario);

    if($retEditar["erro"]){
      geraNotificacao("Aviso!", $retEditar["msg"], "warning");
      redirect(BASE_URL . 'Usuario/editar/' . $vId);
    } else {
      geraNotificacao("Sucesso!", $retEditar["msg"], "success");
      redirect(BASE_URL . 'Usuario/editar/' . $vId);
    }
  }

  public function jsonUsuarioAlteraSenha()
  {
    $variaveisPost  = processaPost();
    $vUsuId         = $variaveisPost->usu_id ?? "";
    $vNovaSenha     = $variaveisPost->nova_senha ?? "";

    $arrRet = [];

    require_once(APPPATH."/models/TbUsuario.php");
    $retUsu = pegaUsuario($vUsuId);

    if($retUsu["erro"]){
      $arrRet["msg"]        = $retUsu["msg"];
      $arrRet["msg_titulo"] = "Aviso!";
      $arrRet["msg_tipo"]   = "warning";
      $arrRet["callback"]   = "jsonUsuarioAlteraSenha($vUsuId);";
    } else {
      $retSenha = alteraSenhaUsuario($vUsuId, $vNovaSenha);
      if($retSenha["erro"]){
        $arrRet["msg"]        = $retSenha["msg"];
        $arrRet["msg_titulo"] = "Aviso!";
        $arrRet["msg_tipo"]   = "warning";
        $arrRet["callback"]   = "jsonUsuarioAlteraSenha($vUsuId);";
      } else {
        $arrRet["msg"]        = $retSenha["msg"];
        $arrRet["msg_titulo"] = "Sucesso!";
        $arrRet["msg_tipo"]   = "success";
      }
    }

    echo json_encode($arrRet);
  }
}