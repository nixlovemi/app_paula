<?php
require_once(APPPATH."/helpers/utils_helper.php");

function pegaGrupoTimeline($vGrtId, $apenasCamposTabela = false)
{
    $arrRetorno                  = [];
    $arrRetorno["erro"]          = false;
    $arrRetorno["msg"]           = "";
    $arrRetorno["GrupoTimeline"] = [];

    if (!is_numeric($vGrtId)) {
        $arrRetorno["erro"] = true;
        $arrRetorno["msg"]  = "ID inválido para buscar Postagem!";

        return $arrRetorno;
    }

    $CI = pega_instancia();
    $CI->load->database();

    $camposTabela = "grt_id, grt_gru_id, grt_grp_id, grt_data, grt_titulo, grt_texto, grt_publico, grt_ativo, grt_resposta_id";
    if (!$apenasCamposTabela) {
        $camposTabela .= ", gru_id, gru_dt_inicio, gru_dt_termino, gru_ativo, grupo_ativo, usu_nome, str_dt_inicio, str_dt_termino, grp_id, grp_gru_id, grp_pes_id, grp_ativo, grupo_pessoa_ativo, pes_nome, pes_email, pes_foto, pet_descricao, pet_cliente";
    }

    $CI->db->select($camposTabela);
    $CI->db->from('v_tb_grupo_timeline');
    $CI->db->where('grt_id =', $vGrtId);

    $query = $CI->db->get();
    $row   = $query->row();

    if (!isset($row)) {
        $arrRetorno["erro"] = true;
        $arrRetorno["msg"]  = "Erro ao encontrar Postagem!";

        return $arrRetorno;
    }

    $GrupoTimeline                    = [];
    $GrupoTimeline["grt_id"]          = $row->grt_id;
    $GrupoTimeline["grt_gru_id"]      = $row->grt_gru_id;
    $GrupoTimeline["grt_grp_id"]      = $row->grt_grp_id;
    $GrupoTimeline["grt_data"]        = $row->grt_data;
    $GrupoTimeline["grt_titulo"]      = $row->grt_titulo;
    $GrupoTimeline["grt_texto"]       = $row->grt_texto;
    $GrupoTimeline["grt_publico"]     = $row->grt_publico;
    $GrupoTimeline["grt_ativo"]       = $row->grt_ativo;
    $GrupoTimeline["grt_resposta_id"] = $row->grt_resposta_id;
    if (!$apenasCamposTabela) {
        $GrupoTimeline["gru_id"]             = $row->gru_id;
        $GrupoTimeline["gru_dt_inicio"]      = $row->gru_dt_inicio;
        $GrupoTimeline["gru_dt_termino"]     = $row->gru_dt_termino;
        $GrupoTimeline["gru_ativo"]          = $row->gru_ativo;
        $GrupoTimeline["grupo_ativo"]        = $row->grupo_ativo;
        $GrupoTimeline["usu_nome"]           = $row->usu_nome;
        $GrupoTimeline["str_dt_inicio"]      = $row->str_dt_inicio;
        $GrupoTimeline["str_dt_termino"]     = $row->str_dt_termino;
        $GrupoTimeline["grp_id"]             = $row->grp_id;
        $GrupoTimeline["grp_gru_id"]         = $row->grp_gru_id;
        $GrupoTimeline["grp_pes_id"]         = $row->grp_pes_id;
        $GrupoTimeline["grp_ativo"]          = $row->grp_ativo;
        $GrupoTimeline["grupo_pessoa_ativo"] = $row->grupo_pessoa_ativo;
        $GrupoTimeline["pes_nome"]           = $row->pes_nome;
        $GrupoTimeline["pes_email"]          = $row->pes_email;
        $GrupoTimeline["pes_foto"]           = $row->pes_foto;
        $GrupoTimeline["pet_descricao"]      = $row->pet_descricao;
        $GrupoTimeline["pet_cliente"]        = $row->pet_cliente;
    }

    $arrRetorno["msg"]           = "Postagem encontrada com sucesso!";
    $arrRetorno["GrupoTimeline"] = $GrupoTimeline;
    return $arrRetorno;
}

/**
 * $arrPostagens vem da função pegaPostagensGrupo
 */
function pegaRespostasGrupoTimeline($arrPostagens)
{
  $arrRetorno              = [];
  $arrRetorno["erro"]      = false;
  $arrRetorno["msg"]       = "";
  $arrRetorno["respostas"] = [];

  require_once(APPPATH."/helpers/utils_helper.php");
  $CI = pega_instancia();
  $CI->load->database();

  $arrGrtId = [];
  foreach($arrPostagens as $postagem){
    $vGrtId     = $postagem["grt_id"] ?? "";
    if($vGrtId > 0){
      $arrGrtId[] = $vGrtId;
    }
  }

  if( count($arrGrtId) > 0 ){
    $CI->db->select('grt_id, grt_gru_id, grt_resposta_id, grt_data, grt_texto, grp_id, pes_nome, pes_email, pes_foto');
    $CI->db->from('v_tb_grupo_timeline');
    $CI->db->where('grt_resposta_id IN ('.implode(",", $arrGrtId).')');
    $CI->db->where('grt_ativo = ', 1);
    $CI->db->order_by('grt_resposta_id ASC, grt_data ASC');
    $query = $CI->db->get();

    if(!$query){
      $arrRetorno["erro"] = true;
      $arrRetorno["msg"]  = "Erro ao encontrar respostas das postagens!";

      return $arrRetorno;
    }

    foreach ($query->result() as $row) {
      if (isset($row)) {
        $vGrtRespostaId = $row->grt_resposta_id ?? "";
        if($vGrtRespostaId != ""){
          if(!array_key_exists($vGrtRespostaId, $arrRetorno["respostas"])){
            $arrRetorno["respostas"][$vGrtRespostaId] = [];
          }

          $arrRetorno["respostas"][$vGrtRespostaId][] = (array) $row;
        }
      }
    }
  }

  //@todo talvez tratar erros
  return $arrRetorno;
}

/**
 * vem da funcao pegaRespostasGrupoTimeline
 */
function geraHtmlRespostas($arrRespostas)
{
  $html = "";
  foreach($arrRespostas as $resposta){
    $grtId         = $resposta["grt_id"] ?? "";
    $grpId         = $resposta["grp_id"] ?? "";
    $gruId         = $resposta["grt_gru_id"] ?? "";
    $ehAdminLogado = ehAdminGrupo($gruId);
    $grpLogado     = pegaGrupoPessoaLogadoId();
    $htmlDel       = ($grpLogado == $grpId || $ehAdminLogado) ? "<a class='delete' href='javascript:;' onclick='fncItemPostagemDelComentario($grtId)'><i class='material-icons'>delete</i></a>": "";

    $foto3 = ($resposta["pes_foto"] != "") ? BASE_URL . $resposta["pes_foto"]: BASE_URL . FOTO_DEFAULT;
    $nome  = $resposta['pes_nome'] ?? "";
    $texto = (isset($resposta['grt_texto'])) ? nl2br($resposta['grt_texto']): "";

    $html .= "<div class='row dv-item-resposta-$grtId' style='padding-bottom:6px;'>";
    $html .= "  <div class='col-md-1 dv-img-comentario' style='padding:0;'>";
    $html .= "    <a class='text-info' href='javascript:;'>";
    $html .= "      <img src='$foto3' alt='Circle Image' class='rounded-circle img-fluid img-comentario' />";
    $html .= "    </a>";
    $html .= "  </div>";
    $html .= "  <div class='col-md-11 dv-area-comentario' style='padding-left:0;'>";
    $html .= "    <p class='comentario-texto'>";
    $html .= "      <span>$nome:</span>";
    $html .= "      $texto";
    $html .= "      $htmlDel";
    $html .= "    </p>";
    $html .= "  </div>";
    $html .= "</div>";
  }

  return $html;
}

function geraHtmlViewGrupoTimeline($GrupoPessoa, $postagemPropria=false, $apenasFavoritos=false, $limit=50, $offset=0, $carregaMais=false)
{
  $CI            = pega_instancia();
  $GrupoTimeline = $CI->session->flashdata('GrupoTimeline') ?? array();
  $vGruDesc      = $GrupoPessoa["gru_descricao"] ?? NULL;
  $gruId         = $GrupoPessoa["grp_gru_id"] ?? "";
  $grpId         = ($postagemPropria || $apenasFavoritos) ? $GrupoPessoa["grp_id"]: NULL;

  require_once(APPPATH."/models/TbGrupoTimeline.php");
  $retPost       = pegaPostagensGrupo($gruId, $grpId, $apenasFavoritos, $limit, $offset);
  $arrPostagens  = (!$retPost["erro"] && isset($retPost["postagens"])) ? $retPost["postagens"]: array();
  $arrSalvos     = (!$retPost["erro"] && isset($retPost["salvos"])) ? $retPost["salvos"]: array();

  // prepara esquema de limit
  unset($retPost["postagens"]);
  unset($retPost["salvos"]);
  unset($retPost["erro"]);
  unset($retPost["msg"]);
  
  $arrInfoLimit                = $retPost;
  $arrInfoLimit["propria"]     = $postagemPropria;
  $arrInfoLimit["GrupoPessoa"] = $GrupoPessoa;
  if(count($arrPostagens) <= 0){
    $arrInfoLimit["limit"]  = 0;
    $arrInfoLimit["offset"] = 0;
  }
  // ========================

  $retResp = pegaRespostasGrupoTimeline($arrPostagens);
  $arrResp = (!$retResp["erro"] && isset($retResp["respostas"])) ? $retResp["respostas"]: array();

  require_once(APPPATH."/models/TbGrupoTimelineArquivos.php");
  $retGTA      = pegaArquivos($arrPostagens);
  $arrArquivos = ($retGTA["erro"]) ? array(): $retGTA["arquivos"];

  require_once(APPPATH."/models/TbGrupoPessoa.php");
  $retGpss  = pegaGrupoPessoasGru($gruId, true);
  $arrStaff = ($retGpss["erro"]) ? array(): $retGpss["GruposPessoas"];
  
  // ve se eh staff
  $vGrpLogado = pegaGrupoPessoaLogadoId();
  $ehStaff    = false;
  foreach($arrStaff as $staff){
    if($staff["grp_id"] == $vGrpLogado){
      $ehStaff = true;
      break;
    }
  }
  // ==============

  $mostraNovoPost    = false;
  $urlNovoPostRed    = BASE_URL . "SisGrupo";
  $urlStaff          = "SisGrupo/indexInfo";
  $urlTdsPosts       = "SisGrupo";
  $urlPostsFavoritos = "SisGrupo/favoritos/$vGrpLogado";

  $tituloPag         = "";
  if($postagemPropria){
    $tituloPag = " - " . ($GrupoPessoa["pes_nome"] ?? "");
  } else if($apenasFavoritos){
    $tituloPag = " - Favoritos";
  }

  $ControllerAction = pegaControllerAction();
  $urlVarGrpId      = $ControllerAction["vars"][0] ?? "";
  if($ControllerAction["controller"] == "SisGrupo" && ($ControllerAction["action"] == "index" || $ControllerAction["action"] == "")){
    $mostraNovoPost    = !$ehStaff;
    $urlNovoPostRed    = BASE_URL . $ControllerAction["controller"] . "/" . $ControllerAction["action"];
  } else if($ControllerAction["controller"] == "SisGrupo" && $ControllerAction["action"] == "indexInfo"){
    $mostraNovoPost    = ($ehStaff) && ($urlVarGrpId == $vGrpLogado);
    $urlNovoPostRed    = BASE_URL . $ControllerAction["controller"] . "/" . $ControllerAction["action"] . "/" . $grpId;
  } else if($ControllerAction["controller"] == "Grupo" && ($ControllerAction["action"] == "timeline" || $ControllerAction["action"] == "indexInfo")){
    $mostraNovoPost    = ($ehStaff) && ($urlVarGrpId == $vGrpLogado);
    $urlNovoPostRed    = BASE_URL . $ControllerAction["controller"] . "/" . $ControllerAction["action"] . "/" . $grpId;
    $urlStaff          = "Grupo/indexInfo";
    $urlTdsPosts       = "Grupo/timeline/$gruId";
    $urlPostsFavoritos = "Grupo/favoritos/$vGrpLogado";
  } else if($ControllerAction["controller"] == "Grupo" && $ControllerAction["action"] == "favoritos"){
    $urlStaff          = "Grupo/indexInfo";
    $urlTdsPosts       = "Grupo/timeline/$gruId";
    $urlPostsFavoritos = "Grupo/favoritos/$vGrpLogado";
  }

  // view posts
  $htmlPosts = $CI->load->view('TbGrupoTimeline/postagens', array(
      "vGruDescricao" => $vGruDesc,
      "arrPostagens"  => $arrPostagens,
      "arrSalvos"     => $arrSalvos,
      "arrArquivos"   => $arrArquivos,
      "arrResp"       => $arrResp,
      "arrInfoLimit"  => $arrInfoLimit,
      "carregaMais"   => $carregaMais,
    ), true);

  if($carregaMais){
    return $htmlPosts;
  } else {
    $CI->template->load(TEMPLATE_STR, 'SisGrupo/index', array(
      "titulo"            => gera_titulo_template("Timeline do Grupo $tituloPag"),
      "arrStaff"          => $arrStaff,
      "urlStaff"          => $urlStaff,
      "urlTdsPosts"       => $urlTdsPosts,
      "urlPostsFavoritos" => $urlPostsFavoritos,
      "htmlPosts"         => $htmlPosts,
      "GrupoTimeline"     => $GrupoTimeline,
      "mostraNovoPost"    => $mostraNovoPost,
      "urlNovoPostRed"    => $urlNovoPostRed,
    ));
  }
}

function validaInsereGrupoTimeline($GrupoTimeline)
{
    require_once(APPPATH."/helpers/utils_helper.php");
    $strValida = "";

    # grt_id 	[grt_gru_id] 	[grt_grp_id] 	[grt_data] 	grt_titulo 	[grt_texto] 	[grt_publico] 	grt_ativo 	[grt_resposta_id]
    // validacao basica dos campos
    $vGruId = $GrupoTimeline["grt_gru_id"] ?? "";
    if (!is_numeric($vGruId)) {
        $strValida .= "<br />&nbsp;&nbsp;* Informação 'ID do Grupo' é inválida.";
    }

    $vGrpId = $GrupoTimeline["grt_grp_id"] ?? "";
    if (!is_numeric($vGrpId)) {
        $strValida .= "<br />&nbsp;&nbsp;* Informação 'ID do Participante' é inválida.";
    }

    $vData = $GrupoTimeline["grt_data"] ?? "";
    if (!isValidDate($vData)) {
        $strValida .= "<br />&nbsp;&nbsp;* Informe uma data válida.";
    }

    $vTexto = $GrupoTimeline["grt_texto"] ?? "";
    if (strlen($vTexto) < 2) {
        $strValida .= "<br />&nbsp;&nbsp;* Informação um texto com pelo menos 3 caracteres.";
    }

    $vPublico = $GrupoTimeline["grt_publico"] ?? "";
    if (!($vPublico == 0 || $vPublico == 1)) {
        $strValida .= "<br />&nbsp;&nbsp;* Informação 'público' é inválida.";
    }

    $vRespId = $GrupoTimeline["grt_resposta_id"] ?? NULL;
    if ($vRespId != NULL && $vRespId < 0) {
        $strValida .= "<br />&nbsp;&nbsp;* Informação 'ID do Resposta' é inválida.";
    }
    // ===========================
    // valida grupo
    require_once(APPPATH."/models/TbGrupo.php");
    $retGru = pegaGrupo($vGruId);
    if ($retGru["erro"]) {
        $strValida .= "<br />&nbsp;&nbsp;* ".$retGru["msg"];
    } else {
        $Grupo     = $retGru["Grupo"] ?? array();
        $vGruAtivo = $Grupo["gru_ativo"] ?? 0;

        if ($vGruAtivo <> 1) {
            $strValida .= "<br />&nbsp;&nbsp;* Este grupo está inativo e não pode receber postagens.";
        }
    }
    // ============
    // valida grupo pessoa
    require_once(APPPATH."/models/TbGrupoPessoa.php");
    $retGP = pegaGrupoPessoa($vGrpId);
    if ($retGP["erro"]) {
        $strValida .= "<br />&nbsp;&nbsp;* ".$retGP["msg"];
    } else {
        $GrupoPessoa = $retGP["GrupoPessoa"] ?? array();
        $vGrpAtivo   = $GrupoPessoa["grp_ativo"] ?? 0;

        if ($vGrpAtivo <> 1) {
            $strValida .= "<br />&nbsp;&nbsp;* Você está inativo e não pode postar nesse grupo.";
        }
    }
    // ===================

    if ($strValida != "") {
        $strValida = "Corrija essas informações antes de prosseguir:<br />$strValida";
    }

    return $strValida;
}

function insereGrupoTimeline($GrupoTimeline)
{
    $arrRetorno          = [];
    $arrRetorno["erro"]  = false;
    $arrRetorno["msg"]   = "";
    $arrRetorno["grtId"] = "";

    $strValida = validaInsereGrupoTimeline($GrupoTimeline);
    if ($strValida != "") {
        $arrRetorno["erro"] = true;
        $arrRetorno["msg"]  = $strValida;

        return $arrRetorno;
    }

    # grt_id 	[grt_gru_id] 	[grt_grp_id] 	[grt_data] 	[grt_titulo] 	[grt_texto] 	[grt_publico] 	[grt_ativo] 	[grt_resposta_id]

    $vGruId   = $GrupoTimeline["grt_gru_id"] ?? NULL;
    $vGrpId   = $GrupoTimeline["grt_grp_id"] ?? NULL;
    $vData    = $GrupoTimeline["grt_data"] ?? NULL;
    $vTitulo  = $GrupoTimeline["grt_titulo"] ?? NULL;
    $vTexto   = $GrupoTimeline["grt_texto"] ?? NULL;
    $vPublico = $GrupoTimeline["grt_publico"] ?? 1;
    $vAtivo   = $GrupoTimeline["grt_ativo"] ?? 1;
    $vRespId  = $GrupoTimeline["grt_resposta_id"] ?? NULL;

    $CI = pega_instancia();
    $CI->load->database();

    $data = array(
        "grt_gru_id" => $vGruId,
        "grt_grp_id" => $vGrpId,
        "grt_data" => $vData,
        "grt_titulo" => $vTitulo,
        "grt_texto" => $vTexto,
        "grt_publico" => $vPublico,
        "grt_ativo" => $vAtivo,
        "grt_resposta_id" => $vRespId,
    );
    $ret  = $CI->db->insert('tb_grupo_timeline', $data);

    if (!$ret) {
        $error = $CI->db->error();

        $arrRetorno["erro"] = true;
        $arrRetorno["msg"]  = "Erro ao inserir Postagem. Mensagem: ".$error["message"];
    } else {
        $arrRetorno["erro"]  = false;
        $arrRetorno["msg"]   = "Postagem inserida com sucesso.";
        $arrRetorno["grtId"] = $CI->db->insert_id();
    }

    return $arrRetorno;
}

function pegaPostagensGrupo($gruId, $grpId = NULL, $apenasFavoritos=false, $limit = 50, $offset = 0)
{
  $arrRetorno              = [];
  $arrRetorno["erro"]      = false;
  $arrRetorno["msg"]       = "";
  $arrRetorno["postagens"] = [];
  $arrRetorno["salvos"]    = [];
  $arrRetorno["gruId"]     = $gruId;
  $arrRetorno["grpId"]     = $grpId;
  $arrRetorno["favoritos"] = $apenasFavoritos;
  $arrRetorno["limit"]     = $limit;
  $arrRetorno["offset"]    = $offset;
  $arrRetorno["step"]      = 50;
  $idUsuLogado             = pegaUsuarioLogadoId();

  // validacoes
  if (!is_numeric($gruId)) {
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "ID inválido para buscar postagens!";
    return $arrRetorno;
  }

  // valida grupo válido
  require_once(APPPATH."/models/TbGrupo.php");
  $retGrp = validaGrupo($gruId, $idUsuLogado);
  if($retGrp["erro"]){
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = $retGrp["msg"];

    return $arrRetorno;
  }
  // ===================

  $CI = pega_instancia();
  $CI->load->database();

  $CI->db->select('*');
  $CI->db->from('v_tb_grupo_timeline');
  if($apenasFavoritos){
    $CI->db->join('tb_grupo_timeline_salvo gts', "gts.gts_grt_id = grt_id");
    $CI->db->where("gts.gts_grp_id = $grpId");
  }
  $CI->db->where('grt_gru_id =', $gruId);
  $CI->db->where('grt_publico =', 1);
  $CI->db->where('grt_ativo =', 1);
  $CI->db->where('grt_resposta_id IS NULL');
  if(!$apenasFavoritos){
    if($grpId > 0){
      $CI->db->where('grp_id =', $grpId);
    } else {
      $CI->db->where('pet_cliente =', 1);
    }
  }
  $CI->db->order_by('grt_data', 'DESC');
  $CI->db->limit($limit, $offset);
  $query = $CI->db->get();

  if (!$query) {
      $arrRetorno["erro"] = true;
      $arrRetorno["msg"]  = "Erro ao carregar postagens desse grupo!";

      return $arrRetorno;
  }

  $arrGrtId   = [];
  $arrGrtId[] = 0;
  foreach ($query->result() as $row) {
    if (isset($row)) {
      $arrRetorno["postagens"][] = (array) $row;
      $arrGrtId[] = $row->grt_id;
    }
  }

  // todos os salvos
  $CI->db->select('gts_grt_id, gts_grp_id');
  $CI->db->from('tb_grupo_timeline_salvo');
  $CI->db->where('gts_grt_id IN ('.implode(",", $arrGrtId).')');
  $query2 = $CI->db->get();

  if ($query2) {
    foreach ($query2->result() as $row) {
      $grtId = $row->gts_grt_id;
      if (!array_key_exists($grtId, $arrRetorno["salvos"])) {
        $arrRetorno["salvos"][$grtId] = [];
      }

      $arrRetorno["salvos"][$grtId][$row->gts_grp_id] = true;
    }
  }
  // ===============

  // marca tds como lido
  $grpLogado = pegaGrupoPessoaLogadoId();
  $CI->db->trans_start();
  foreach ($arrGrtId as $vGrpId) {
    if($vGrpId > 0){
      $item = array(
        "gts_grt_id" => $vGrpId,
        "gts_grp_id" => $grpLogado,
        "gts_data"   => date("Y-m-d H:i:s"),
      );

      $insert_query2 = $CI->db->insert_string('tb_grupo_timeline_status', $item);
      $insert_query  = str_replace('INSERT INTO', 'INSERT IGNORE INTO', $insert_query2);
      $CI->db->query($insert_query);
    }
  }
  $CI->db->trans_complete();
  // ===================

  return $arrRetorno;
}

function deletaGrupoTimeline($vGrtId)
{
    $arrRetorno         = [];
    $arrRetorno["erro"] = false;
    $arrRetorno["msg"]  = "";

    if (!is_numeric($vGrtId)) {
        $arrRetorno["erro"] = true;
        $arrRetorno["msg"]  = "ID inválido para excluir Postagem!";
    } else {
        $CI = pega_instancia();
        $CI->load->database();

        // pega postagem
        $retGRT = pegaGrupoTimeline($vGrtId);
        if ($retGRT["erro"]) {
            $arrRetorno["erro"] = true;
            $arrRetorno["msg"]  = $retGRT["msg"];
        } else {
            $GrupoTimeline = $retGRT["GrupoTimeline"] ?? array();
            $gruId         = $GrupoTimeline["grt_gru_id"] ?? "";
            $pesId         = $GrupoTimeline["grp_pes_id"] ?? "";

            $usuLogadoId    = pegaUsuarioLogadoId();
            $gruLogadoId    = pegaGrupoLogadoId();
            $adminDeletando = ehAdminGrupo($gruId);
            
            if (($adminDeletando == false) && ($usuLogadoId != "" && $usuLogadoId != $pesId)) {
                $arrRetorno["erro"] = true;
                $arrRetorno["msg"]  = "Essa postagem não pertence a você!";
            } else if($gruLogadoId != "" && $gruLogadoId != $gruId){
                $arrRetorno["erro"] = true;
                $arrRetorno["msg"]  = "Essa postagem não pertence a esse grupo!";
            } else {
                $data = array(
                    "grt_ativo" => (int) 0
                );
                $CI->db->where('grt_id', $vGrtId);
                $ret  = $CI->db->update('tb_grupo_timeline', $data);

                if (!$ret) {
                    $error = $CI->db->error();

                    $arrRetorno["erro"] = true;
                    $arrRetorno["msg"]  = "Erro ao editar Postagem. Mensagem: ".$error["message"];
                } else {
                    $arrRetorno["erro"] = false;
                    $arrRetorno["msg"]  = "Postagem excluída com sucesso.";
                }
            }
        }
    }

    return $arrRetorno;
}
