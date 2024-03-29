<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Json extends CI_Controller
{
  public function __construct()
  {
    CI_Controller::__construct();
    $this->load->helper("utils_helper");
  }
  
  public function jsonPegaHtmlAnexo()
  {
    $arrRet = [];
    
    $variaveisPost = processaPost();
    $vLinhaHtml    = $variaveisPost->linhaHtml ?? "";
    $vidForm       = $variaveisPost->idForm ?? "";
    $vidAnexo      = $variaveisPost->idAnexo ?? "";
    $vLinkYt       = $variaveisPost->linkYt ?? "";
    
    // valida se é link do YT
    if($vLinkYt != "" && !eh_link_youtube($vLinkYt)){
      $arrRet["msg"]        = "Informe um link válido do Youtube!";
      $arrRet["msg_titulo"] = "Alerta!";
      $arrRet["msg_tipo"]   = "warning";
    } else {
      $html = $this->load->view('SisGrupo/novoAnexo', array(
        "frmId"   => $vidForm,
        "idAnexo" => $vidAnexo,
        "linkYt"  => $vLinkYt,
      ), true);
      
      $arrRet["html"]          = $html;
      $arrRet["html_selector"] = $vLinhaHtml;
      $arrRet["html_append"]   = true;
      $arrRet["callback"]      = " $('$vidForm #anexo$vidAnexo').click(); ";
    }
    
    echo json_encode($arrRet);
  }

  public function jsonPegaViewAddGpi()
  {
    $arrRet        = [];
    $variaveisPost = processaPost();

    $vPesId = $variaveisPost->pessoa ?? "";
    $vGruId = $variaveisPost->grupo ?? "";

    require_once(APPPATH."/models/TbGrupoPessoaInfo.php");
    $retGPI = pegaGrupoPessoaInfoPesGru($vPesId, $vGruId);
    if($retGPI["erro"]){
      $arrRet["msg"]        = $retGPI["msg"];
      $arrRet["msg_titulo"] = "Aviso!";
      $arrRet["msg_tipo"]   = "warning";
    } else {
      $GrupoPessoaInfo    = $retGPI["GrupoPessoaInfo"] ?? array();
      $retGrp             = agrupaGrupoPessoaInfoLancamentos($GrupoPessoaInfo);
      $GrupoPessoaInfoGrp = $retGrp["GrupoPessoaInfoGrp"] ?? array();

      $ehPrimeira         = count($GrupoPessoaInfoGrp["primeira"]) <= 0;
      $htmlView           = $this->load->view('TbGrupoPessoaInfo/novo', array(
        "titulo"          => gera_titulo_template("Informação do Participante - Novo"),
        "ehPrimeira"      => $ehPrimeira,
        "pesId"           => $vPesId,
        "gruId"           => $vGruId,
      ), true);

      $htmlAjustado  = processaJsonHtml($htmlView);
      $arrRet["callback"] = "jsonShowAddGrupoPessoaInfo('$htmlAjustado')";
    }

    echo json_encode($arrRet);
  }

  public function jsonPostAddGpi()
  {
    $arrRet        = [];
    $variaveisPost = processaPost();

    $vData     = $variaveisPost->data ?? "";
    $vAltura   = $variaveisPost->altura_cm ?? NULL;
    $vPeso     = $variaveisPost->peso_kg ?? "";
    $vPesoObj  = $variaveisPost->peso_kg_obj ?? NULL;
    $vPrimeira = $variaveisPost->primeira ?? true;
    $vPesId    = $variaveisPost->pessoa ?? "";
    $vGruId    = $variaveisPost->grupo ?? "";

    // valida grupo pessoa
    require_once(APPPATH."/models/TbGrupoPessoa.php");
    $retGP = pegaGrupoPessoaPesGru($vPesId, $vGruId);
    if($retGP["erro"]){
      $arrRet["msg"]        = $retGP["msg"];
      $arrRet["msg_titulo"] = "Aviso!";
      $arrRet["msg_tipo"]   = "warning";
    } else {
      $GrupoPessoa = $retGP["GrupoPessoa"] ?? array();
      $vGrpId      = $GrupoPessoa["grp_id"] ?? "";

      $GrupoPessoaInfo = [];
      $GrupoPessoaInfo["gpi_grp_id"]        = $vGrpId;
      $GrupoPessoaInfo["gpi_data"]          = acerta_data($vData);
      $GrupoPessoaInfo["gpi_altura"]        = $vAltura;
      $GrupoPessoaInfo["gpi_peso"]          = acerta_moeda($vPeso);
      $GrupoPessoaInfo["gpi_peso_objetivo"] = acerta_moeda($vPesoObj);
      $GrupoPessoaInfo["gpi_inicial"]       = (int)$vPrimeira;

      require_once(APPPATH."/models/TbGrupoPessoaInfo.php");
      $ret = insereGrupoPessoaInfo($GrupoPessoaInfo);
      if($ret["erro"]){
        $arrRet["msg"]        = $ret["msg"];
        $arrRet["msg_titulo"] = "Aviso!";
        $arrRet["msg_tipo"]   = "warning";

        $arrRet["callback"] = "jsonAddGrupoPessoaInfo($vPesId, $vGruId);";
      } else {
        geraNotificacao("Sucesso!", $ret["msg"], "success");
        $arrRet["callback"] = "document.location.href = document.location.href;";
      }
    }

    echo json_encode($arrRet);
  }

  public function jsonAddComentario()
  {
    $variaveisPost = processaPost();
    $vGrtId        = $variaveisPost->grtId ?? ""; #grupo timeline
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

  public function postNovoTimelineGrupo()
  {
    # nao vou usar por causa do FILE
    # $variaveisPost = processaPost();

    $descricao      = $_REQUEST["descricao"] ?? "";
    $programar      = $_REQUEST["programar"] ?? NULL;
    $urlNovoPostRed = $_REQUEST["urlNovoPostRed"] ?? BASE_URL . "SisGrupo";
    $publico        = (isset($_REQUEST["publico"]) && $_REQUEST["publico"] == "on") ? 1 : 0;
    $vGrpId         = $this->session->grp_id ?? NULL;
    if(!$vGrpId > 0){
      $vGrpId       = $_REQUEST["grpIdLogado"] ?? "";
    }

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
    $this->session->set_flashdata('GrupoTimeline', $GrupoTimeline);
    // =================

    require_once(APPPATH."/models/TbGrupoTimeline.php");
    $retInserir = insereGrupoTimeline($GrupoTimeline);
    if ($retInserir["erro"]) {
      geraNotificacao("Aviso!", $retInserir["msg"], "warning");
      redirect($urlNovoPostRed);
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
        $retGTA   = insereArquivos($grtId, $arrFiles["arquivos"] ?? array());
        // @todo talvez tratar o retorno
      }
      // ===============

      $this->session->set_flashdata('GrupoTimeline', array());
      geraNotificacao("Sucesso!", $retInserir["msg"], "success");
      redirect($urlNovoPostRed);
    }
  }

  public function jsonFavoritar()
  {
    $variaveisPost = processaPost();
    $vGrtId = $variaveisPost->id ?? "";
    $vGrpId = pegaGrupoPessoaLogadoId() ?? "";

    $GrupoTimelineSalvo = [];
    $GrupoTimelineSalvo["gts_grt_id"] = $vGrtId;
    $GrupoTimelineSalvo["gts_grp_id"] = $vGrpId;

    require_once(APPPATH . "/models/TbGrupoTimelineSalvo.php");
    $retAdd = insereGrupoTimelineSalvo($GrupoTimelineSalvo);

    if ($retAdd["erro"]) {
      $arrRet["msg"] = $retAdd["msg"];
      $arrRet["msg_titulo"] = "Aviso!";
      $arrRet["msg_tipo"] = "warning";
    } else {
      $arrRet["msg"] = $retAdd["msg"];
      $arrRet["msg_titulo"] = "Sucesso!";
      $arrRet["msg_tipo"] = "success";
      $arrRet["callback"] = "jqueryMostraFavoritado($vGrtId)";
    }

    echo json_encode($arrRet);
  }

  public function jsonHtmlFotoPerfil()
  {
    $arrRet   = [];
    $htmlView = $this->load->view('FotoPerfil/index', array(
      "titulo" => gera_titulo_template("Alterar Foto do Perfil"),
    ), true);

    $htmlAjustado  = processaJsonHtml($htmlView);
    $arrRet["callback"] = "fncShowAlterarFotoPerfil('$htmlAjustado')";

    echo json_encode($arrRet);
  }

  public function jsonPostHtmlFotoPerfil()
  {
    $arrRet     = [];
    $UsuarioLog = $_SESSION["usuario_info"] ?? array();
    $ehCliente  = $UsuarioLog->cliente > 0;
    $prefix     = ($ehCliente) ? "pes": "usu";
    $rand       = date("YmdHis");

    # fazer o upload, carregar nova janela e "ligar" o plugin
    if(!isset($_FILES["file"])){
      $arrRet["msg"]        = "Selecione uma imagem para prosseguir!";
      $arrRet["msg_titulo"] = "Aviso!";
      $arrRet["msg_tipo"]   = "warning";
    } else {
      $imgTipo   = $_FILES['file']['type'];
      $imgInfo   = getimagesize($_FILES["file"]["tmp_name"]);
      $imgWidth  = $imgInfo[0];
      $imgHeight = $imgInfo[1];

      $permitido = array("image/jpeg", "image/png");
      if(!in_array($imgTipo, $permitido)) {
        $arrRet["msg"]        = "Selecione uma imagem do tipo JPG ou PNG para prosseguir!";
        $arrRet["msg_titulo"] = "Aviso!";
        $arrRet["msg_tipo"]   = "warning";
      } else if($imgWidth < 250 && $imgHeight < 250){
        $arrRet["msg"]        = "A imagem precisa ter pelo menos 250x250 de tamanho!";
        $arrRet["msg_titulo"] = "Aviso!";
        $arrRet["msg_tipo"]   = "warning";
      } else {
        $usuLogado = pegaUsuarioLogadoId();
        $ext       = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $caminho   = FCPATH."assets/cache/temp-foto-perfil-$prefix-$usuLogado-$rand.$ext";

        $ret = move_uploaded_file($_FILES['file']['tmp_name'], $caminho);
        if(!$ret){
          $arrRet["msg"]        = "Erro ao executar upload! Tente novamente.";
          $arrRet["msg_titulo"] = "Aviso!";
          $arrRet["msg_tipo"]   = "warning";
        } else {
          resizeImage(500, $caminho, $caminho);

          $htmlView = $this->load->view('FotoPerfil/crop', array(
            "caminhoImg" => str_replace(FCPATH, base_url(), $caminho),
          ), true);

          $htmlAjustado       = processaJsonHtml($htmlView);
          $arrRet["callback"] = "fncShowAlterarFotoPerfilCrop('$htmlAjustado')";
        }
      }
    }

    echo json_encode($arrRet);
  }

  public function jsonPostHtmlFotoPerfilCrop()
  {
    #@todo talvez tratar erros melhor
    $arrRet = [];
    $base64 = $_REQUEST["base64"] ?? "";
    $imgUrl = $_REQUEST["imgUrl"] ?? "";
    $tdOk   = ($base64 != "") && ($imgUrl != "");

    if(!$tdOk){
      $arrRet["msg"]        = "Erro ao recortar imagem! Tente novamente.";
      $arrRet["msg_titulo"] = "Aviso!";
      $arrRet["msg_tipo"]   = "warning";
    } else {
      $srcImg = str_replace(BASE_URL, FCPATH, $imgUrl);
      $ext    = pathinfo($srcImg, PATHINFO_EXTENSION);
      if($ext == "png"){
        $type = "data:image/png";
      } else {
        $type = "data:image/jpg";
      }

      list($type, $base64) = explode(';', $base64);
      list(, $base64)      = explode(',', $base64);
      $base64              = base64_decode($base64);

      $ret = file_put_contents($srcImg, $base64);
      if($ret === false){
        $arrRet["msg"]        = "Erro ao salvar imagem! Tente novamente.";
        $arrRet["msg_titulo"] = "Aviso!";
        $arrRet["msg_tipo"]   = "warning";
      } else {
        $nomeImg  = basename($srcImg);
        $novoPath = FCPATH . "template/assets/img/pessoas/$nomeImg";

        rename($srcImg, $novoPath);
        
        $UsuarioLog  = $_SESSION["usuario_info"] ?? array();
        $ehCliente   = $UsuarioLog->cliente > 0;
        $idLogado    = $UsuarioLog->id ?? "";
        $caminhoFoto = str_replace(FCPATH, "", $novoPath);

        if($ehCliente){
          require_once(APPPATH."/models/TbPessoa.php");
          $ret                = pegaPessoa($idLogado);
          $Pessoa             = ($ret["erro"]) ? array(): $ret["Pessoa"];
          $Pessoa["pes_foto"] = $caminhoFoto;
          $retEditar          = editaPessoa($Pessoa, false);
        } else {
          require_once(APPPATH."/models/TbUsuario.php");
          $ret                 = pegaUsuario($idLogado);
          $Usuario             = ($ret["erro"]) ? array(): $ret["Usuario"];
          $Usuario["usu_foto"] = $caminhoFoto;
          $retEditar           = editaUsuario($Usuario, false);
        }
        
        if($retEditar["erro"]){
          $arrRet["msg"]        = $retEditar["msg"];
          $arrRet["msg_titulo"] = "Aviso!";
          $arrRet["msg_tipo"]   = "warning";
        } else {
          $_SESSION["foto"]         = $caminhoFoto;
          $UsuarioLog->foto         = $caminhoFoto;
          $_SESSION["usuario_info"] = $UsuarioLog;
          $arrRet["callback"]       = "document.location.href=document.location.href;";
        }
      }
    }

    echo json_encode($arrRet);
  }

  public function jsonAtualizaNotificacaoStaff()
  {
    $vGrpId = pegaGrupoPessoaLogadoId();

    require_once(APPPATH."/models/TbGrupoTimelineStatus.php");
    $ret = pegaInfoStaffNotificacao($vGrpId);

    echo json_encode($ret);
  }

  public function jsonCarregarMaisPostagens()
  {
    $json         = $_REQUEST["json"] ?? "";
    $jsonDecoded  = base64url_decode($json);
    $arrJson      = json_decode($jsonDecoded, true);
    $dv_ret       = $_REQUEST["dv_ret"] ?? "";
    
    $vGrupoPessoa = $arrJson["GrupoPessoa"] ?? array();
    $vPropria     = $arrJson["propria"] ?? false;
    $vFavoritos   = $arrJson["apenas_favoritos"] ?? false;
    $vPrivada     = $arrJson["apenas_privado"] ?? false;
    $vLimit       = $arrJson["limit"] ?? NULL;
    $vStep        = $arrJson["step"] ?? 50;
    $vOffset      = (isset($arrJson["offset"])) ? $arrJson["offset"]+$vStep: NULL;
    
    require_once(APPPATH."/models/TbGrupoTimeline.php");
    $html        = geraHtmlViewGrupoTimeline($vGrupoPessoa, array(
      "postagem_propria" => $vPropria,
      "apenas_favoritos" => $vFavoritos,
      "limit"            => $vLimit,
      "offset"           => $vOffset,
      "carrega_mais"     => true,
      "apenas_privado"   => $vPrivada,
    ));

    $arrRet = [];
    $htmlAjustado       = processaJsonHtml($html);
    $arrRet["callback"] = "jsonPostCarregarMaisPostagens('$htmlAjustado', '$dv_ret')";
    echo json_encode($arrRet);
  }

  /**
   * usando jquery inpt-seleciona-modal
   */
  public function jsonCidadeSeleciona()
  {
    $arrRet = [];
    $variaveisPost           = processaPost();
    $texoDig                 = $variaveisPost->text;
    $htmlSelector            = "#".SELECIONA_MODAL_DIV;
    $arrRet["html_selector"] = $htmlSelector;

    if(strlen($texoDig) <= 2){
      $arrRet["html"] = "Digite mais de 2 caracteres para pesquisar";
    } else {
      require_once(APPPATH."/models/TbCidade.php");
      $retHtmlSelecionaCidade = pegaListaSelecionaCidade($texoDig);
      $arrRet["html"]         = $retHtmlSelecionaCidade;
    }

    echo json_encode($arrRet);
  }

  public function jsonAvaliarPost()
  {
    $variaveisPost = processaPost();
    $vGrtId        = $variaveisPost->id ?? "";
    $arrRet        = [];

    //@todo validar se pode avaliar esse grt_id
    require_once(APPPATH . '/models/TbGrupoTimeline.php');
    $retGRT = pegaGrupoTimeline($vGrtId, true);
    if($retGRT["erro"]){
      $arrRet["msg"] = $retGRT["msg"];
      $arrRet["msg_titulo"] = "Aviso!";
      $arrRet["msg_tipo"] = "warning";
    } else {
      $GrupoTimeline  = $retGRT["GrupoTimeline"] ?? array();
      $avaliacaoAtual = $GrupoTimeline["grt_avaliacao"] ?? NULL;

      $arrRet["callback"] = "jsonEscolheAvaliacaoPostModal($vGrtId, $avaliacaoAtual)";
    }
    
    echo json_encode($arrRet);
  }

  public function jsonAvaliarPostSalvar()
  {
    $variaveisPost = processaPost();
    $vGrtId        = $variaveisPost->id ?? "";
    $vAvaliacao    = $variaveisPost->avaliacao ?? NULL;
    $arrRet        = [];

    //@todo validar se pode avaliar esse grt_id
    require_once(APPPATH . '/models/TbGrupoTimeline.php');
    $retGRT = pegaGrupoTimeline($vGrtId, true);
    if($retGRT["erro"]){
      $arrRet["msg"] = $retGRT["msg"];
      $arrRet["msg_titulo"] = "Aviso!";
      $arrRet["msg_tipo"] = "warning";
    } else {
      $GrupoTimeline = $retGRT["GrupoTimeline"] ?? array();
      $avaliacao     = ($vAvaliacao != "" && $vAvaliacao != NULL) ? $vAvaliacao: NULL;
      $retAvalia = avaliaGrupoTimeline($GrupoTimeline, $avaliacao);
      if($retAvalia["erro"]){
        $arrRet["msg"] = $retAvalia["msg"];
        $arrRet["msg_titulo"] = "Aviso!";
        $arrRet["msg_tipo"] = "warning";
      } else {
        
        if($avaliacao == NULL || $avaliacao == ""){
          $html = '&nbsp;';
        } else if($avaliacao == 0){
          $html = '<i class="material-icons text-danger">thumb_down</i>';
        } else if($avaliacao == 1) {
          $html = '<i class="material-icons text-success">thumb_up</i>';
        }

        $arrRet["html_selector"] = "#item-postagem-$vGrtId .mais_info_post .mip_bom_ruim";
        $arrRet["html"]          = processaJsonHtml($html);
      }
    }

    echo json_encode($arrRet);
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
      $arrRet["callback"]   = "jsonAlteraSenha('Json', 'jsonPessoaAlteraSenha', $vPesId);";
    } else {
      $retSenha = alteraSenhaPessoa($vPesId, $vNovaSenha);
      if($retSenha["erro"]){
        $arrRet["msg"]        = $retSenha["msg"];
        $arrRet["msg_titulo"] = "Aviso!";
        $arrRet["msg_tipo"]   = "warning";
        $arrRet["callback"]   = "jsonAlteraSenha('Json', 'jsonPessoaAlteraSenha', $vPesId);";
      } else {
        $arrRet["msg"]        = $retSenha["msg"];
        $arrRet["msg_titulo"] = "Sucesso!";
        $arrRet["msg_tipo"]   = "success";
      }
    }

    echo json_encode($arrRet);
  }
}
