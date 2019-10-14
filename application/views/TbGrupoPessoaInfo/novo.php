<?php
$pesId       = $pesId ?? "";
$gruId       = $gruId ?? "";
$ehPrimeira  = $ehPrimeira ?? true;

$strPrimeira = ($ehPrimeira) ? "Lançar Medidas Iniciais": "Lançar Medida Atual";
$nrCol       = ($ehPrimeira) ? "3": "6";
?>

<form id="frmGrupoPessoaInfoNovo">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-info">
          <h4 class="card-title"><?php echo $strPrimeira; ?></h4>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-<?php echo $nrCol; ?>">
              <div class="form-group bmd-form-group has-info">
                <label class="label-control bmd-label-static">Data</label>
                <input name="data" type="text" class="form-control datepicker" value="<?php echo date("d/m/Y"); ?>" />
              </div>
            </div>
            <?php
            if($ehPrimeira){
              ?>
              <div class="col-md-3">
                <div class="form-group bmd-form-group has-info">
                  <label class="label-control bmd-label-static">Altura (cm)</label>
                  <input maxlength="3" name="altura_cm" type="text" class="form-control txt_inteiro" />
                </div>
              </div>
              <?php
            }
            ?>
            <div class="col-md-<?php echo $nrCol; ?>">
              <div class="form-group bmd-form-group has-info">
                <label class="label-control bmd-label-static">Peso (kg)</label>
                <input data-decimais="3" name="peso_kg" type="text" class="form-control txt_moeda" />
              </div>
            </div>
            <?php
            if($ehPrimeira){
              ?>
              <div class="col-md-3">
                <div class="form-group bmd-form-group has-info">
                  <label class="label-control bmd-label-static">Peso (Objetivo)</label>
                  <input data-decimais="3" name="peso_kg_obj" type="text" class="form-control txt_moeda" />
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
  <input type="hidden" name="pessoa" value="<?php echo $pesId; ?>" />
  <input type="hidden" name="grupo" value="<?php echo $gruId; ?>" />
  <input type="hidden" name="primeira" value="<?php echo $ehPrimeira; ?>" />
</form>