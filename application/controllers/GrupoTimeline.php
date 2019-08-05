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
    $publico   = (isset($_REQUEST["publico"]) && $_REQUEST["publico"] == "on") ? 1: 0;
    $vGrpId    = $this->session->grp_id ?? NULL;

    // preenche os dados
    $GrupoTimeline     = [];
    $GrupoTimeline["grt_data"]    = date("Y-m-d H:i:s");
    $GrupoTimeline["grt_titulo"]  = $titulo;
    $GrupoTimeline["grt_texto"]   = $descricao;
    $GrupoTimeline["grt_publico"] = (int)$publico;
    $GrupoTimeline["grt_grp_id"]  = $vGrpId;
    
    require_once(APPPATH."/models/TbGrupoPessoa.php");
    $retGP       = pegaGrupoPessoa($vGrpId);
    $GrupoPessoa = (!$retGP["erro"] && isset($retGP["GrupoPessoa"])) ? $retGP["GrupoPessoa"]: array();
    $vGruId      = $GrupoPessoa["grp_gru_id"] ?? NULL;

    $GrupoTimeline["grt_gru_id"] = $vGruId;
    $this->session->set_flashdata('GrupoTimeline', $GrupoTimeline);
    // =================
    
    require_once(APPPATH."/models/TbGrupoTimeline.php");
    $retInserir = insereGrupoTimeline($GrupoTimeline);

    if($retInserir["erro"]){
      geraNotificacao("Aviso!", $retInserir["msg"], "warning");
      redirect(BASE_URL . 'SisGrupo');
    } else {
      $grtId = $retInserir["grtId"] ?? "";

      // verifica anexos
      if(count($_FILES) > 0){
        require_once(APPPATH."/models/TbGrupoTimelineArquivos.php");
        $arrFiles = preConfereArquivos($_FILES);
        $retGTA   = insereArquivos($grtId, $arrFiles["arquivos"] ?? array());
        // @todo talvez tratar o retorno
      }
      
      $this->session->set_flashdata('GrupoTimeline', array());
      geraNotificacao("Sucesso!", $retInserir["msg"], "success");
      redirect(BASE_URL . 'SisGrupo');
    }
  }
}