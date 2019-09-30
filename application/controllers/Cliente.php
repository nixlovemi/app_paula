<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cliente extends MY_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->helper("utils_helper");
  }

  public function index()
  {
    require_once(APPPATH."/models/TbPessoa.php");
    $htmlLista = pegaListaPessoa(true, true, true, true);

    $this->template->load(TEMPLATE_STR, 'TbCliente/index', array(
      "titulo"    => gera_titulo_template("Cliente"),
      "htmlLista" => $htmlLista,
    ));
  }

  public function novo()
  {
    $Cliente = $this->session->flashdata('Cliente') ?? array();

    $this->template->load(TEMPLATE_STR, 'TbCliente/novo', array(
      "titulo"  => gera_titulo_template("Cliente - Novo"),
      "Cliente" => $Cliente,
    ));
  }

  public function postNovo()
  {
    $variaveisPost  = processaPost();
    $vNome          = $variaveisPost->nome ?? "";
    $vEmail         = $variaveisPost->email ?? "";
    $vSenha         = $variaveisPost->senha ?? "";
    $vNascimento    = $variaveisPost->nascimento ?? NULL;
    $vTelefone      = $variaveisPost->telefone ?? NULL;
    $vCelular       = $variaveisPost->celular ?? NULL;
    $vSexo          = $variaveisPost->sexo ?? "";
    $vCidDesc       = $variaveisPost->cidade ?? "";
    $vCidId         = $variaveisPost->cidade_id ?? "";

    $Pessoa = [];
    $Pessoa["pes_nome"]       = $vNome;
    $Pessoa["pes_email"]      = $vEmail;
    $Pessoa["pes_senha"]      = $vSenha;
    $Pessoa["pes_nascimento"] = acerta_data($vNascimento);
    $Pessoa["pes_telefone"]   = $vTelefone;
    $Pessoa["pes_celular"]    = $vCelular;
    $Pessoa["pes_sexo"]       = $vSexo;
    $Pessoa["pes_cid_id"]     = $vCidId;
    $Pessoa["cid_desc"]       = $vCidDesc; // nao gravo esse campo
    $Pessoa["pes_usa_id"]     = pegaUsuarioLogadoId();
    $this->session->set_flashdata('Cliente', $Pessoa);

    require_once(APPPATH."/models/TbPessoa.php");
    $retInserir = inserePessoa($Pessoa);

    if($retInserir["erro"]){
      geraNotificacao("Aviso!", $retInserir["msg"], "warning");
      redirect(BASE_URL . 'Cliente/novo');
    } else {
      $this->session->unset_userdata('Cliente');
      geraNotificacao("Sucesso!", $retInserir["msg"], "success");
      redirect(BASE_URL . 'Cliente/editar/' . $retInserir["pesId"]);
    }
  }

  public function visualizar($id)
  {
    require_once(APPPATH."/models/TbPessoa.php");
    $ret = pegaPessoa($id);

    if($ret["erro"]){
      geraNotificacao("Aviso!", $ret["msg"], "warning");
      redirect(BASE_URL . 'Cliente');
    } else {
      $Pessoa = $ret["Pessoa"] ?? array();

      require_once(APPPATH."/models/TbPessoaCfg.php");
      $htmlConfigList = pegaListaPessoaCfg($Pessoa["pes_id"], false, false, false);

      $this->template->load(TEMPLATE_STR, 'TbCliente/visualizar', array(
        "titulo"         => gera_titulo_template("Cliente - Visualizar"),
        "Pessoa"         => $Pessoa,
        "htmlConfigList" => $htmlConfigList,
      ));
    }
  }

  public function editar($id)
  {
    require_once(APPPATH."/models/TbPessoa.php");
    $ret = pegaPessoa($id);

    require_once(APPPATH."/models/TbPessoaCfgTipo.php");
    $filtro              = [];
    $filtro["pct_ativo"] = 1;
    $retPCT              = pegaTodasPesCfgTipo($filtro);
    $arrPesCfgTipo       = ($retPCT["erro"]) ? array(): $retPCT["arrPessoaCfgTipo"];

    if($ret["erro"]){
      geraNotificacao("Aviso!", $ret["msg"], "warning");
      redirect(BASE_URL . 'Cliente');
    } else {
      $Pessoa = $this->session->flashdata('Pessoa') ?? $ret["Pessoa"];

      require_once(APPPATH."/models/TbPessoaCfg.php");
      $htmlConfigList = pegaListaPessoaCfg($Pessoa["pes_id"], false, false, true);

      $this->template->load(TEMPLATE_STR, 'TbCliente/editar', array(
        "titulo"         => gera_titulo_template("Cliente - Editar"),
        "Cliente"        => $Pessoa,
        "arrPesCfgTipo"  => $arrPesCfgTipo,
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
    $vNascimento   = $variaveisPost->nascimento ?? NULL;
    $vTelefone     = $variaveisPost->telefone ?? NULL;
    $vCelular      = $variaveisPost->celular ?? NULL;
    $vSexo         = $variaveisPost->sexo ?? "";
    $vCidDesc      = $variaveisPost->cidade ?? "";
    $vCidId        = $variaveisPost->cidade_id ?? "";

    $Pessoa = [];
    $Pessoa["pes_id"]         = $vId;
    $Pessoa["pes_nome"]       = $vNome;
    $Pessoa["pes_email"]      = $vEmail;
    $Pessoa["pes_ativo"]      = (int)$vAtivo;
    $Pessoa["usa_usuario"]    = $vCadPor;
    $Pessoa["pes_nascimento"] = acerta_data($vNascimento);
    $Pessoa["pes_telefone"]   = $vTelefone;
    $Pessoa["pes_celular"]    = $vCelular;
    $Pessoa["pes_sexo"]       = $vSexo;
    $Pessoa["pes_cid_id"]     = $vCidId;
    $Pessoa["cid_desc"]       = $vCidDesc; // nao gravo esse campo

    $this->session->set_flashdata('Pessoa', $Pessoa);

    require_once(APPPATH."/models/TbPessoa.php");
    $retEditar = editaPessoa($Pessoa);

    if($retEditar["erro"]){
      geraNotificacao("Aviso!", $retEditar["msg"], "warning");
      redirect(BASE_URL . 'Cliente/editar/' . $vId);
    } else {
      geraNotificacao("Sucesso!", $retEditar["msg"], "success");
      redirect(BASE_URL . 'Cliente/editar/' . $vId);
    }
  }

  public function jsonUsuarioAlteraSenha()
  {
    $variaveisPost  = processaPost();
    $vUsuId         = $variaveisPost->id ?? "";
    $vNovaSenha     = $variaveisPost->nova_senha ?? "";

    $arrRet = [];

    require_once(APPPATH."/models/TbUsuario.php");
    $retUsu = pegaUsuario($vUsuId);

    if($retUsu["erro"]){
      $arrRet["msg"]        = $retUsu["msg"];
      $arrRet["msg_titulo"] = "Aviso!";
      $arrRet["msg_tipo"]   = "warning";
      $arrRet["callback"]   = "jsonAlteraSenha('Usuario', 'jsonUsuarioAlteraSenha', $vUsuId);";
    } else {
      $retSenha = alteraSenhaUsuario($vUsuId, $vNovaSenha);
      if($retSenha["erro"]){
        $arrRet["msg"]        = $retSenha["msg"];
        $arrRet["msg_titulo"] = "Aviso!";
        $arrRet["msg_tipo"]   = "warning";
        $arrRet["callback"]   = "jsonAlteraSenha('Usuario', 'jsonUsuarioAlteraSenha', $vUsuId);";
      } else {
        $arrRet["msg"]        = $retSenha["msg"];
        $arrRet["msg_titulo"] = "Sucesso!";
        $arrRet["msg_tipo"]   = "success";
      }
    }

    echo json_encode($arrRet);
  }
}