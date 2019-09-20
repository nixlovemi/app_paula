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
    $vLimit        = $variaveisPost->limit ?? 25;
    $vOffset       = $variaveisPost->offset ?? 0;

    require_once(APPPATH . 'models/TbGrupoTimeline.php');
    $retPostagens = pegaPostagensGrupo(array(
      "gru_id"            => $vGruId,
      "grp_id"            => $vGrpPostagem,
      "apenas_favoritos"  => $vFavoritos,
      "apenas_programado" => $vProgramado,
      "apenas_privado"    => $vPrivado,
      "limit"             => $vLimit,
      "offset"            => $vOffset,
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

    printaRetornoRest($arrRet);
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

    printaRetornoRest($arrRet);
  }

  public function postNovoTimelineGrupo()
  {
    # nao vou usar por causa do FILE
    $arrRet = [];
    $arrRet["erro"] = false;
    $arrRet["msg"]  = "";
    $variaveisPost  = proccessPostRest();

    $descricao      = $variaveisPost->descricao ?? "";
    $programar      = $variaveisPost->programar ?? NULL;
    $publico        = (isset($variaveisPost->publico) && $variaveisPost->publico) ? 1 : 0;
    $vGrpId         = $variaveisPost->grpLogado ?? NULL;
    $vImagens       = $variaveisPost->imagens ?? array();

    // preenche os dados
    $GrupoTimeline = [];
    $GrupoTimeline["grt_data"]          = date("Y-m-d H:i:s");
    $GrupoTimeline["grt_dt_programado"] = ($programar != NULL) ? acerta_data_hora($programar): NULL;
    $GrupoTimeline["grt_texto"]         = $descricao;
    $GrupoTimeline["grt_publico"]       = (int) $publico;
    $GrupoTimeline["grt_grp_id"]        = $vGrpId;

    require_once(APPPATH."/models/TbGrupoPessoa.php");
    $retGP       = pegaGrupoPessoa($vGrpId);
    $GrupoPessoa = (!$retGP["erro"] && isset($retGP["GrupoPessoa"])) ? $retGP["GrupoPessoa"] : array();
    $vGruId      = $GrupoPessoa["grp_gru_id"] ?? NULL;

    $GrupoTimeline["grt_gru_id"] = $vGruId;
    // =================

    require_once(APPPATH."/models/TbGrupoTimeline.php");
    $retInserir = insereGrupoTimeline($GrupoTimeline);
    if ($retInserir["erro"]) {
      $arrRet["erro"] = true;
      $arrRet["msg"]  = $retInserir["msg"];
    } else {
      $grtId         = $retInserir["grtId"] ?? "";
      $arrRet["msg"] = $retInserir["msg"];

      $vFiles                         = [];
      $vFiles["arquivos"]             = [];
      $vFiles["arquivos"]["name"]     = [];
      $vFiles["arquivos"]["tmp_name"] = [];
      $vFiles["arquivos"]["app"]      = [];
      
      $i       = 1;
      $idxFile = 0;
      foreach($vImagens as $base64Img){
        // $baseFromJavascript = "data:image/png;base64,BBBFBfj42Pj4";

        // remove the part that we don't need from the provided image and decode it
        $data     = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Img));
        $filename = "img-id$grtId-$i.jpg";
        $filepath = APPPATH . "cache/$filename";

        // Save the image in a defined path
        file_put_contents($filepath,$data);
        $i++;

        $vFiles["arquivos"]["name"][$idxFile]     = $filename;
        $vFiles["arquivos"]["tmp_name"][$idxFile] = $filepath;
        $vFiles["arquivos"]["app"][$idxFile]      = true;
        $idxFile++;
      }

      if (count($vFiles) > 0) {
        require_once(APPPATH."/models/TbGrupoTimelineArquivos.php");
        $arrFiles = preConfereArquivos($vFiles, $grtId);

        $retGTA   = insereArquivos($grtId, $arrFiles["arquivos"] ?? array());
        // @todo talvez tratar o retorno
      }
    }

    printaRetornoRest($arrRet);
  }

  public function postDeletaPostagem()
  {
    $arrRet = [];
    $arrRet["erro"] = false;
    $arrRet["msg"]  = "";

    $variaveisPost = proccessPostRest();
    $vGrtId        = $variaveisPost->id ?? "";

    require_once(APPPATH."/models/TbGrupoTimeline.php");
    $retDel = deletaGrupoTimeline($vGrtId);

    if($retDel["erro"]){
        $arrRet["msg"]  = $retDel["msg"];
        $arrRet["erro"] = true;
    } else {
        $arrRet["msg"]  = $retDel["msg"];
        $arrRet["erro"] = false;
    }

    echo json_encode($arrRet);
  }

  public function postFavoritarPostagem()
  {
    $arrRet = [];
    $arrRet["erro"] = false;
    $arrRet["msg"]  = "";

    $variaveisPost      = proccessPostRest();
    $vGrtId             = $variaveisPost->id ?? "";
    $vGrpId             = $variaveisPost->grpLogado ?? "";
    $_SESSION["grp_id"] = $vGrpId;

    $GrupoTimelineSalvo = [];
    $GrupoTimelineSalvo["gts_grt_id"] = $vGrtId;
    $GrupoTimelineSalvo["gts_grp_id"] = $vGrpId;

    require_once(APPPATH . "/models/TbGrupoTimelineSalvo.php");
    $retAdd = insereGrupoTimelineSalvo($GrupoTimelineSalvo);

    if ($retAdd["erro"]) {
      $arrRet["msg"]  = $retAdd["msg"];
      $arrRet["erro"] = true;
    } else {
      $arrRet["msg"]  = $retAdd["msg"];
      $arrRet["erro"] = false;
    }

    echo json_encode($arrRet);
  }
}