<?php
# essa tela tem visualizar/editar!!
# @todo ver como fazer se a pessoa alterar MANUALMENTE o parametro de visualizar/editar
# !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

$GrupoPessoa        = $GrupoPessoa ?? array();
$Grupo              = $Grupo ?? array();
$GrupoPessoaInfoGrp = $GrupoPessoaInfoGrp ?? array();
$htmlPeso           = $htmlPeso ?? "";
$lancar             = $lancar ?? true;
$editar             = $editar ?? false;
$exibeVoltar        = $exibeVoltar ?? true;

$pesId       = $GrupoPessoa["grp_pes_id"] ?? "";
$pesNome     = $GrupoPessoa["pes_nome"] ?? "";
$pesFoto     = ($GrupoPessoa["pes_foto"] != "") ? $GrupoPessoa["pes_foto"]: FOTO_DEFAULT;
$pesEmail    = $GrupoPessoa["pes_email"] ?? "";
$petDesc     = $GrupoPessoa["pet_descricao"] ?? "";
$gruId       = $GrupoPessoa["grp_gru_id"] ?? "";
$gruDesc     = $GrupoPessoa["gru_descricao"] ?? "";
$gruDtIni    = $Grupo["gru_dt_inicio"] ?? "";
$gruDtFim    = $Grupo["gru_dt_termino"] ?? "";
$infoInicial = $GrupoPessoaInfoGrp["primeira"] ?? array();
$infoDemais  = $GrupoPessoaInfoGrp["demais"] ?? array();
$infoAltura  = $GrupoPessoaInfoGrp["altura"] ?? NULL;
$infoPeso    = $GrupoPessoaInfoGrp["peso"] ?? NULL;
$arrIMC      = calcula_imc($infoAltura, $infoPeso);
$vImc        = $arrIMC["imc"] ?? NULL;
$vImcResult  = $arrIMC["resultado"] ?? "";
$vAguaDia    = calcula_agua_dia($infoPeso);

$vGpiIdIni   = $infoInicial["gpi_id"] ?? "";
$strDtIni    = ($gruDtIni != "") ? date("d/m/Y", strtotime($gruDtIni)): "";
$strDtFim    = ($gruDtFim != "") ? date("d/m/Y", strtotime($gruDtFim)): "";
$strData     = (isset($infoInicial["gpi_data"]) && $infoInicial["gpi_data"] != "") ? date("d/m/Y", strtotime($infoInicial["gpi_data"])): "";
$strAltura   = (isset($infoInicial["gpi_altura"]) && $infoInicial["gpi_altura"] != "") ? $infoInicial["gpi_altura"] . "cm": "";
$strPeso     = (isset($infoInicial["gpi_peso"]) && $infoInicial["gpi_peso"] != "") ? number_format($infoInicial["gpi_peso"], 3, ",", ".") . "kg": "";
$strPesoObj  = (isset($infoInicial["gpi_peso_objetivo"]) && $infoInicial["gpi_peso_objetivo"] != "") ? number_format($infoInicial["gpi_peso_objetivo"], 3, ",", ".") . "kg": "";
$strDif      = ($strPesoObj != "" && $strPeso != "") ? number_format($infoInicial["gpi_peso"] - $infoInicial["gpi_peso_objetivo"], 3, ",", ".") . "kg": "";

// info do grafico #chartProgressaoMedidas
// @todo refatorar essa parte!!!!!!!!!!!!!
$arrLabel     = [];
$arrSerie     = [];
$arrLoopChart = [];

$vLabel      = $infoInicial["gpi_data"] ?? "";
$vSerie      = $infoInicial["gpi_peso"] ?? "";
if($vLabel != "" && $vSerie != ""){
  $arrLoopChart[] = array(
    "gpi_data" => $vLabel,
    "gpi_peso" => $vSerie,
  );
}

$arrLoop = array_merge($arrLoopChart, $infoDemais);
$i = 1;
foreach($arrLoop as $info){
  $vLabel = ($info["gpi_data"] != "") ? date("d/m", strtotime($info["gpi_data"])): "";
  $vSerie = $info["gpi_peso"] ?? "";

  if($vLabel != "" && $vSerie != ""){
    #$arrLabel[] = "'$vLabel'";
    $arrLabel[] = "'$i'";
    $arrSerie[] = $vSerie;

    $lastPeso = $vSerie;
    $i++;
  }
}

$difAtual = ($strPesoObj != "") ? number_format(($infoInicial["gpi_peso_objetivo"] / $lastPeso) * 100, 3, ",", "."): "?";
// =======================================
?>

<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header card-header-info">
        <h4 class="card-title">Medidas iniciais</h4>
      </div>
      <div class="card-body">
        <div class="row">
          <?php
          if(count($infoInicial) <= 0){
            ?>
            <div class="col-md-12">
              <p style="margin-bottom: 0;">As medidas iniciais não foram lançadas.</p>
              <?php
              if($editar || $lancar){
                ?>
                <a href="javascript:;" class="btn btn-info btn-sm" onclick="jsonAddGrupoPessoaInfo(<?php echo $pesId; ?>, <?php echo $gruId; ?>);">
                  Lançar agora
                  <div class="ripple-container"></div>
                </a>
                <?php
              }
              ?>
            </div>
            <?php
          } else {
            ?>
            <div class="col-md-2">
              <div class="form-group bmd-form-group has-info">
                <label class="bmd-label-floating">Data</label>
                <input type="text" class="form-control" readonly="" value="<?php echo $strData; ?>" />
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group bmd-form-group has-info">
                <label class="bmd-label-floating">Altura</label>
                <input type="text" class="form-control" readonly="" value="<?php echo $strAltura; ?>" />
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group bmd-form-group has-info">
                <label class="bmd-label-floating">Peso</label>
                <input type="text" class="form-control" readonly="" value="<?php echo $strPeso; ?>" />
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group bmd-form-group has-info">
                <label class="bmd-label-floating">Peso (Objetivo)</label>
                <input type="text" class="form-control" readonly="" value="<?php echo $strPesoObj; ?>" />
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group bmd-form-group has-info">
                <label class="bmd-label-floating">Diferença</label>
                <input type="text" class="form-control" readonly="" value="<?php echo $strDif; ?>" />
              </div>
            </div>
            <div class="col-md-2 text-center">
              <?php
              if($editar){
                ?>
                <button style="position:relative; top:6px;" type="button" class="btn btn-info btn-sm" onclick="jsonEditaGrupoPessoaInfo(<?php echo $vGpiIdIni; ?>)">
                  <i style="color:#FFF !important;" class="material-icons text-info">create</i>
                  <div class="ripple-container"></div>
                </button>
                <?php
              }
              ?>
            </div>
            <?php
          }
          ?>
        </div>
      </div>
    </div>

    <?php
    if(count($infoInicial) > 0){
      ?>
      <div class="card">
        <div class="card-header card-header-info">
          <h4 class="card-title">Acompanhamento</h4>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-12">
              <?php
              if($editar || $lancar){
                ?>
                <a href="javascript:;" class="btn btn-info btn-sm" onclick="jsonAddGrupoPessoaInfo(<?php echo $pesId; ?>, <?php echo $gruId; ?>);">
                  Lançar medida
                  <div class="ripple-container"></div>
                </a>
                <?php
              }
              ?>
              <?php echo $htmlPeso; ?>
            </div>
          </div>
        </div>
      </div>
      <?php
    }
    ?>
  </div>
  <div class="col-md-4">
    <div class="card card-profile">
      <div class="card-avatar">
        <img class="img" src="<?php echo base_url(); ?><?php echo $pesFoto; ?>" />
      </div>
      <div class="card-body">
        <h4 class="card-title"><?php echo $pesNome; ?></h4>
        <h6 class="card-category text-gray"><?php echo $pesEmail; ?></h6>
        <p class="card-description">
          <?php echo "$petDesc, participante do grupo $gruDesc no período de $strDtIni a $strDtFim."; ?>
        </p>
      </div>
    </div>

    <?php
    if(count($infoInicial) > 0){
      ?>
      <div class="card card-chart">
        <div class="card-header card-header-info">
          <div class="ct-chart" id="chartProgressaoMedidas"></div>
          <script>
            $( document ).ready(function(){
              new Chartist.Line('#chartProgressaoMedidas', {
                labels: [<?php echo implode(",", $arrLabel); ?>],
                series: [
                  [<?php echo implode(",", $arrSerie); ?>]
                ]
              }, {
                fullWidth: true,
                chartPadding: {
                  right: 40
                }
              });
            });
          </script>
        </div>
        <div class="card-body">
          <h4 class="card-title">Progressão das Medidas</h4>
          <p class="card-category">
            <span class="text-info">
              Você está com <?php echo $difAtual; ?>% do objetivo concluído.
              <hr style="margin:5px 0; padding:0;" />
              <?php
              if($vImc > 0){
                echo "<b>IMC:</b> " . number_format($vImc, 2, ",", "") . " - <small>" . $vImcResult . "</small>";
              }
              if($vAguaDia > 0){
                echo "<br /><b>Água/dia:</b> " . number_format($vAguaDia, 2, ",", "") . "L";
              }
              ?>
            </span>
          </p>
        </div>
      </div>
      <?php
    }
    ?>

    <?php
    if($exibeVoltar){
      ?>
      <a href="<?php echo base_url() ?>Grupo/editar/<?php echo $gruId;?>" class="btn btn-default pull-right">
        Voltar
        <div class="ripple-container"></div>
      </a>
      <?php
    }
    ?>
  </div>
</div>