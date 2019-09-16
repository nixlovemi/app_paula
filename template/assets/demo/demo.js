V_GLOB_URL_BASE = $('body').data('base-url');
V_CROPPIE       = null;

// moment js pt-br locale
!function(e,d){"object"==typeof exports&&"undefined"!=typeof module&&"function"==typeof require?d(require("../moment")):"function"==typeof define&&define.amd?define(["../moment"],d):d(e.moment)}(this,function(e){"use strict";return e.defineLocale("pt-br",{months:"Janeiro_Fevereiro_Março_Abril_Maio_Junho_Julho_Agosto_Setembro_Outubro_Novembro_Dezembro".split("_"),monthsShort:"Jan_Fev_Mar_Abr_Mai_Jun_Jul_Ago_Set_Out_Nov_Dez".split("_"),weekdays:"Domingo_Segunda-feira_Terça-feira_Quarta-feira_Quinta-feira_Sexta-feira_Sábado".split("_"),weekdaysShort:"Dom_Seg_Ter_Qua_Qui_Sex_Sáb".split("_"),weekdaysMin:"Do_2ª_3ª_4ª_5ª_6ª_Sá".split("_"),weekdaysParseExact:!0,longDateFormat:{LT:"HH:mm",LTS:"HH:mm:ss",L:"DD/MM/YYYY",LL:"D [de] MMMM [de] YYYY",LLL:"D [de] MMMM [de] YYYY [às] HH:mm",LLLL:"dddd, D [de] MMMM [de] YYYY [às] HH:mm"},calendar:{sameDay:"[Hoje às] LT",nextDay:"[Amanhã às] LT",nextWeek:"dddd [às] LT",lastDay:"[Ontem às] LT",lastWeek:function(){return 0===this.day()||6===this.day()?"[Último] dddd [às] LT":"[Última] dddd [às] LT"},sameElse:"L"},relativeTime:{future:"em %s",past:"há %s",s:"poucos segundos",ss:"%d segundos",m:"um minuto",mm:"%d minutos",h:"uma hora",hh:"%d horas",d:"um dia",dd:"%d dias",M:"um mês",MM:"%d meses",y:"um ano",yy:"%d anos"},dayOfMonthOrdinalParse:/\d{1,2}º/,ordinal:"%dº"})});
// ======================

// numeric js ===========
!function(e){"function"==typeof define&&define.amd?define(["jquery"],e):e(window.jQuery)}(function(e){e.fn.numeric=function(t,n){"boolean"==typeof t&&(t={decimal:t,negative:!0,decimalPlaces:-1}),void 0===(t=t||{}).negative&&(t.negative=!0);var i=!1===t.decimal?"":t.decimal||".",r=!0===t.negative,a=void 0===t.decimalPlaces?-1:t.decimalPlaces;return n="function"==typeof n?n:function(){},this.data("numeric.decimal",i).data("numeric.negative",r).data("numeric.callback",n).data("numeric.decimalPlaces",a).keypress(e.fn.numeric.keypress).keyup(e.fn.numeric.keyup).blur(e.fn.numeric.blur)},e.fn.numeric.keypress=function(t){var n=e.data(this,"numeric.decimal"),i=e.data(this,"numeric.negative"),r=e.data(this,"numeric.decimalPlaces"),a=t.charCode?t.charCode:t.keyCode?t.keyCode:0;if(13==a&&"input"==this.nodeName.toLowerCase())return!0;if(13==a)return!1;if(35==a||36==a||37==a)return!1;var c=!1;if(t.ctrlKey&&97==a||t.ctrlKey&&65==a)return!0;if(t.ctrlKey&&120==a||t.ctrlKey&&88==a)return!0;if(t.ctrlKey&&99==a||t.ctrlKey&&67==a)return!0;if(t.ctrlKey&&122==a||t.ctrlKey&&90==a)return!0;if(t.ctrlKey&&118==a||t.ctrlKey&&86==a||t.shiftKey&&45==a)return!0;if(a<48||a>57){var l=e(this).val();if(0!==e.inArray("-",l.split(""))&&i&&45==a&&(0===l.length||0===parseInt(e.fn.getSelectionStart(this),10)))return!0;n&&a==n.charCodeAt(0)&&-1!=e.inArray(n,l.split(""))&&(c=!1),8!=a&&9!=a&&13!=a&&35!=a&&36!=a&&37!=a&&39!=a&&46!=a?c=!1:void 0!==t.charCode&&(t.keyCode==t.which&&0!==t.which?(c=!0,46==t.which&&(c=!1)):0!==t.keyCode&&0===t.charCode&&0===t.which&&(c=!0)),n&&a==n.charCodeAt(0)&&(c=-1==e.inArray(n,l.split("")))}else if(c=!0,n&&r>0){var u=e.fn.getSelectionStart(this),s=e.fn.getSelectionEnd(this),d=e.inArray(n,e(this).val().split(""));u===s&&d>=0&&u>d&&e(this).val().length>d+r&&(c=!1)}return c},e.fn.numeric.keyup=function(t){var n=e(this).val();if(n&&n.length>0){var i=e.fn.getSelectionStart(this),r=e.fn.getSelectionEnd(this),a=e.data(this,"numeric.decimal"),c=e.data(this,"numeric.negative"),l=e.data(this,"numeric.decimalPlaces");if(""!==a&&null!==a)0===(m=e.inArray(a,n.split("")))&&(this.value="0"+n,i++,r++),1==m&&"-"==n.charAt(0)&&(this.value="-0"+n.substring(1),i++,r++),n=this.value;for(var u=[0,1,2,3,4,5,6,7,8,9,"-",a],s=n.length,d=s-1;d>=0;d--){var f=n.charAt(d);0!==d&&"-"==f?n=n.substring(0,d)+n.substring(d+1):0!==d||c||"-"!=f||(n=n.substring(1));for(var o=!1,h=0;h<u.length;h++)if(f==u[h]){o=!0;break}o&&" "!=f||(n=n.substring(0,d)+n.substring(d+1))}var m,v=e.inArray(a,n.split(""));if(v>0)for(var g=s-1;g>v;g--){n.charAt(g)==a&&(n=n.substring(0,g)+n.substring(g+1))}if(a&&l>0)(m=e.inArray(a,n.split("")))>=0&&(n=n.substring(0,m+l+1),r=Math.min(n.length,r));this.value=n,e.fn.setSelection(this,[i,r])}},e.fn.numeric.blur=function(){var t=e.data(this,"numeric.decimal"),n=e.data(this,"numeric.callback"),i=e.data(this,"numeric.negative"),r=this.value;""!==r&&(new RegExp("^"+(i?"-?":"")+"\\d+$|^"+(i?"-?":"")+"\\d*"+t+"\\d+$").exec(r)||n.apply(this))},e.fn.removeNumeric=function(){return this.data("numeric.decimal",null).data("numeric.negative",null).data("numeric.callback",null).data("numeric.decimalPlaces",null).unbind("keypress",e.fn.numeric.keypress).unbind("keyup",e.fn.numeric.keyup).unbind("blur",e.fn.numeric.blur)},e.fn.getSelectionStart=function(e){if("number"!==e.type){if(e.createTextRange&&document.selection){var t=document.selection.createRange().duplicate();return t.moveEnd("character",e.value.length),""==t.text?e.value.length:Math.max(0,e.value.lastIndexOf(t.text))}try{return e.selectionStart}catch(e){return 0}}},e.fn.getSelectionEnd=function(e){if("number"!==e.type){if(e.createTextRange&&document.selection){var t=document.selection.createRange().duplicate();return t.moveStart("character",-e.value.length),t.text.length}return e.selectionEnd}},e.fn.setSelection=function(e,t){if("number"==typeof t&&(t=[t,t]),t&&t.constructor==Array&&2==t.length)if("number"===e.type)e.focus();else if(e.createTextRange){var n=e.createTextRange();n.collapse(!0),n.moveStart("character",t[0]),n.moveEnd("character",t[1]-t[0]),n.select()}else{e.focus();try{e.setSelectionRange&&e.setSelectionRange(t[0],t[1])}catch(e){}}}});
// ======================

// mask money js ========
!function(e){"use strict";e.browser||(e.browser={},e.browser.mozilla=/mozilla/.test(navigator.userAgent.toLowerCase())&&!/webkit/.test(navigator.userAgent.toLowerCase()),e.browser.webkit=/webkit/.test(navigator.userAgent.toLowerCase()),e.browser.opera=/opera/.test(navigator.userAgent.toLowerCase()),e.browser.msie=/msie/.test(navigator.userAgent.toLowerCase()));var t={destroy:function(){return e(this).unbind(".maskMoney"),e.browser.msie&&(this.onpaste=null),this},mask:function(t){return this.each(function(){var n,a=e(this);return"number"==typeof t&&(a.trigger("mask"),n=e(a.val().split(/\D/)).last()[0].length,t=t.toFixed(n),a.val(t)),a.trigger("mask")})},unmasked:function(){return this.map(function(){var t,n=e(this).val()||"0",a=-1!==n.indexOf("-");return e(n.split(/\D/).reverse()).each(function(e,n){if(n)return t=n,!1}),n=(n=n.replace(/\D/g,"")).replace(new RegExp(t+"$"),"."+t),a&&(n="-"+n),parseFloat(n)})},init:function(t){return t=e.extend({prefix:"",suffix:"",affixesStay:!0,thousands:",",decimal:".",precision:2,allowZero:!1,allowNegative:!1},t),this.each(function(){var n,a=e(this);function r(){var e,t,n,r,o,i=a.get(0),s=0,l=0;return"number"==typeof i.selectionStart&&"number"==typeof i.selectionEnd?(s=i.selectionStart,l=i.selectionEnd):(t=document.selection.createRange())&&t.parentElement()===i&&(r=i.value.length,e=i.value.replace(/\r\n/g,"\n"),(n=i.createTextRange()).moveToBookmark(t.getBookmark()),(o=i.createTextRange()).collapse(!1),n.compareEndPoints("StartToEnd",o)>-1?s=l=r:(s=-n.moveStart("character",-r),s+=e.slice(0,s).split("\n").length-1,n.compareEndPoints("EndToEnd",o)>-1?l=r:(l=-n.moveEnd("character",-r),l+=e.slice(0,l).split("\n").length-1))),{start:s,end:l}}function o(e){var n="";return e.indexOf("-")>-1&&(e=e.replace("-",""),n="-"),n+t.prefix+e+t.suffix}function i(e){var n,a,r,i=e.indexOf("-")>-1&&t.allowNegative?"-":"",s=e.replace(/[^0-9]/g,""),l=s.slice(0,s.length-t.precision);return""===(l=(l=l.replace(/^0*/g,"")).replace(/\B(?=(\d{3})+(?!\d))/g,t.thousands))&&(l="0"),n=i+l,t.precision>0&&(a=s.slice(s.length-t.precision),r=new Array(t.precision+1-a.length).join(0),n+=t.decimal+r+a),o(n)}function s(e){var t,n,r=a.val().length;a.val(i(a.val())),t=a.val().length,n=e-=r-t,a.each(function(e,t){if(t.setSelectionRange)t.focus(),t.setSelectionRange(n,n);else if(t.createTextRange){var a=t.createTextRange();a.collapse(!0),a.moveEnd("character",n),a.moveStart("character",n),a.select()}})}function l(){var e=a.val();a.val(i(e))}function c(e){e.preventDefault?e.preventDefault():e.returnValue=!1}function u(n){var o,i,l,u,v,g,f=(n=n||window.event).which||n.charCode||n.keyCode;return void 0!==f&&(f<48||f>57?45===f?(a.val((g=a.val(),t.allowNegative?""!==g&&"-"===g.charAt(0)?g.replace("-",""):"-"+g:g)),!1):43===f?(a.val(a.val().replace("-","")),!1):13===f||9===f||(!(!e.browser.mozilla||37!==f&&39!==f||0!==n.charCode)||(c(n),!0)):!!function(){var e=!(a.val().length>=a.attr("maxlength")&&a.attr("maxlength")>=0),t=r(),n=t.start,o=t.end,i=!(t.start===t.end||!a.val().substring(n,o).match(/\d/)),s="0"===a.val().substring(0,1);return e||i||s}()&&(c(n),o=String.fromCharCode(f),l=(i=r()).start,u=i.end,v=a.val(),a.val(v.substring(0,l)+o+v.substring(u,v.length)),s(l+1),!1))}function v(){setTimeout(function(){l()},0)}function g(){return(parseFloat("0")/Math.pow(10,t.precision)).toFixed(t.precision).replace(new RegExp("\\.","g"),t.decimal)}t=e.extend(t,a.data()),a.unbind(".maskMoney"),a.bind("keypress.maskMoney",u),a.bind("keydown.maskMoney",function(e){var n,o,i,l,u,v=(e=e||window.event).which||e.charCode||e.keyCode;return void 0!==v&&(o=(n=r()).start,i=n.end,8!==v&&46!==v&&63272!==v||(c(e),l=a.val(),o===i&&(8===v?""===t.suffix?o-=1:(u=l.split("").reverse().join("").search(/\d/),i=1+(o=l.length-u-1)):i+=1),a.val(l.substring(0,o)+l.substring(i,l.length)),s(o),!1))}),a.bind("blur.maskMoney",function(r){if(e.browser.msie&&u(r),""===a.val()||a.val()===o(g()))t.allowZero?t.affixesStay?a.val(o(g())):a.val(g()):a.val("");else if(!t.affixesStay){var i=a.val().replace(t.prefix,"").replace(t.suffix,"");a.val(i)}a.val()!==n&&a.change()}),a.bind("focus.maskMoney",function(){n=a.val(),l();var e,t=a.get(0);t.createTextRange&&((e=t.createTextRange()).collapse(!1),e.select())}),a.bind("click.maskMoney",function(){var e,t=a.get(0);t.setSelectionRange?(e=a.val().length,t.setSelectionRange(e,e)):a.val(a.val())}),a.bind("cut.maskMoney",v),a.bind("paste.maskMoney",v),a.bind("mask.maskMoney",l)})}};e.fn.maskMoney=function(n){return t[n]?t[n].apply(this,Array.prototype.slice.call(arguments,1)):"object"!=typeof n&&n?void e.error("Method "+n+" does not exist on jQuery.maskMoney"):t.init.apply(this,arguments)}}(window.jQuery||window.Zepto);
// ======================

function pegaMaxZindex()
{
  var index_highest = 0;
  // Search all elements with '*'
  $("*").each(function() {
      var index_current = parseInt($(this).css("zIndex"), 10);
      if (index_current > index_highest) {
          index_highest = index_current;
      }
  });
  
  return index_highest;
}

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
  
  var funcao = function(){
    var maxZindex   = pegaMaxZindex();
    var alertZindex = maxZindex + 5;
    $('div.alert').css({'z-index':alertZindex});
  };
  setTimeout(funcao, 650);
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
  
  // fcbk grid
  $( "div.fcbkGrid" ).each(function() {
    var arrImagens = [];
    $(this).find('input.img_url').each(function() {
      arrImagens.push( $(this).val() );
    });
    
    $(this).imagesGrid({
      images     : arrImagens,
      align      : true,
      onModalOpen: function($modal, image) {
        setTimeout(function(){
          var maxZindex   = pegaMaxZindex();
          var alertZindex = maxZindex + 5;
          $('.imgs-grid-modal').css({'z-index':alertZindex});
        }, 600)
      }
    }).show();
  });
  // =========
  
  if( $('.grupo-staff-notificacao').length > 0 ){
    atualizaNotificacaoStaff();
    setInterval(
      "atualizaNotificacaoStaff()"
      ,25000);
  }
  
  init_components();
});

function atualizaNotificacaoStaff()
{
  $.ajax({type: 'POST',
    contentType: false,
    processData: false,
    url: V_GLOB_URL_BASE + 'Json' + '/' + 'jsonAtualizaNotificacaoStaff',
    dataType: 'json',
    success: function (data) {
      if(data != null && !data.erro){
        $(".grupo-staff-notificacao").each(function() {
          var spanId         = $(this).data("id");
          var cntNotificacao = data.notificacao[spanId];
          if(typeof cntNotificacao == "undefined"){
            $(this).hide();
          } else {
            $(this).html(cntNotificacao).show();
          }
        });
      }
    }
  });
}

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
  $('.datepicker_time').datetimepicker({
    format: 'DD/MM/YYYY HH:mm',
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
  
  try {
    // inteiro
    $(".txt_inteiro").numeric();
    // =======
    
    // money
    $(".txt_moeda").each(function() {
      var decimais  = $(this).data("decimais");
      var pode_zero = $(this).data("pode-zero");
      var pode_neg  = $(this).data("pode-neg");
      
      if(typeof decimais === "undefined"){
        decimais = 2;
      }
      if(typeof pode_zero === "undefined"){
        pode_zero = false;
      }
      if(typeof pode_neg === "undefined"){
        pode_neg = false;
      }
      
      $(this).maskMoney({
        /*prefix:'US$ ', // The symbol to be displayed before the value entered by the user*/
        allowZero: pode_zero, // Prevent users from inputing zero
        allowNegative: pode_neg, // Prevent users from inputing negative values
        defaultZero: false, // when the user enters the field, it sets a default mask using zero
        thousands: '.', // The thousands separator
        decimal: ',', // The decimal separator
        precision: decimais, // How many decimal places are allowed
        affixesStay: false, // set if the symbol will stay in the field after the user exits the field.
        symbolPosition: 'left' // use this setting to position the symbol at the left or right side of the value. default 'left'
      });
    });
    // =====
    
    // input password
    $("input[type='password']").each(function() {
      var objVerPass = $(this).next('.password_show_text');
      if( objVerPass.length <= 0 ){
        var paiMB = $(this).css('margin-bottom');
        
        $(this).after('<span class="password_show_text"><a href="javascript:;" class="text-info">Exibir senha</a></span>');
        var objVerPass = $(this).next('.password_show_text');
        objVerPass.css({'position':'relative', 'top':'-'+paiMB, 'font-size':'11.5px'});
      }
    });
    // ==============
    
    // audio video
    $(function () {
      //$('audio, video').stylise();
      $('audio').stylise();
    });
    
    $(".postagem-inner .post-video").each(function() {
      $(this).responsive(true);
    });
    // ===========
    
    $('.inpt-celular-ddd').focusout(function ()
    {
      var phone, element;
      element = $(this);
      element.unmask();
      phone = element.val().replace(/\D/g, '');
      if (phone.length > 10) {
          element.mask("(99)99999-999?9");
      } else {
          element.mask("(99)9999-9999?9");
      }
    }).trigger('focusout');
    
    // esquema de filtrar
    // exemplo Json->jsonCidadeSeleciona
    var clInputSelecionaModal  = '.inpt-seleciona-modal';
    var clSearchSelecionaModal = 'inpt-seleciona-modal-icon';
    var clClearSelecionaModal  = 'inpt-limpa-modal-icon';
    var clHddnSelecionaModal   = 'inpt-seleciona-modal-hidden';
    var clFilterSelecionaModal = 'text_search_seleciona_modal';
    var clDvRetSelecionaModal  = 'dv-ret-inpt-seleciona-modal-icon';
    var clRadioSelecionaModal  = 'radio_seleciona_modal';
    var clListaSelecionaModal  = 'ListaSelecionaModal';
    
    $(clInputSelecionaModal).each(function()
    {
      var ehDesabilitado = $(this).is(':disabled');
      var ehReadonly     = $(this).attr('readonly') == "readonly";
      
      if(!ehDesabilitado && !ehReadonly){
        var id        = $(this).data('id');
        var name      = $(this).attr("name");
        var htmlIcon  = '<i title="Pesquisar" class="material-icons '+clSearchSelecionaModal+'">search</i>';
        var htmlClear = '<i title="Limpar" class="material-icons '+clClearSelecionaModal+'">clear</i>';
        var htmlHddn  = '<input class="'+clHddnSelecionaModal+'" type="hidden" name="'+name+'_id" value="'+id+'" />';
        $(this).attr('readonly', 'readonly').after(htmlIcon + htmlClear + htmlHddn);
      }
    });
    
    $(document).on('click','.'+clSearchSelecionaModal , function()
    {
      var objText    = $(this).parent().find(clInputSelecionaModal);
      var titulo     = objText.data('title');
      var controller = objText.data('controller');
      var action     = objText.data('action');
      
      var html       = '';
      html           = html + '<h3>'+titulo+'</h3>';
      html           = html + '<div class="row">';
      html           = html + '  <div class="col-md-12">';
      html           = html + '    <div class="form-group bmd-form-group has-info">';
      html           = html + '      <label class="label-control bmd-label-static text-default">Digite algo para pesquisar:</label>';
      html           = html + '      <input maxlength="100" name="'+clFilterSelecionaModal+'" type="text" class="form-control '+clFilterSelecionaModal+'" data-controller="'+controller+'" data-action="'+action+'" value="" />';
      html           = html + '    </div>';
      html           = html + '  </div>';
      html           = html + '</div>';
      html           = html + '<div class="row">';
      html           = html + '  <div class="col-md-12" id="'+clDvRetSelecionaModal+'">';
      html           = html + '    ';
      html           = html + '  </div>';
      html           = html + '</div>';
      
      Swal.fire({
        html: html,
        showCloseButton: true,
        focusConfirm: false,
        confirmButtonColor: '#00bcd4',
        confirmButtonText: 'Selecionar',
        width: '80%'
      })
      .then((result) => {
        if (result.value) {
          var checkSelected = $('input[name='+clRadioSelecionaModal+']:checked', 'table#'+clListaSelecionaModal);
          if(checkSelected.length > 0){
            var value         = checkSelected.val();
            var text          = checkSelected.closest('tr').find('td:nth-child(2)').html(); //sempre segunda coluna

            objText.val(text);
            objText.parent().find('.'+clHddnSelecionaModal).val(value);
          }
        }
      });
    });
    
    $(document).on('click','.'+clClearSelecionaModal , function()
    {
      var objText = $(this).parent().find(clInputSelecionaModal);
      objText.val('');
      
      objText.parent().find('.'+clHddnSelecionaModal).val('');
    });
    
    var timeout = null;
    $(document).on('keyup', '.'+clFilterSelecionaModal, function()
    {
      var controller = $(this).data('controller');
      var action     = $(this).data('action');
      var text       = $(this).val();
      
      clearTimeout(timeout);
      $('#'+clDvRetSelecionaModal).html('Carregando ...');

      // Make a new timeout set to go off in 800ms
      // esquema com typing delay
      timeout = setTimeout(function () {
        mvc_post_ajax_var(controller, action, 'text=' + text);
      }, 500);
    });
    // ==================
  } catch (err) { }
}

$(document).on('click','.password_show_text', function()
{
  var objPass = $(this).prev();

  if (objPass.attr("type") === "password") {
    objPass.attr("type", "text");
    $(this).find('a').html('Esconder senha');
  } else {
    objPass.attr("type", "password");
    $(this).find('a').html('Exibir senha');
  }
});

function process_mvc_ret(data)
{
  if(typeof data.callback !== 'undefined'){
    if(data.callback !== ""){
      setTimeout(data.callback, 350);
    }
  }

  if(typeof data.msg !== 'undefined' && typeof data.msg_titulo !== 'undefined' && typeof data.msg_tipo !== 'undefined'){
    if(data.msg !== "" && data.msg_titulo !== "" && data.msg_tipo !== ""){
      mostraNotificacao(data.msg_titulo, data.msg, data.msg_tipo);
    }
  }

  if(typeof data.html_selector !== 'undefined'){
    var append = false;
    if(data.html_append !== 'undefined'){
      append = data.html_append;
    }

    if(data.html_selector !== ""){
      var vHtml = "";
      if(typeof data.html !== "undefined"){
        vHtml = data.html;
      }
      
      if(append){
        $(data.html_selector).append(vHtml);
      } else {
        $(data.html_selector).html(vHtml);
      }
    }
  }
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
      process_mvc_ret(data);
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
    },
    onOpen: function(){
      setTimeout("init_components()", 300);
    }
  });

  if (password) {
    mvc_post_ajax_var(controller, action, 'id=' + id + '&nova_senha=' + password);
  } else {
    mostraNotificacao('Aviso!', 'Informe a nova senha para prosseguir com a alteração!', 'danger');
  }
}
/* ======= */

/* grupo pessoa info */
function jsonAddGrupoPessoaInfo(pessoa, grupo)
{
  mvc_post_ajax_var("Json", "jsonPegaViewAddGpi", "pessoa=" + pessoa + '&grupo=' + grupo);
}

function jsonShowAddGrupoPessoaInfo(html)
{
  Swal.fire({
    html: html,
    showCloseButton: true,
    focusConfirm: false,
    confirmButtonColor: '#00bcd4',
    confirmButtonText: 'Salvar',
    width: '70%'
  })
  .then((result) => {
    if (result.value) {
      var formVars = $("form#frmGrupoPessoaInfoNovo").serialize();
      jsonSaveGrupoPessoaInfo(formVars);
    }
  });
  
  setTimeout("init_components()", 300);
}

function jsonSaveGrupoPessoaInfo(strVars)
{
  mvc_post_ajax_var("Json", "jsonPostAddGpi", strVars);
}

function jsonEditaGrupoPessoaInfo(id)
{
  mvc_post_ajax_var("GrupoPessoaInfo", "jsonPegaViewEditaGpi", "id=" + id);
}

function jsonShowEditarGrupoPessoaInfo(html)
{
  Swal.fire({
    html: html,
    showCloseButton: true,
    focusConfirm: false,
    confirmButtonColor: '#00bcd4',
    confirmButtonText: 'Salvar',
    width: '70%'
  })
  .then((result) => {
    if (result.value) {
      var formVars = $("form#frmGrupoPessoaInfoEditar").serialize();
      jsonSaveEditarGrupoPessoaInfo(formVars);
    }
  });
  
  setTimeout("init_components()", 300);
}

function jsonSaveEditarGrupoPessoaInfo(strVars)
{
  mvc_post_ajax_var("GrupoPessoaInfo", "jsonPostEditarGpi", strVars);
}
/* ================= */

/* Grupo */
var timeout = null;
$('#selec-participantes').on('keyup', '#filtra_participante', function()
{
  var text = $(this).val().toLowerCase();

  clearTimeout(timeout);

  // Make a new timeout set to go off in 800ms
  // esquema com typing delay
  timeout = setTimeout(function () {
    $("#selec-participantes .dv-participante").show();
    
    $("#selec-participantes .dv-participante").each(function() {
      var achouTexto = $(this).text().toLowerCase().indexOf(text) >= 0;
      if(!achouTexto){
        $(this).hide();
      }
    });
  }, 500);
});

$('#selec-participantes').on('click', '.checkbox-participante', function()
{
  var count = $('input.checkbox-participante:checked').length;
  $('#selec-participantes #spn-count-participantes').html(count);
});
/* ===== */

/* Sis Grupo */
$('form#frmNovaPostagem').on('change','.anexo' , function()
{
  var filename = $(this).val().replace(/C:\\fakepath\\/i, '');
  if(filename.length > 28){
    filename = filename.substr(0, 24) + '..';
  }
  
  $(this).parent().find('span.lbl_anexo').html(filename);
});

function deletaAnexo(element)
{
  element.closest(".row").remove();
}

function fncAnexarArqPostagem()
{
  var idAnexo = $('#lista-anexos .anexo').length + 1;
  mvc_post_ajax_var('Json', 'jsonPegaHtmlAnexo', 'linhaHtml=#lista-anexos&idForm=#frmNovaPostagem&idAnexo=' + idAnexo);
}

async function fncAnexarArqPostagemYt()
{
  var idAnexo = $('#lista-anexos .anexo').length + 1;
  const {value: linkYt} = await Swal.fire({
    title: 'Digite o link do Youtube',
    input: 'text',
    inputPlaceholder: 'Link Youtube',
    inputAttributes: {
      maxlength: 60,
      autocapitalize: 'off',
      autocorrect: 'off'
    }
  });

  if (linkYt) {
    mvc_post_ajax_var('Json', 'jsonPegaHtmlAnexo', 'linhaHtml=#lista-anexos&idForm=#frmNovaPostagem&idAnexo=' + idAnexo + '&linkYt=' + linkYt);
  }
}

function deletarPostagem(id)
{
  Swal.fire({
    title: 'Confirmação!',
    text: "Deseja mesmo excluir essa postagem?",
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Sim',
    cancelButtonText: 'Não ...'
  }).then((result) => {
    if (result.value) {
      mvc_post_ajax_var("Json", "jsonDeletaPostagem", "id=" + id);
    }
  });
}

function removeDvPostagem(id)
{
  $('div#item-postagem-' + id).remove();
}

function favoritarPostagem(id)
{
  mvc_post_ajax_var("Json", "jsonFavoritar", "id=" + id);
}

function jqueryMostraFavoritado(id)
{
  $('div#item-postagem-' + id + ' .mais_info_post .mip_favorito').html('<i class="material-icons text-success">favorite</i>');
}

$('.item-postagem .dv-area-comentario').on('keypress','textarea' , function(e)
{
  if(e.which == 13 && !e.shiftKey) {
    e.preventDefault();
    
    var grtId = $(this).data('id');
    var text  = $(this).val();
    
    mvc_post_ajax_var("Json", "jsonAddComentario", "grtId=" + grtId + "&texto=" + text);
  }
});

function fncItemPostagemDelComentario(grtId)
{
  Swal.fire({
    title: 'Confirmação!',
    text: "Deseja mesmo excluir esse comentário?",
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Sim',
    cancelButtonText: 'Não ...'
  }).then((result) => {
    if (result.value) {
      mvc_post_ajax_var("Json", "jsonDeletaComentario", "id=" + grtId);
    }
  });
}

$('div.content').on('click', '#carregar_mais_postagens a', function(e)
{
  var json_string = $(this).parent().find('#hddn_carregar_mais_postagens').val();
  var formData    = new FormData();
  formData.append('json', json_string);
  formData.append('dv_ret', '#carregar_mais_postagens');

  $.ajax({type: 'POST',
    data: formData,
    contentType: false,
    processData: false,
    url: V_GLOB_URL_BASE + 'Json' + '/' + 'jsonCarregarMaisPostagens',
    dataType: 'json',
    beforeSend: function () {
      $('#carregar_mais_postagens').html('Carregando ...');
    },
    success: function (data) {
      process_mvc_ret(data);
    }
  });
});

function jsonPostCarregarMaisPostagens(html, div)
{
  $(div).replaceWith(html);
}

function jsonEscolheAvaliacaoPost(id)
{
  mvc_post_ajax_var("Json", "jsonAvaliarPost", "id=" + id);
}

function jsonEscolheAvaliacaoPostModal(id, avaliacaoAtual){
  var temAvaliacao = (avaliacaoAtual == 0 || avaliacaoAtual == 1);
  var positivo     = "";
  var negativo     = "";
  if(avaliacaoAtual == 0){
    negativo = " selected ";
  } else if(avaliacaoAtual == 1){
    positivo = " selected ";
  }
  
  var html = '';
  html     = html + '<select id="cbJsonEscolheAvaliacaoPostModal" class="form-control" size="">';
  if(temAvaliacao){
    html   = html + '  <option value="">Remover avaliação atual</option>';
  }
  html     = html + '  <option '+positivo+' value="1">Avaliação Positiva</option>';
  html     = html + '  <option '+negativo+' value="0">Avaliação Negativa</option>';
  html     = html + '</select>';
  
  Swal.fire({
    title: 'Avaliar postagem',
    html: html,
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Gravar Avaliação',
    cancelButtonText: 'Voltar ...'
  }).then((result) => {
    if (result.value) {
      var avaliacao = $('#cbJsonEscolheAvaliacaoPostModal').val();
      mvc_post_ajax_var("Json", "jsonAvaliarPostSalvar", "id=" + id + "&avaliacao=" + avaliacao);
    }
  })
}
/* ========= */

/* foto perfil */
function fncAlterarFotoPerfil()
{
  mvc_post_ajax_var("Json", "jsonHtmlFotoPerfil", "");
}

function fncShowAlterarFotoPerfil(html)
{
  $(document).ready(function(){
    $('#frmAlterarFotoPerfil input[type="file"]').change(function(e){
      var fileName = e.target.files[0].name;
      $(this).parent().find('#spn_nome_foto').html('Foto selecionada. Faça o upload.');
      //alert('The file "' + fileName +  '" has been selected.');
    });
  });
  
  Swal.fire({
    html: html,
    showCloseButton: true,
    focusConfirm: false,
    confirmButtonColor: '#00bcd4',
    confirmButtonText: 'Subir foto',
    width: '200px'
  })
  .then((result) => {
    if (result.value) {
      var file     = document.getElementById("file-alterar-foto-perfil").files[0];
      var formData = new FormData();
      formData.append('file', file);
      
      $.ajax({type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        url: V_GLOB_URL_BASE + 'Json' + '/' + 'jsonPostHtmlFotoPerfil',
        dataType: 'json',
        success: function (data) {
          process_mvc_ret(data);
        }
      });
    }
  });
  
  setTimeout("init_components()", 300);
}

function fncShowAlterarFotoPerfilCrop(html)
{
  setTimeout(function(){
    V_CROPPIE = $('#img-foto-perfil-crop').croppie({
      viewport: {
        width: 300,
        height: 300
      },
      mouseWheelZoom: false,
    });
  }, 400);
  
  Swal.fire({
    html: html,
    showCloseButton: true,
    focusConfirm: false,
    confirmButtonColor: '#00bcd4',
    confirmButtonText: 'Salvar',
    width: '99%'
  })
  .then((result) => {
    if (result.value) {
      V_CROPPIE.croppie('result', 'base64').then(function(base64) {
        var formData = new FormData();
        formData.append('base64', base64);
        formData.append('imgUrl', $("#img-foto-perfil-crop").attr("src"));

        $.ajax({type: 'POST',
          data: formData,
          contentType: false,
          processData: false,
          url: V_GLOB_URL_BASE + 'Json' + '/' + 'jsonPostHtmlFotoPerfilCrop',
          dataType: 'json',
          success: function (data) {
            process_mvc_ret(data);
          }
        });
      });
    }
  });
}
/* =========== */
