<?php
require_once(APPPATH."/helpers/utils_helper.php");
$CI = pega_instancia();
$CI->load->database();

require_once(FCPATH."/assets/Lista_CI/Lista_CI.php");
$Lista_CI = new Lista_CII($CI->db);
$Lista_CI->addField("usa_id AS id");
$Lista_CI->addField("usa_usuario AS usuario", "L");
$Lista_CI->addField("usa_senha AS senha", "L");
$Lista_CI->addField("usa_ativo AS ativo");
$Lista_CI->addField("1 AS admin");
$Lista_CI->addFrom("tb_usuario_admin");
$Lista_CI->setLimit(1);

echo $Lista_CI->getHtmlTable();