<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class GrupoPessoa extends MY_Controller
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

  public function meuPerfil()
  {
    $grpId = pegaGrupoPessoaLogadoId();

    require_once(APPPATH."/models/TbGrupoPessoa.php");
    $retGrp = pegaGrupoPessoa($grpId);
    if($retGrp["erro"]){
      geraNotificacao("Aviso!", $retGrp["msg"], "warning");
      redirect(BASE_URL . 'SisGrupo');
    } else {
      $GrupoPessoa   = $retGrp["GrupoPessoa"] ?? array();
      $vPetCliente   = $GrupoPessoa["pet_cliente"] ?? 0;
      $vGruId        = $GrupoPessoa["grp_gru_id"] ?? "";
      $vGruDescricao = $GrupoPessoa["gru_descricao"] ?? "";
      $vPesNome      = $GrupoPessoa["pes_nome"] ?? "";

      if($vPetCliente == 0){
        redirect(BASE_URL . 'GrpConfig');
      } else {
        // info do grupo
        require_once(APPPATH."/models/TbGrupo.php");
        $retG = pegaGrupo($vGruId, false);

        if($retG["erro"]){
          geraNotificacao("Aviso!", $retG["msg"], "warning");
          redirect(BASE_URL . 'SisGrupo');
        } else {
          $Grupo = $retG["Grupo"] ?? array();

          // info dos lancamentos
          require_once(APPPATH."/models/TbGrupoPessoaInfo.php");
          $retGPI = pegaGrupoPessoaInfo($grpId);
          $GrupoPessoaInfo    = $retGPI["GrupoPessoaInfo"] ?? array();
          $retGrp             = agrupaGrupoPessoaInfoLancamentos($GrupoPessoaInfo);
          $GrupoPessoaInfoGrp = $retGrp["GrupoPessoaInfoGrp"] ?? array();

          // lista das pesagens
          require_once(APPPATH."/models/TbGrupoPessoaInfo.php");
          $vGrpId   = $GrupoPessoa["grp_id"] ?? "";
          $htmlPeso = pegaListaGrupoPessoaInfo($vGrpId, false, false, false);

          $this->template->load(TEMPLATE_STR, 'TbGrupo/infoPessoa', array(
            "titulo"             => gera_titulo_template("Grupo $vGruDescricao - $vPesNome"),
            "lancar"             => true,
            "editar"             => false,
            "exibeVoltar"        => false,
            "GrupoPessoa"        => $GrupoPessoa,
            "Grupo"              => $Grupo ?? array(),
            "GrupoPessoaInfoGrp" => $GrupoPessoaInfoGrp,
            "htmlPeso"           => $htmlPeso
          ));
        }
      }
    }
  }
}