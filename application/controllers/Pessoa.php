<?php
//!! lembrar de exibir apenas pra pessoa logada
//=============================================
defined('BASEPATH') OR exit('No direct script access allowed');

class Pessoa extends MY_Controller
{
  public function __construct()
  {
    $admin = false;
    parent::__construct($admin);
    $this->load->helper("utils_helper");
  }

  public function index()
  {
    require_once(APPPATH."/models/TbPessoa.php");
    $htmlLista = pegaListaPessoa(true, true, true);

    $this->template->load(TEMPLATE_STR, 'TbPessoa/index', array(
      "titulo"    => gera_titulo_template("Pessoa"),
      "htmlLista" => $htmlLista,
    ));
  }

  public function novo()
  {
    $Pessoa = $this->session->flashdata('Pessoa') ?? array();
    
    require_once(APPPATH."/models/TbPessoaTipo.php");
    $filtro = array("pet_ativo"=>1);
    $ret = pegaTodasPessoaTipo($filtro);
    $arrPessoaTipo = ($ret["erro"]) ? array(): $ret["arrPessoaTipo"];

    $this->template->load(TEMPLATE_STR, 'TbPessoa/novo', array(
      "titulo"        => gera_titulo_template("Usuário - Novo"),
      "Pessoa"        => $Pessoa,
      "arrPessoaTipo" => $arrPessoaTipo,
    ));
  }

  public function postNovo()
  {
    $variaveisPost = processaPost();
    $vTipo         = $variaveisPost->tipo ?? "";
    $vNome         = $variaveisPost->nome ?? "";
    $vEmail        = $variaveisPost->email ?? "";
    $vSenha        = $variaveisPost->senha ?? "";

    $Pessoa = [];
    $Pessoa["pes_pet_id"] = $vTipo;
    $Pessoa["pes_nome"]   = $vNome;
    $Pessoa["pes_email"]  = $vEmail;
    $Pessoa["pes_senha"]  = $vSenha;
    $Pessoa["pes_usu_id"] = pegaUsuarioLogadoId();
    $this->session->set_flashdata('Pessoa', $Pessoa);

    require_once(APPPATH."/models/TbPessoa.php");
    $retInserir = inserePessoa($Pessoa);

    if($retInserir["erro"]){
      geraNotificacao("Aviso!", $retInserir["msg"], "warning");
      redirect(BASE_URL . 'Pessoa/novo');
    } else {
      geraNotificacao("Sucesso!", $retInserir["msg"], "success");
      redirect(BASE_URL . 'Pessoa/editar/' . $retInserir["pesId"]);
    }
  }

  public function editar($id)
  {
    require_once(APPPATH."/models/TbPessoa.php");
    $ret    = pegaPessoa($id);
    $Pessoa = $this->session->flashdata('Pessoa') ?? $ret["Pessoa"];

    require_once(APPPATH."/models/TbPessoaTipo.php");
    $filtro        = array("pet_ativo"=>1);
    $retPT         = pegaTodasPessoaTipo($filtro);
    $arrPessoaTipo = ($retPT["erro"]) ? array(): $retPT["arrPessoaTipo"];

    if($ret["erro"]){
      geraNotificacao("Aviso!", $ret["msg"], "warning");
      redirect(BASE_URL . 'Pessoa');
    } else {
      $this->template->load(TEMPLATE_STR, 'TbPessoa/editar', array(
        "titulo"        => gera_titulo_template("Pessoa - Editar"),
        "Pessoa"        => $Pessoa,
        "arrPessoaTipo" => $arrPessoaTipo,
      ));
    }
  }

  public function postEditar()
  {
    $variaveisPost = processaPost();
    $vId           = $variaveisPost->id ?? "";
    $vTipo         = $variaveisPost->tipo ?? "";
    $vNome         = $variaveisPost->nome ?? "";
    $vEmail        = $variaveisPost->email ?? "";
    $vAtivo        = $variaveisPost->ativo ?? "";

    $Pessoa = [];
    $Pessoa["pes_id"]     = $vId;
    $Pessoa["pes_pet_id"] = $vTipo;
    $Pessoa["pes_nome"]   = $vNome;
    $Pessoa["pes_email"]  = $vEmail;
    $Pessoa["pes_ativo"]  = $vAtivo;
    $this->session->set_flashdata('Pessoa', $Pessoa);

    require_once(APPPATH."/models/TbPessoa.php");
    $retEditar = editaPessoa($Pessoa);

    if($retEditar["erro"]){
      geraNotificacao("Aviso!", $retEditar["msg"], "warning");
      redirect(BASE_URL . 'Pessoa/editar/' . $vId);
    } else {
      geraNotificacao("Sucesso!", $retEditar["msg"], "success");
      redirect(BASE_URL . 'Pessoa/editar/' . $vId);
    }
  }

  public function visualizar($id)
  {
    require_once(APPPATH."/models/TbPessoa.php");
    $ret = pegaPessoa($id);

    if($ret["erro"]){
      geraNotificacao("Aviso!", $ret["msg"], "warning");
      redirect(BASE_URL . 'Pessoa');
    } else {
      $this->template->load(TEMPLATE_STR, 'TbPessoa/visualizar', array(
        "titulo" => gera_titulo_template("Pessoa - Visualizar"),
        "Pessoa" => $ret["Pessoa"],
      ));
    }
  }

  public function jsonPessoaAlteraSenha()
  {
    $variaveisPost  = processaPost();
    $vPesId         = $variaveisPost->id ?? "";
    $vNovaSenha     = $variaveisPost->nova_senha ?? "";

    $arrRet = [];

    require_once(APPPATH."/models/TbPessoa.php");
    $retUsu = pegaPessoa($vPesId);

    if($retUsu["erro"]){
      $arrRet["msg"]        = $retUsu["msg"];
      $arrRet["msg_titulo"] = "Aviso!";
      $arrRet["msg_tipo"]   = "warning";
      $arrRet["callback"]   = "jsonAlteraSenha('Pessoa', 'jsonPessoaAlteraSenha', $vPesId);";
    } else {
      $retSenha = alteraSenhaPessoa($vPesId, $vNovaSenha);
      if($retSenha["erro"]){
        $arrRet["msg"]        = $retSenha["msg"];
        $arrRet["msg_titulo"] = "Aviso!";
        $arrRet["msg_tipo"]   = "warning";
        $arrRet["callback"]   = "jsonAlteraSenha('Pessoa', 'jsonPessoaAlteraSenha', $vPesId);";
      } else {
        $arrRet["msg"]        = $retSenha["msg"];
        $arrRet["msg_titulo"] = "Sucesso!";
        $arrRet["msg_tipo"]   = "success";
      }
    }

    echo json_encode($arrRet);
  }
}