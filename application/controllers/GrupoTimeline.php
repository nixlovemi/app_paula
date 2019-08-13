<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class GrupoTimeline extends MY_Controller
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

  public function postNovo()
  {
      # nao vou usar por causa do FILE
      # $variaveisPost = processaPost();

      $titulo    = $_REQUEST["titulo"] ?? NULL;
      $descricao = $_REQUEST["descricao"] ?? "";
      $publico   = (isset($_REQUEST["publico"]) && $_REQUEST["publico"] == "on")
              ? 1 : 0;
      $vGrpId    = $this->session->grp_id ?? NULL;

      // preenche os dados
      $GrupoTimeline                = [];
      $GrupoTimeline["grt_data"]    = date("Y-m-d H:i:s");
      $GrupoTimeline["grt_titulo"]  = $titulo;
      $GrupoTimeline["grt_texto"]   = $descricao;
      $GrupoTimeline["grt_publico"] = (int) $publico;
      $GrupoTimeline["grt_grp_id"]  = $vGrpId;

      require_once(APPPATH."/models/TbGrupoPessoa.php");
      $retGP       = pegaGrupoPessoa($vGrpId);
      $GrupoPessoa = (!$retGP["erro"] && isset($retGP["GrupoPessoa"])) ? $retGP["GrupoPessoa"] : array();
      $vGruId      = $GrupoPessoa["grp_gru_id"] ?? NULL;

      $GrupoTimeline["grt_gru_id"] = $vGruId;
      $this->session->set_flashdata('GrupoTimeline', $GrupoTimeline);
      // =================

      require_once(APPPATH."/models/TbGrupoTimeline.php");
      $retInserir = insereGrupoTimeline($GrupoTimeline);

      if ($retInserir["erro"]) {
          geraNotificacao("Aviso!", $retInserir["msg"], "warning");
          redirect(BASE_URL.'SisGrupo');
      } else {
          $grtId = $retInserir["grtId"] ?? "";

          // verifica anexos
          $arrLinks = $_REQUEST["arquivos"] ?? array();
          foreach ($arrLinks as $linkYT) {
              $_FILES["arquivos"]["name"][]     = $linkYT;
              $_FILES["arquivos"]["tmp_name"][] = "";
          }

          if (count($_FILES) > 0) {
              require_once(APPPATH."/models/TbGrupoTimelineArquivos.php");
              $arrFiles = preConfereArquivos($_FILES, $grtId);
              $retGTA   = insereArquivos($grtId,
                  $arrFiles["arquivos"] ?? array());
              // @todo talvez tratar o retorno
          }
          // ===============

          $this->session->set_flashdata('GrupoTimeline', array());
          geraNotificacao("Sucesso!", $retInserir["msg"], "success");
          redirect(BASE_URL.'SisGrupo');
      }
  }

  public function jsonDeletaPostagem()
  {
      $variaveisPost = processaPost();
      $vGrtId        = $variaveisPost->id ?? "";

      require_once(APPPATH."/models/TbGrupoTimeline.php");
      $retDel = deletaGrupoTimeline($vGrtId);

      if($retDel["erro"]){
          $arrRet["msg"]        = $retDel["msg"];
          $arrRet["msg_titulo"] = "Aviso!";
          $arrRet["msg_tipo"]   = "warning";
      } else {
          $arrRet["msg"]        = $retDel["msg"];
          $arrRet["msg_titulo"] = "Sucesso!";
          $arrRet["msg_tipo"]   = "success";
          $arrRet["callback"]   = "removeDvPostagem($vGrtId)";
      }

      echo json_encode($arrRet);
  }

  public function jsonAddComentario()
  {
    $variaveisPost = processaPost();
    $vGrtId        = $variaveisPost->grtId ?? "";
    $vTexto        = $variaveisPost->texto ?? "";
    $vGrpId        = pegaGrupoPessoaLogadoId();

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
      $arrRet["msg"]        = $retInserir["msg"];
      $arrRet["msg_titulo"] = "Aviso!";
      $arrRet["msg_tipo"]   = "warning";
    } else {
      $arrPostagens   = [];
      $arrPostagens[] = array(
        "grt_id" => $vGrtId
      );

      $retHtmlPost = pegaRespostasGrupoTimeline($arrPostagens);
      if(!$retHtmlPost["erro"]){
        $arrRespostas = $retHtmlPost["respostas"];
        $html         = geraHtmlRespostas($arrRespostas[$vGrtId]);

        $arrRet["html_selector"] = "#item-postagem-$vGrtId .dv-resposta";
        $arrRet["html"]          = $html;
        $arrRet["callback"]      = "$('#item-postagem-$vGrtId .dv-area-comentario textarea').val('')";
      }
    }

    echo json_encode($arrRet);
  }

  public function jsonDeletaComentario()
  {
    $variaveisPost = processaPost();
    $vGrtId        = $variaveisPost->id ?? "";

    require_once(APPPATH."/models/TbGrupoTimeline.php");
    $retDel = deletaGrupoTimeline($vGrtId);

    if($retDel["erro"]){
      $arrRet["msg"]        = $retDel["msg"];
      $arrRet["msg_titulo"] = "Aviso!";
      $arrRet["msg_tipo"]   = "warning";
    } else {
      $arrRet["callback"]   = "$('.item-postagem .dv-resposta .dv-item-resposta-$vGrtId').remove()";
    }

    echo json_encode($arrRet);
  }
}