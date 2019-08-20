<?php

function pega_instancia()
{
  $CI = & get_instance();
  return $CI;
}

function encripta_string($string)
{
  $retString = md5(SALT_KEY.$string);
  return $retString;
}

function valida_email($email)
{
  $ret = filter_var($email, FILTER_VALIDATE_EMAIL);
  return $ret;
}

function valida_senha($senha)
{
  $arrRet = [];
  $arrRet["erro"] = false;
  $arrRet["msg"]  = "";

  $uppercase = preg_match('@[A-Z]@', $senha);
  $lowercase = preg_match('@[a-z]@', $senha);
  $number    = preg_match('@[0-9]@', $senha);

  if (!$uppercase || !$lowercase || !$number || strlen($senha) < 8) {
    $arrRet["erro"] = true;
    $arrRet["msg"]  = "A senha deve conter letras e números, ter pelo menos 8 caracteres e um caracter maiúsculo!";
  }

  return $arrRet;
}

function array_ret_para_retorno($arrRet)
{
  return (object) $arrRet;
}

function processaPost()
{
  $postdata = file_get_contents("php://input");
  $jsonVars = [];

  if ($postdata != "") {
    parse_str($postdata, $jsonVars);
  } else {
    parse_str($_REQUEST, $jsonVars);
  }

  return (object) $jsonVars;
}

function processaJsonHtml($html)
{
  $htmlAjustado  = str_replace("'", "\'", $html);
  $htmlAjustado2 = str_replace(array("\r","\n"),"", $htmlAjustado);

  return $htmlAjustado2;
}

function gera_titulo_template($titulo, $href="javascript:;")
{
  return "<a class='navbar-brand' href='$href'>$titulo<div class='ripple-container'></div></a>";
}

/**
 * type = ['', 'info', 'danger', 'success', 'warning', 'rose', 'primary'];
*/
function geraNotificacao($titulo, $mensagem, $tipo)
{
  $CI = pega_instancia();
  $CI->session->set_flashdata('geraNotificacao', array("titulo"=>$titulo, "mensagem"=>$mensagem, "tipo"=>$tipo));
}

/**
 * pes_id, usu_id
 */
function pegaUsuarioLogadoId()
{
  $Usuario = $_SESSION["usuario_info"] ?? null;
  $id      = $Usuario->id ?? null;

  return $id;
}

function pegaGrupoLogadoId()
{
  $gruId = NULL;
  $grpId = $_SESSION["grp_id"] ?? null;
  
  if($grpId != NULL){
    require_once(APPPATH."/models/TbGrupoPessoa.php");
    $retGrp = pegaGrupoPessoa($grpId);
    if(!$retGrp["erro"]){
      $GrupoPessoa = $retGrp["GrupoPessoa"];
      $gruId       = $GrupoPessoa["grp_gru_id"] ?? NULL;
    }
  }

  return $gruId;
}

function pegaGrupoPessoaLogadoId()
{
  $grpId = $_SESSION["grp_id"] ?? NULL;
  return $grpId;
}

function pegaFotoLogado()
{
  $img = $_SESSION["foto"] ?? "";
  if($img == ""){
    $img = FOTO_DEFAULT;
  }

  return BASE_URL . $img;
}

function pegaControllerAction()
{
  $CI = pega_instancia();

  $arrRet = [];
  $arrRet["controller"] = "";
  $arrRet["action"]     = "";
  $arrRet["vars"]       = []; // na ordem que aparece na URL

  $i = 1;
  foreach($CI->router->uri->segments as $info){
    if($i == 1){
      $arrRet["controller"] = $info;
    } else if($i == 2){
      $arrRet["action"] = $info;
    } else {
      $arrRet["vars"][] = $info;
    }

    $i++;
  }

  return $arrRet;
}

function ehAdminGrupo($gruId)
{
  require_once(APPPATH."/models/TbGrupo.php");
  $retGrupo   = pegaGrupo($gruId);
  $Grupo      = (!$retGrupo["erro"]) ? $retGrupo["Grupo"]: array();
  $vGruUsuId  = $Grupo["gru_usu_id"] ?? "";
  $UsuarioLog = $_SESSION["usuario_info"] ?? array();
  $usuLogado  = $UsuarioLog->id ?? "";
  $cliente    = $UsuarioLog->cliente ?? 1;

  return ($vGruUsuId == $usuLogado) && ($cliente == 0);
}

function acerta_moeda($strInput)
{
    $str = trim($strInput);

    if (strlen($str) <= 0) {
        return null;
    }

    $str = str_replace(".", "", $str);
    $str = str_replace(",", ".", $str);
    $str = str_replace("R$", "", $str);
    $str = str_replace("US$", "", $str);
    $str = str_replace("U$", "", $str);
    $str = str_replace("$", "", $str);
    $str = str_replace(" ", "", $str);
    return $str;
}

function acerta_data($dt)
{
  if (!preg_match('/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/', $dt)){
    return null;
  }
  $temp = explode('/', $dt);
  return $temp [2].'-'.$temp [1].'-'.$temp [0];
}

/**
 * Pega data hora no formato DD/MM/YYYY HH:MI:SS e retorna YYYY-MM-DD HH:MI:SS
 * @param text $dt
 * @return string
 */
function acerta_data_hora($dt)
{
  //if (!preg_match('/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})\s([0-9]{2}):([0-9]{2})$/', $dt))
  //  return null;
  $hora = substr($dt, 11, 8);
  $data = explode('/', substr($dt, 0, 10));
  $data = $data [2].'-'.$data [1].'-'.$data [0].' '.$hora;
  return $data;
}

function isValidDate($date, $format = 'Y-m-d H:i:s')
{
  $d = DateTime::createFromFormat($format, $date);
  return $d && $d->format($format) == $date;
}

function resizeImage($newWidth, $targetFile, $originalFile)
{
  $info = getimagesize($originalFile);
  $mime = $info['mime'];
  switch ($mime) {
    case 'image/jpeg':
      $image_create_func = 'imagecreatefromjpeg';
      $image_save_func   = 'imagejpeg';
      $new_image_ext     = 'jpg';
      $quality           = '75';
      break;
    case 'image/png':
      $image_create_func = 'imagecreatefrompng';
      $image_save_func   = 'imagepng';
      $new_image_ext     = 'png';
      $quality           = '5';
      break;
    case 'image/gif':
      $image_create_func = 'imagecreatefromgif';
      $image_save_func   = 'imagegif';
      $new_image_ext     = 'gif';
      $quality           = '70';
      break;
    default:
      throw new Exception('Unknown image type.');
  }
  $img       = $image_create_func($originalFile);
  list($width, $height) = getimagesize($originalFile);
  $newHeight = ($height / $width) * $newWidth;
  $tmp       = imagecreatetruecolor($newWidth, $newHeight);
  imagecopyresampled($tmp, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
  if (file_exists($targetFile)) {
    unlink($targetFile);
  }
  $image_save_func($tmp, "$targetFile", $quality);
}

function eh_audio($caminho)
{
  $allowed = array(
    'audio/mpeg', 'audio/x-mpeg', 'audio/mpeg3', 'audio/x-mpeg-3', 'audio/aiff',
    'audio/mid', 'audio/x-aiff', 'audio/x-mpequrl','audio/midi', 'audio/x-mid',
    'audio/x-midi','audio/wav','audio/x-wav','audio/xm','audio/x-aac','audio/basic',
    'audio/flac','audio/mp4','audio/x-matroska','audio/ogg','audio/s3m','audio/x-ms-wax',
    'audio/xm'
  );

  // check REAL MIME type
  $finfo = finfo_open(FILEINFO_MIME_TYPE);
  $type = finfo_file($finfo, $caminho );
  finfo_close($finfo);

  // check to see if REAL MIME type is inside $allowed array
  if( in_array($type, $allowed) ) {
      return true;
  } else {
      return false;
  }
}

function eh_video($caminho)
{
  $mime = mime_content_type($caminho);
  if(strstr($mime, "video/")){
    return true;
  } else {
    return false;
  }
}

function eh_link_youtube($link)
{
  $ehYoutube = strpos($link, "youtu.be") !== false || strpos($link, "youtube.com") !== false;
  return $ehYoutube;
}

function pegaStrLinkYoutube($link)
{
  // @todo talvez melhorar a logica da analise do link
  $ehEncurtado = strpos($link, "youtu.be") !== false;
  $ehEmbed     = strpos($link, "youtube.com/embed/") !== false;
  if($ehEncurtado || $ehEmbed){
    $arrLink = explode("/", $link);
    return end($arrLink);
  } else {
    $arrKeys = parse_url($link);
    $strKeys = $arrKeys["query"] ?? "";
    parse_str($strKeys);
    return $v ?? "";
  }
}

function sanitize_file_name($filename)
{
  $filename_raw  = $filename;
  $special_chars = array("?", "[", "]", "/", "\\", "=", "<", ">", ":", ";", ",", "'", "\"", "&", "$", "#", "*", "(", ")", "|", "~", "`", "!", "{", "}");
  $special_chars = apply_filters('sanitize_file_name_chars', $special_chars, $filename_raw);
  $filename      = str_replace($special_chars, '', $filename);
  $filename      = preg_replace('/[\s-]+/', '-', $filename);
  $filename      = trim($filename, '.-_');
  return apply_filters('sanitize_file_name', $filename, $filename_raw);
}

/**
 * é função do WP
 */
function apply_filters($tag, $value)
{
  global $wp_filter, $wp_current_filter;

  $args = array();

  // Do 'all' actions first.
  if (isset($wp_filter['all'])) {
    $wp_current_filter[] = $tag;
    $args                = func_get_args();
    _wp_call_all_hook($args);
  }

  if (!isset($wp_filter[$tag])) {
    if (isset($wp_filter['all'])) {
      array_pop($wp_current_filter);
    }
    return $value;
  }

  if (!isset($wp_filter['all'])) {
    $wp_current_filter[] = $tag;
  }

  if (empty($args)) {
    $args = func_get_args();
  }

  // don't pass the tag name to WP_Hook
  array_shift($args);

  $filtered = $wp_filter[$tag]->apply_filters($value, $args);

  array_pop($wp_current_filter);

  return $filtered;
}
