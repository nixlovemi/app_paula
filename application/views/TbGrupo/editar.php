<?php
$Grupo      = $Grupo ?? array();
$htmlGP     = $htmlGP ?? "";

$vDescricao = $Grupo["gru_descricao"] ?? "";
$vInicio    = $Grupo["gru_dt_inicio"] ?? "";
$vTermino   = $Grupo["gru_dt_termino"] ?? "";
$vAtivo     = $Grupo["gru_ativo"] ?? "";

$strDtIni   = ($vInicio != "") ? date("d/m/Y", strtotime($vInicio)): "";
$strDtFim   = ($vTermino != "") ? date("d/m/Y", strtotime($vTermino)): "";
$strAtivo   = ($vAtivo == 1) ? "Sim": "Não";
?>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-header-success">
        <h4 class="card-title">Grupo</h4>
        <p class="card-category">Controle dos grupos gerenciados por você. Crie ou visualize informações dos grupos aqui.</p>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group bmd-form-group has-success">
              <label class="bmd-label-floating">Descrição</label>
              <input readonly="" maxlength="80" name="descricao" type="text" class="form-control" value="<?php echo $vDescricao; ?>" />
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group bmd-form-group has-success">
              <label class="bmd-label-floating">Ativo</label>
              <input readonly="" maxlength="80" name="ativo" type="text" class="form-control" value="<?php echo $strAtivo; ?>" />
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group bmd-form-group has-success">
              <label class="bmd-label-floating">Início</label>
              <input readonly="" name="inicio" type="text" class="form-control" value="<?php echo $strDtIni; ?>" />
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group bmd-form-group has-success">
              <label class="bmd-label-floating">Término</label>
              <input readonly="" name="termino" type="text" class="form-control" value="<?php echo $strDtFim; ?>" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-header-success">
        <h4 class="card-title">Participantes</h4>
      </div>
      <div class="card-body">
        <?php echo $htmlGP; ?>
      </div>
    </div>
  </div>
</div>

<a href="<?php echo base_url() ?>Grupo" class="btn btn-info pull-right">
  Voltar
  <div class="ripple-container"></div>
</a>
<div class="clearfix"></div>