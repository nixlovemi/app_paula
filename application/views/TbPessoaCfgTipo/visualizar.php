<?php
$PessoaCfgTipo = $PessoaCfgTipo ?? array();
$pctId         = $PessoaCfgTipo["pct_id"] ?? "";
$pctDescricao  = $PessoaCfgTipo["pct_descricao"] ?? "";
$pctAtivo      = $PessoaCfgTipo["pct_ativo"] ?? "";

$strAtivo       = "";
if($pctAtivo != ""){
  $strAtivo = ($pctAtivo == 0) ? "Não": "Sim";
}
?>
<form>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-info">
          <h4 class="card-title">Visualizar Tipo de configuração</h4>
          <p class="card-category">Cada nova configuração deve ser programada no servidor. </p>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-2">
              <div class="form-group bmd-form-group has-info">
                <label class="bmd-label-floating">ID</label>
                <input readonly="" maxlength="80" name="id" type="text" class="form-control" value="<?php echo $pctId; ?>" />
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group bmd-form-group has-info">
                <label class="bmd-label-floating">Descrição</label>
                <input readonly="" maxlength="80" name="descricao" type="text" class="form-control" value="<?php echo $pctDescricao; ?>" />
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group bmd-form-group has-info">
                <label class="bmd-label-floating">Ativo</label>
                <input readonly="" maxlength="80" name="ativo" type="text" class="form-control" value="<?php echo $strAtivo; ?>" />
              </div>

              <!--
              <div class="form-group bmd-form-group has-info">
                <select name="ativo" class="form-control" size="">
                  <option value="0">Ativo: Não</option>
                  <option value="1">Ativo: Sim</option>
                </select>
                <span class="bmd-help">Ativo</span>
              </div>
              -->
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <a href="<?php echo base_url() ?>PessoaCfgTipo" class="btn btn-default pull-right">
    &#60; Voltar
    <div class="ripple-container"></div>
  </a>
  <div class="clearfix"></div>
</form>