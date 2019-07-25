<?php
$Pessoa    = $Pessoa ?? array();

$vId       = $Pessoa["pes_id"] ?? "";
$vTipo     = $Pessoa["pet_descricao"] ?? "";
$vNome     = $Pessoa["pes_nome"] ?? "";
$vEmail    = $Pessoa["pes_email"] ?? "";
$vAtivo    = $Pessoa["pes_ativo"] ?? "";

$vStrAtivo = ($vAtivo == 1) ? "Sim": "Não";
?>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-header-success">
        <h4 class="card-title">Visualizar Pessoa</h4>
        <p class="card-category">Controle das pessoas que farão parte dos seus grupos. Isso inclui pessoas do staff e clientes.</p>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-2">
            <div class="form-group bmd-form-group has-success">
              <label class="bmd-label-floating">ID</label>
              <input readonly="" maxlength="100" name="id" type="text" class="form-control" value="<?php echo $vId; ?>" />
            </div>
          </div>
          <div class="col-md-5">
            <div class="form-group bmd-form-group has-success">
              <label class="bmd-label-floating">Tipo</label>
              <input readonly="" maxlength="100" name="tipo" type="text" class="form-control" value="<?php echo $vTipo; ?>" />
            </div>
          </div>
          <div class="col-md-5">
            <div class="form-group bmd-form-group has-success">
              <label class="bmd-label-floating">Nome</label>
              <input readonly="" maxlength="100" name="nome" type="text" class="form-control" value="<?php echo $vNome; ?>" />
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-7">
            <div class="form-group bmd-form-group has-success">
              <label class="bmd-label-floating">Email</label>
              <input readonly="" maxlength="150" name="email" type="text" class="form-control" value="<?php echo $vEmail; ?>" />
            </div>
          </div>
          <div class="col-md-5">
            <div class="form-group bmd-form-group has-success">
              <label class="bmd-label-floating">Ativo</label>
              <input readonly="" maxlength="150" name="ativo" type="text" class="form-control" value="<?php echo $vStrAtivo; ?>" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<a href="<?php echo base_url() ?>Pessoa" class="btn btn-info pull-right">
  Voltar
  <div class="ripple-container"></div>
</a>
<div class="clearfix"></div>
