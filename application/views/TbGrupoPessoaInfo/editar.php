<?php
$GrupoPessoaInfo = $GrupoPessoaInfo ?? array();
$vGpiId          = $GrupoPessoaInfo["gpi_id"] ?? 0;
$vGpiInicial     = $GrupoPessoaInfo["gpi_inicial"] ?? 0;
$vGpiData        = $GrupoPessoaInfo["gpi_data"] ?? date("Y-m-d");
$vGpiAltura      = $GrupoPessoaInfo["gpi_altura"] ?? "";
$vGpiPeso        = $GrupoPessoaInfo["gpi_peso"] ?? "";
$vGpiPesoObj     = $GrupoPessoaInfo["gpi_peso_objetivo"] ?? "";

$strData         = date("d/m/Y", strtotime($vGpiData));
$strPeso         = ($vGpiPeso != "") ? number_format($vGpiPeso, 3, ",", "."): "";
$strPesoObj      = ($vGpiPesoObj != "") ? number_format($vGpiPesoObj, 3, ",", "."): "";
$ehPrimeira      = ($vGpiInicial == 1);
$strPrimeira     = ($ehPrimeira) ? "Lançar Medidas Iniciais": "Lançar Medida Atual";
$nrCol           = ($ehPrimeira) ? "3": "6";
?>

<form id="frmGrupoPessoaInfoEditar">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-success">
          <h4 class="card-title"><?php echo $strPrimeira; ?></h4>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-<?php echo $nrCol; ?>">
              <div class="form-group bmd-form-group has-success">
                <label class="label-control bmd-label-static">Data</label>
                <input name="data" type="text" class="form-control datepicker" value="<?php echo $strData; ?>" />
              </div>
            </div>
            <?php
            if($ehPrimeira){
              ?>
              <div class="col-md-3">
                <div class="form-group bmd-form-group has-success">
                  <label class="label-control bmd-label-static">Altura (cm)</label>
                  <input maxlength="3" name="altura_cm" type="text" class="form-control txt_inteiro" value="<?php echo $vGpiAltura; ?>" />
                </div>
              </div>
              <?php
            }
            ?>
            <div class="col-md-<?php echo $nrCol; ?>">
              <div class="form-group bmd-form-group has-success">
                <label class="label-control bmd-label-static">Peso (kg)</label>
                <input data-decimais="3" name="peso_kg" type="text" class="form-control txt_moeda" value="<?php echo $strPeso; ?>" />
              </div>
            </div>
            <?php
            if($ehPrimeira){
              ?>
              <div class="col-md-3">
                <div class="form-group bmd-form-group has-success">
                  <label class="label-control bmd-label-static">Peso (Objetivo)</label>
                  <input data-decimais="3" name="peso_kg_obj" type="text" class="form-control txt_moeda" value="<?php echo $strPesoObj; ?>" />
                </div>
              </div>
              <?php
            }
            ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  <input type="hidden" name="id" value="<?php echo $vGpiId; ?>" />
</form>