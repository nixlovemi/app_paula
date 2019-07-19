function filter_lista_ci(url_request_base, lista_ci_id, filter, filter_val, changePage, orderBy) {
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