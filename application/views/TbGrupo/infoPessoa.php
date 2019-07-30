<?php
$GrupoPessoa        = $GrupoPessoa ?? array();
$Grupo              = $Grupo ?? array();
$GrupoPessoaInfoGrp = $GrupoPessoaInfoGrp ?? array();

$pesId       = $GrupoPessoa["grp_pes_id"] ?? "";
$pesNome     = $GrupoPessoa["pes_nome"] ?? "";
$pesEmail    = $GrupoPessoa["pes_email"] ?? "";
$petDesc     = $GrupoPessoa["pet_descricao"] ?? "";
$gruId       = $GrupoPessoa["grp_gru_id"] ?? "";
$gruDesc     = $GrupoPessoa["gru_descricao"] ?? "";
$gruDtIni    = $Grupo["gru_dt_inicio"] ?? "";
$gruDtFim    = $Grupo["gru_dt_termino"] ?? "";
$infoInicial = $GrupoPessoaInfoGrp["primeira"] ?? array();

$strDtIni    = ($gruDtIni != "") ? date("d/m/Y", strtotime($gruDtIni)): "";
$strDtFim    = ($gruDtFim != "") ? date("d/m/Y", strtotime($gruDtFim)): "";
$strData     = ($infoInicial["gpi_data"] != "") ? date("d/m/Y", strtotime($infoInicial["gpi_data"])): "";
$strAltura   = ($infoInicial["gpi_altura"] != "") ? $infoInicial["gpi_altura"] . "cm": "";
$strPeso     = ($infoInicial["gpi_peso"] != "") ? number_format($infoInicial["gpi_peso"], 3, ",", ".") . "kg": "";
$strPesoObj  = ($infoInicial["gpi_peso_objetivo"] != "") ? number_format($infoInicial["gpi_peso_objetivo"], 3, ",", ".") . "kg": "";
$strDif      = ($infoInicial["gpi_peso_objetivo"] != "" && $infoInicial["gpi_peso"] != "") ? number_format($infoInicial["gpi_peso"] - $infoInicial["gpi_peso_objetivo"], 3, ",", ".") . "kg": "";
?>

<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header card-header-success">
        <h4 class="card-title">Medidas iniciais</h4>
      </div>
      <div class="card-body">
        <div class="row">
          <?php
          if(count($infoInicial) <= 0){
            ?>
            <div class="col-md-12">
              <p style="margin-bottom: 0;">As medidas iniciais não foram lançadas.</p>
              <a href="javascript:;" class="btn btn-info btn-sm" onclick="jsonAddGrupoPessoaInfo(<?php echo $pesId; ?>, <?php echo $gruId; ?>);">
                Lançar agora
                <div class="ripple-container"></div>
              </a>
            </div>
            <?php
          } else {
            ?>
            <div class="col-md-2">
              <div class="form-group bmd-form-group has-success">
                <label class="bmd-label-floating">Data</label>
                <input type="text" class="form-control" readonly="" value="<?php echo $strData; ?>" />
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group bmd-form-group has-success">
                <label class="bmd-label-floating">Altura</label>
                <input type="text" class="form-control" readonly="" value="<?php echo $strAltura; ?>" />
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group bmd-form-group has-success">
                <label class="bmd-label-floating">Peso</label>
                <input type="text" class="form-control" readonly="" value="<?php echo $strPeso; ?>" />
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group bmd-form-group has-success">
                <label class="bmd-label-floating">Peso (Objetivo)</label>
                <input type="text" class="form-control" readonly="" value="<?php echo $strPesoObj; ?>" />
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group bmd-form-group has-success">
                <label class="bmd-label-floating">Diferença</label>
                <input type="text" class="form-control" readonly="" value="<?php echo $strDif; ?>" />
              </div>
            </div>
            <?php
          }
          ?>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header card-header-success">
        <h4 class="card-title">Acompanhamento</h4>
      </div>
      <div class="card-body">
        <div class="row">
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card card-profile">
      <div class="card-avatar">
        <a href="#pablo">
          <img class="img" src="<?php echo base_url(); ?>template/assets/img/faces/avatar.png" />
        </a>
      </div>
      <div class="card-body">
        <h4 class="card-title"><?php echo $pesNome; ?></h4>
        <h6 class="card-category text-gray"><?php echo $pesEmail; ?></h6>
        <p class="card-description">
          <?php echo "$petDesc, participante do grupo $gruDesc no período de $strDtIni a $strDtFim."; ?>
        </p>
      </div>
    </div>

    <a href="<?php echo base_url() ?>Grupo/editar/<?php echo $gruId;?>" class="btn btn-info pull-right">
      Voltar
      <div class="ripple-container"></div>
    </a>
  </div>
</div>