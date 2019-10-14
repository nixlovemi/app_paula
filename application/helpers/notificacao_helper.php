<?php
function exibe_notificacao($html, $tipo="alert-info", $close=true)
{
  $htmlClose = "<button type='button' class='close' data-dismiss='alert' aria-label='Close'><i class='material-icons'>close</i></button>";
  if(!$close){
    $htmlClose = "";
  }

  return "
    <div class='alert $tipo'>
      $htmlClose
      <span>
        $html
      </span>
    </div>
  ";
}

function exibe_info($html, $close=true)
{
  return exibe_notificacao($html, "alert-info", $close);
}

function exibe_success($html, $close=true)
{
  return exibe_notificacao($html, "alert-success", $close);
}

function exibe_warning($html, $close=true)
{
  return exibe_notificacao($html, "alert-warning", $close);
}

function exibe_danger($html, $close=true)
{
  return exibe_notificacao($html, "alert-danger", $close);
}

function exibe_primary($html, $close=true)
{
  return exibe_notificacao($html, "alert-primary", $close);
}

function exibe_gray($html, $close=true)
{
  return exibe_notificacao($html, "alert-gray", $close);
}