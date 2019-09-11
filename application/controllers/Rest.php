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

  public function pegaGrupoPessoa()
  {
    $arrRet = [];
    $arrRet["erro"]        = false;
    $arrRet["msg"]         = "";
    $arrRet["GrupoPessoa"] = [];

    $variaveisPost = proccessPostRest();
    $vGrpId        = $variaveisPost->grp_id ?? "";
    $vApenasCmpTb  = $variaveisPost->campos_tabela ?? false;

    require_once(APPPATH . '/models/TbGrupoPessoa.php');
    $retGrupoPessoa = pegaGrupoPessoa($vGrpId, $vApenasCmpTb);
    if($retGrupoPessoa["erro"]){
      $arrRet["erro"] = true;
      $arrRet["msg"]  = $retGrupoPessoa["msg"];
    } else {
      $arrRet["GrupoPessoa"] = $retGrupoPessoa["GrupoPessoa"];
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
    $vGrpPostagem  = (isset($variaveisPost->grp_postagem) && $variaveisPost->grp_postagem > 0) ? $variaveisPost->grp_postagem: NULL;
    $vFavoritos    = $variaveisPost->apenas_favoritos ?? false;
    $vProgramado   = $variaveisPost->apenas_programado ?? false;
    $vPrivado      = $variaveisPost->apenas_privado ?? false;

    require_once(APPPATH . 'models/TbGrupoTimeline.php');
    $retPostagens = pegaPostagensGrupo(array(
      "gru_id"            => $vGruId,
      "grp_id"            => $vGrpPostagem,
      "apenas_favoritos"  => $vFavoritos,
      "apenas_programado" => $vProgramado,
      "apenas_privado"    => $vPrivado,
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

  public function salvaComentario()
  {
    $arrRet = [];
    $arrRet["erro"]        = false;
    $arrRet["msg"]         = "";
    $arrRet["Comentarios"] = [];

    $variaveisPost = proccessPostRest();
    $vGrtId        = $variaveisPost->grt_id ?? ""; #grupo timeline
    $vTexto        = $variaveisPost->comentario ?? "";
    $vGrpId        = $variaveisPost->grp_id ?? "";

    // preenche os dados
    $GrupoTimeline                    = [];
    $GrupoTimeline["grt_data"]        = date("Y-m-d H:i:s");
    $GrupoTimeline["grt_titulo"]      = NULL;
    $GrupoTimeline["grt_texto"]       = $vTexto;
    $GrupoTimeline["grt_publico"]     = (int) 1;
    $GrupoTimeline["grt_grp_id"]      = $vGrpId;
    $GrupoTimeline["grt_resposta_id"] = $vGrtId;

    require_once(APPPATH."/models/TbGrupoPessoa.php");
    $retGP       = pegaGrupoPessoa($vGrpId);
    $GrupoPessoa = (!$retGP["erro"] && isset($retGP["GrupoPessoa"])) ? $retGP["GrupoPessoa"] : array();
    $vGruId      = $GrupoPessoa["grp_gru_id"] ?? NULL;
    $GrupoTimeline["grt_gru_id"] = $vGruId;

    require_once(APPPATH."/models/TbGrupoTimeline.php");
    $retInserir = insereGrupoTimeline($GrupoTimeline);

    if ($retInserir["erro"]) {
      $arrRet["msg"]  = $retInserir["msg"];
      $arrRet["erro"] = true;
    } else {
      $arrPostagens   = [];
      $arrPostagens[] = array(
        "grt_id" => $vGrtId
      );

      $retHtmlPost = pegaRespostasGrupoTimeline($arrPostagens);
      if(!$retHtmlPost["erro"]){
        $arrRespostas          = $retHtmlPost["respostas"];
        $arrRet["Comentarios"] = $arrRespostas[$vGrtId];
      }
    }

    echo json_encode($arrRet);
  }

  public function deletaComentario()
  {
    $arrRet = [];
    $arrRet["erro"]        = false;
    $arrRet["msg"]         = "";
    $arrRet["Comentarios"] = [];

    $variaveisPost = proccessPostRest();
    $vGrtId        = $variaveisPost->grt_id ?? "";
    $vGrtIdPai     = $variaveisPost->grt_id_pai ?? "";

    require_once(APPPATH."/models/TbGrupoTimeline.php");
    $retDel = deletaGrupoTimeline($vGrtId);

    if($retDel["erro"]){
      $arrRet["msg"]  = $retDel["msg"];
      $arrRet["erro"] = true;
    } else {
      $arrPostagens   = [];
      $arrPostagens[] = array(
        "grt_id" => $vGrtIdPai
      );

      $retHtmlPost = pegaRespostasGrupoTimeline($arrPostagens);
      if(!$retHtmlPost["erro"]){
        $arrRespostas          = $retHtmlPost["respostas"];
        $arrRet["Comentarios"] = $arrRespostas[$vGrtIdPai];
      }
    }

    echo json_encode($arrRet);
  }
}