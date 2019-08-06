<?php
/**
 * tipo funcao privada
 */
function compressImage($caminhoImagem)
{
  $fileName = basename($caminhoImagem);
  $filePath = str_replace($fileName, "", $caminhoImagem);

  #require_once(APPPATH."/third_party/Compress.php");
  #$file           = $caminhoImagem; //file that you wanna compress
  #$new_name_image = $fileName; //name of new file compressed
  #$quality        = 70; // Value that I chose
  #$pngQuality     = 9; // Exclusive for PNG files
  #$destination    = $filePath; //This destination must be exist on your project
  #$maxsize        = 5245330; //Set maximum image size in bytes. if no value given 5mb by default.
  #$image_compress = new Compress($file, $new_name_image, $quality, $pngQuality, $destination, $maxsize);
  #$image_compress->compress_image();
  
  require_once(APPPATH."/helpers/utils_helper.php");
  resizeImage(1024, $caminhoImagem, $caminhoImagem); #já esta comprimindo JPG
}

/**
 * tipo funcao privada
 */
function convertVideo($caminhoImagem)
{
  exec("mencoder $caminhoImagem -o $caminhoImagem -oac copy -ovc lavc -lavcopts vcodec=mpeg1video -of mpeg");
}

/**
 * pega o $_FILES e faz as validacoes pra gravar
 * retorna um array com as info pra salvar
 */
function preConfereArquivos($vFiles, $grtId)
{
  $arrRetorno             = [];
  $arrRetorno["erro"]     = false;
  $arrRetorno["msg"]      = "";
  $arrRetorno["arquivos"] = "";

  // @todo talvez fazer a validacao dos tipos de arquivo aqui tb
  $qtItens = count($vFiles["arquivos"]["name"]);
  for($i=0; $i<$qtItens; $i++){
    $nomeOriginal     = $vFiles["arquivos"]["name"][$i];
    $pathTemporario   = $vFiles["arquivos"]["tmp_name"][$i];
    #$tipoArquivo      = $vFiles["arquivos"]["type"][$i];
    #$tamanhoKbArquivo = $vFiles["arquivos"]["size"][$i] / 1000;
    
    // ve se pasta existe; senao cria
    if(!file_exists(PASTA_UPLOAD . $grtId)){
      mkdir(PASTA_UPLOAD . $grtId);
    }
    // ==============================

    require_once(APPPATH."/helpers/utils_helper.php");
    $info             = new SplFileInfo($nomeOriginal);
    $extensaoArquivo  = strtolower($info->getExtension());
    $nomeNovo         = sanitize_file_name($nomeOriginal);
    $caminhoNovo      = PASTA_UPLOAD . $grtId . "/" . $nomeNovo;

    $ret = move_uploaded_file($pathTemporario, $caminhoNovo);
    if($ret){
      if($extensaoArquivo == "jpg"){
        compressImage($caminhoNovo);
      } else if($extensaoArquivo == "avi"){
        convertVideo($caminhoNovo);
      }

      $arrRetorno["arquivos"][] = array(
        "caminho" => str_replace(FCPATH, "", $caminhoNovo)
      );
    }
  }

  return $arrRetorno;
}

/**
 * $arrArquivos vem da funcao preConfereArquivos
 */
function insereArquivos($grtId, $arrArquivos)
{
  $arrRetorno         = [];
  $arrRetorno["erro"] = false;
  $arrRetorno["msg"]  = "";

  $arrBatchInsert = [];
  foreach($arrArquivos as $arquivo){
    $arrBatchInsert[] = array(
      "gta_grt_id"  => $grtId,
      "gta_caminho" => $arquivo["caminho"] ?? ""
    );
  }

  if (count($arrBatchInsert) > 0) {
    require_once(APPPATH."/helpers/utils_helper.php");
    $CI = pega_instancia();
    $CI->load->database();

    $retInsert = $CI->db->insert_batch('tb_grupo_timeline_arquivos', $arrBatchInsert);
    if ($retInsert === false) {
      $arrRetorno["erro"] = true;
      $arrRetorno["msg"]  = "Erro ao fazer upload dos arquivos do post!";
    } else {
      $arrRetorno["erro"] = false;
      $arrRetorno["msg"]  = "Upload dos arquivos do post realizado com sucesso!";
    }
  }

  return $arrRetorno;
}

/**
 * $arrPostagens vem da função pegaPostagensGrupo
 */
function pegaArquivos($arrPostagens)
{
  $arrRetorno             = [];
  $arrRetorno["erro"]     = false;
  $arrRetorno["msg"]      = "";
  $arrRetorno["arquivos"] = [];
  
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

  $CI->db->select('gta_id, gta_grt_id, gta_caminho');
  $CI->db->from('tb_grupo_timeline_arquivos');
  $CI->db->where('gta_grt_id IN ('.implode(",", $arrGrtId).')');
  $query = $CI->db->get();
  
  if(!$query){
    $arrRetorno["erro"] = true;
    $arrRetorno["msg"]  = "Erro ao encontrar arquivos das postagens desse grupo!";

    return $arrRetorno;
  }

  foreach ($query->result() as $row) {
    if (isset($row)) {
      $vGtaGrtId = $row->gta_grt_id ?? "";
      if($vGtaGrtId != ""){
        if(!array_key_exists($vGtaGrtId, $arrRetorno["arquivos"])){
          $arrRetorno["arquivos"][$vGtaGrtId] = [];
          $arrRetorno["arquivos"][$vGtaGrtId]["imagens"]    = []; #jpg, png
          $arrRetorno["arquivos"][$vGtaGrtId]["audio"]      = []; #mp3, ogg
          $arrRetorno["arquivos"][$vGtaGrtId]["video"]      = [];
          $arrRetorno["arquivos"][$vGtaGrtId]["documentos"] = []; #doc, xls, pdf ...
        }

        $arrArquivo = array(
          "gta_id" => $row->gta_id ?? "",
          "gta_caminho" => $row->gta_caminho ?? ""
        );

        $caminhoArquivo = FCPATH . $row->gta_caminho;
        if( exif_imagetype($caminhoArquivo) !== false ){
          $textoIdx = "imagens";
        } else if( eh_audio($caminhoArquivo) !== false ){
          $textoIdx = "audio";
        } else if(eh_video($caminhoArquivo)){
          $textoIdx = "video";
        }else {
          $textoIdx = "documentos";
        }

        $arrRetorno["arquivos"][$vGtaGrtId][$textoIdx][] = $arrArquivo;
      }
    }
  }

  //@todo talvez tratar erros
  return $arrRetorno;
}