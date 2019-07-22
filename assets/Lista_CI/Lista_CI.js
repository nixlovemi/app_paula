function filter_lista_ci(url_request_base, lista_ci_id, filter, filter_val, changePage, orderBy)
{
  var json_lista_ci = $('#hddn_' + lista_ci_id).val();
  
  $.ajax({
    url: url_request_base + 'Lista_CI',
    type: 'POST',
    data: {json_lista_ci: json_lista_ci, lista_ci_id: lista_ci_id, filter: filter, filter_val: filter_val, changePage: changePage, orderBy: orderBy},
    error: function () {
      $("#spn_" + lista_ci_id).html("Erro ao recarregar listagem!");
    },
    beforeSend: function () {
      $("#spn_" + lista_ci_id).html("<img style='width:96px; height:96px;' src='"+url_request_base+"assets/Lista_CI/ajax-loader.gif' />");
    },
    success: function (data) {
      $("#spn_" + lista_ci_id).html(data);
    }
  });
}

function input_filter_click(e, lista_ci_id)
{
  if (e.which == 13 || e.keyCode == 13) {
    var Lista_CI_ROW = $('#dv-row-filter-lista-ci-' + lista_ci_id);
    var filter    = Lista_CI_ROW.find('#filter_field_lista_ci').val();
    var filterTxt = Lista_CI_ROW.find('#filter_text_lista_ci').val();
    var urlBase   = Lista_CI_ROW.data('url-request-base');
    var listaId   = Lista_CI_ROW.data('lista-ci-id');
    
    filter_lista_ci(urlBase, listaId, filter, filterTxt, 0, '');
  }
}

$( document ).ready(function() {
  $( ".inptHddnRecListaCi" ).each(function( index ) {
    var jsonParamLst = $(this).val();
    var varIdListaCi = $(this).attr("id").replace("inptHddnRecListaCi__", "");
    var dcRowLista   = $('div#dv-row-filter-lista-ci-' + varIdListaCi);
    
    if(dcRowLista.length > 0){
      var url_request_base = dcRowLista.data("url-request-base");
      $.ajax({
        url: url_request_base + 'Lista_CI/reload',
        type: 'POST',
        data: {jsonParamLst: jsonParamLst},
        error: function () {
          $("#spn_" + varIdListaCi).html("Erro ao recarregar listagem!");
        },
        beforeSend: function () {
          $("#spn_" + varIdListaCi).html("<img style='width:96px; height:96px;' src='"+url_request_base+"assets/Lista_CI/ajax-loader.gif' />");
        },
        success: function (data) {
          $("#spn_" + varIdListaCi).html(data);
        }
      });
    }
  });
});