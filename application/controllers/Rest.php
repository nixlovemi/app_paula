<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rest extends CI_Controller
{
  public function __construct()
  {
    CI_Controller::__construct();

    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    $method = $_SERVER['REQUEST_METHOD'];
    if($method == "OPTIONS") {
      die();
    }

    $this->load->helper("utils_helper");
  }

  public function executaLogin()
  {
    $arrRet = [];
    $arrRet["erro"]    = false;
    $arrRet["msg"]     = "";
    $arrRet["Usuario"] = [];
    $arrRet["Grupos"]  = [];

    // @todo fazer tela quando pessoa for cadastrada por dois usuários diferentes
    // provavelmente fazer uma tela antes pra ela escolher em qual grupo/usuário vai logar
    require_once(APPPATH."/models/Login.php");
    $variaveisPost = proccessPostRest();
    $vUsuario      = $variaveisPost->usuario ?? "";
    $vSenha        = $variaveisPost->senha ?? "";

    $retLogin = executaLogin($vUsuario, $vSenha, false, true);
    if($retLogin->erro){
      $arrRet["erro"] = true;
      $arrRet["msg"]  = $retLogin->msg;
    } else {
      $Usuario           = json_decode($retLogin->infoUsr);
      $arrRet["Usuario"] = $Usuario;

      // pega grupos dessa pessoa
      require_once(APPPATH."/models/TbGrupoPessoa.php");
      $retGrp = pegaGruposPessoaId($Usuario->id);
      if($retGrp["erro"]){
        $arrRet["erro"] = true;
        $arrRet["msg"]  = $retGrp["msg"];
      } else {
        #@todo aqui peguei o primeiro que veio / o certo é mostrar tds e a pessoa escolher
        $GruposPessoa     = $retGrp["GruposPessoa"] ?? array();
        $arrRet["Grupos"] = $GruposPessoa;
      }
    }

    printaRetornoRest($arrRet);
  }

  public function pegaStaffGrupo()
  {
    $arrRet = [];
    $arrRet["erro"]  = false;
    $arrRet["msg"]   = "";
    $arrRet["Staff"] = [];
    
    $variaveisPost = proccessPostRest();
    $vGruId        = $variaveisPost->gru_id ?? "";

    require_once(APPPATH . '/models/TbGrupoPessoa.php');
    $retStaff = pegaGrupoPessoasGru($vGruId, true);
    if($retStaff["erro"]){
      $arrRet["erro"] = true;
      $arrRet["msg"]  = $retStaff["msg"];
    } else {
      $arrRet["Staff"] = $retStaff["GruposPessoas"];
    }
    
    printaRetornoRest($arrRet);
  }

  public function pegaPostagensGrupo()
  {
    // qdo alterar o geraHtmlViewGrupoTimeline, tem q atualizar aqui

    $arrRet = [];
    $arrRet["erro"]      = false;
    $arrRet["msg"]       = "";
    $arrRet["Postagens"] = [];
    $arrRet["Respostas"] = [];
    $arrRet["Arquivos"]  = [];
    $arrRet["Salvos"]    = [];
    $arrRet["arrParam"]  = [];

    $variaveisPost = proccessPostRest();
    $vGruId        = $variaveisPost->gru_id ?? "";

    require_once(APPPATH . 'models/TbGrupoTimeline.php');
    $retPostagens = pegaPostagensGrupo(array(
      "gru_id" => $vGruId,
    ));

    if($retPostagens["erro"]){
      $arrRet["erro"] = true;
      $arrRet["msg"]  = $retPostagens["msg"];
    } else {
      $arrRet["Postagens"] = $retPostagens["postagens"];
      $arrRet["Salvos"]    = $retPostagens["salvos"];
      $arrRet["arrParam"]  = $retPostagens["arrParam"];

      $retResp             = pegaRespostasGrupoTimeline($arrRet["Postagens"]);
      $arrResp             = (!$retResp["erro"] && isset($retResp["respostas"])) ? $retResp["respostas"]: array();
      $arrRet["Respostas"] = $arrResp;

      require_once(APPPATH."/models/TbGrupoTimelineArquivos.php");
      $retGTA             = pegaArquivos($arrRet["Postagens"]);
      $arrArquivos        = ($retGTA["erro"]) ? array(): $retGTA["arquivos"];
      $arrRet["Arquivos"] = $arrArquivos;
    }

    printaRetornoRest($arrRet);
  }
}