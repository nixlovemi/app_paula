V_GLOB_URL_BASE = $('body').data('base-url');

function mostraNotificacao(titulo, mensagem, tipo)
{
  //type = ['', 'info', 'danger', 'success', 'warning', 'rose', 'primary'];
  //color = Math.floor((Math.random() * 6) + 1);
  
  var html = '';
  html    += '<h2 style="margin: 0 0 10px 0; font-size: unset; line-height: unset;"><b><u style="font-size: 30px !important;">'+titulo+'</u></b></h2>';
  html    += mensagem;

  $.notify({
    message: html
  }, {
    type: tipo,
    autoHide: false,
    clickToHide: false,
    placement: {
      from: 'top',
      align: 'left'
    },
    timer: 60000
    //position:"center"
  });
}

$( document ).ready(function()
{
  if( $('#dvMostraNotificacao').length > 0 ){
    var titulo   = $('#dvMostraNotificacao').data('titulo');
    var mensagem = $('#dvMostraNotificacao').html();
    var tipo     = $('#dvMostraNotificacao').data('tipo');
    
    if(titulo !== "" && mensagem !== "" && tipo !== ""){
      mostraNotificacao(titulo, mensagem, tipo);
    }
    
    $('#dvMostraNotificacao').remove();
  }
});

function mvc_post_ajax_var(controller, action, vars)
{
  $.ajax({
    url: V_GLOB_URL_BASE + controller + '/' + action,
    data:vars,
    type: 'POST',
    dataType: 'json',
    error: function () {
      mostraNotificacao('Aviso!', 'Erro ao carregar página.', 'danger');
    },
    beforeSend: function () {
      
    },
    success: function (data) {
      if(typeof data.html !== 'undefined' && typeof data.html_selector !== 'undefined'){
        if(data.html !== "" && data.html_selector !== ""){
          $(data.html_selector).html(data.html);
        }
      }
      
      if(typeof data.msg !== 'undefined' && typeof data.msg_titulo !== 'undefined' && typeof data.msg_tipo !== 'undefined'){
        if(data.msg !== "" && data.msg_titulo !== "" && data.msg_tipo !== ""){
          mostraNotificacao(data.msg_titulo, data.msg, data.msg_tipo);
        }
      }
      
      if(typeof data.callback !== 'undefined'){
        if(data.callback !== ""){
          setTimeout(data.callback, 200);
        }
      }
    }
  });
  
  
  
  /*if (typeof (hide_loading) === 'undefined')
    hide_loading = true;

  // MVC =========================
  return $.ajax({
    type: "POST",
    url: '_wis_app/loader.php',
    data: 'controller=' + controller + '&action=' + action + '&' + vars,
    global: false,
    async: false,
    beforeSend: function () {
      if (hide_loading) {
        show_loader();
      }
    },
    complete: function () {
      if (hide_loading) {
        hide_loader();
      }

      setTimeout("obj_jquerys();", 250);
    },
    success: function (data) {
      return data;
    }
  }).responseText;
  // =============================
  */
}

/* usuario cfg */
function jsonAddUsuCfg(usuario, configuracao, valor)
{
  var vars = 'usuId=' + usuario + '&cfgId=' + configuracao + '&valor=' + valor;
  mvc_post_ajax_var('UsuarioCfg', 'jsonAdd', vars);
}
/* =========== */

/* usuario */
async function jsonAlteraSenha(controller, action, id)
{
  const {value: password} = await Swal.fire({
    title: 'Informe a nova senha',
    input: 'password',
    inputPlaceholder: 'Nova senha ...',
    inputAttributes: {
      maxlength: 25,
      autocapitalize: 'off',
      autocorrect: 'off'
    }
  });

  if (password) {
    mvc_post_ajax_var(controller, action, 'id=' + id + '&nova_senha=' + password);
  } else {
    mostraNotificacao('Aviso!', 'Informe a nova senha para prosseguir com a alteração!', 'danger');
  }
}
/* ======= */