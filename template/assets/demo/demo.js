V_GLOB_URL_BASE = $('body').data('base-url');

// moment js pt-br locale
//! moment.js locale configuration
;(function (global, factory) {
   typeof exports === 'object' && typeof module !== 'undefined'
       && typeof require === 'function' ? factory(require('../moment')) :
   typeof define === 'function' && define.amd ? define(['../moment'], factory) :
   factory(global.moment)
}(this, (function (moment) { 'use strict';


    var ptBr = moment.defineLocale('pt-br', {
        months : 'Janeiro_Fevereiro_Março_Abril_Maio_Junho_Julho_Agosto_Setembro_Outubro_Novembro_Dezembro'.split('_'),
        monthsShort : 'Jan_Fev_Mar_Abr_Mai_Jun_Jul_Ago_Set_Out_Nov_Dez'.split('_'),
        weekdays : 'Domingo_Segunda-feira_Terça-feira_Quarta-feira_Quinta-feira_Sexta-feira_Sábado'.split('_'),
        weekdaysShort : 'Dom_Seg_Ter_Qua_Qui_Sex_Sáb'.split('_'),
        weekdaysMin : 'Do_2ª_3ª_4ª_5ª_6ª_Sá'.split('_'),
        weekdaysParseExact : true,
        longDateFormat : {
            LT : 'HH:mm',
            LTS : 'HH:mm:ss',
            L : 'DD/MM/YYYY',
            LL : 'D [de] MMMM [de] YYYY',
            LLL : 'D [de] MMMM [de] YYYY [às] HH:mm',
            LLLL : 'dddd, D [de] MMMM [de] YYYY [às] HH:mm'
        },
        calendar : {
            sameDay: '[Hoje às] LT',
            nextDay: '[Amanhã às] LT',
            nextWeek: 'dddd [às] LT',
            lastDay: '[Ontem às] LT',
            lastWeek: function () {
                return (this.day() === 0 || this.day() === 6) ?
                    '[Último] dddd [às] LT' : // Saturday + Sunday
                    '[Última] dddd [às] LT'; // Monday - Friday
            },
            sameElse: 'L'
        },
        relativeTime : {
            future : 'em %s',
            past : 'há %s',
            s : 'poucos segundos',
            ss : '%d segundos',
            m : 'um minuto',
            mm : '%d minutos',
            h : 'uma hora',
            hh : '%d horas',
            d : 'um dia',
            dd : '%d dias',
            M : 'um mês',
            MM : '%d meses',
            y : 'um ano',
            yy : '%d anos'
        },
        dayOfMonthOrdinalParse: /\d{1,2}º/,
        ordinal : '%dº'
    });

    return ptBr;

})));
// ======================

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
  
  init_components();
});

function init_components()
{
  $('.datepicker').datetimepicker({
    format: 'DD/MM/YYYY',
    icons: {
      time: "fa fa-clock-o",
      date: "fa fa-calendar",
      up: "fa fa-chevron-up",
      down: "fa fa-chevron-down",
      previous: 'fa fa-chevron-left',
      next: 'fa fa-chevron-right',
      today: 'fa fa-screenshot',
      clear: 'fa fa-trash',
      close: 'fa fa-remove'
    },
    locale: 'pt-br'
  });
}

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