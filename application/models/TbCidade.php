<?php
require_once(APPPATH."/helpers/utils_helper.php");

function pegaListaSelecionaCidade($texto)
{
  $CI = pega_instancia();
  $CI->load->database();

  $radio_modal = SELECIONA_MODAL_RADIO;

  require_once(FCPATH."/assets/Lista_CI/Lista_CI.php");
  $Lista_CI = new Lista_CII($CI->db);
  $Lista_CI->setId(SELECIONA_MODAL_LISTA);
  $Lista_CI->addField("REPLACE('<input style=\"position:relative; top:-1px;\" class=\"form-check-input\" type=\"radio\" name=\"$radio_modal\" id=\"$radio_modal\" value=\"{cid_id}\">', '{cid_id}', cid_id) AS \"&nbsp;\"");
  $Lista_CI->addField("CONCAT(cid_descricao, ' - ', est_descricao) AS \"Cidade\"", "L");
  $Lista_CI->addFrom("v_tb_cidade");
  $Lista_CI->addWhere("cid_descricao LIKE '$texto%'");
  $Lista_CI->changeOrderCol(2);
  $Lista_CI->setLimit(30);

  return $Lista_CI->getHtml();
}