<?php
function exibe_notificacao($html, $tipo="alert-info")
{
  return "
    <div class='alert $tipo'>
      <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
        <i class='material-icons'>close</i>
      </button>
      <span>
        $html
      </span>
    </div>
  ";
}

function exibe_info($html)
{
  return exibe_notificacao($html, "alert-info");
}

function exibe_success($html)
{
  return exibe_notificacao($html, "alert-success");
}

function exibe_warning($html)
{
  return exibe_notificacao($html, "alert-warning");
}

function exibe_danger($html)
{
  return exibe_notificacao($html, "alert-danger");
}

function exibe_primary($html)
{
  return exibe_notificacao($html, "alert-primary");
}