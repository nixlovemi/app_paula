<?php
$Usuario     = $Usuario ?? array();

$vUsuId      = $Usuario["usu_id"] ?? "";
$vUsuEmail   = $Usuario["usu_email"] ?? "";
$vUsuNome    = $Usuario["usu_nome"] ?? "";
$vUsuAtivo   = $Usuario["usu_ativo"] ?? "";
$vUsaUsuario = $Usuario["usa_usuario"] ?? "";

$strAtivo = "";
if($vUsuAtivo != ""){
  $strAtivo = ($vUsuAtivo == 0) ? "Não": "Sim";
}
?>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-header-info">
        <h4 class="card-title">Visualizar Usuário</h4>
        <p class="card-category">Controle dos usuários (clientes) que terão acesso ao sistema. Lembre-se de configurar os parâmetros de cada cadastro.</p>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-2">
            <div class="form-group bmd-form-group has-info">
              <label class="bmd-label-floating">ID</label>
              <input readonly="" maxlength="80" name="id" type="text" class="form-control" value="<?php echo $vUsuId; ?>" />
            </div>
          </div>
          <div class="col-md-5">
            <div class="form-group bmd-form-group has-info">
              <label class="bmd-label-floating">Nome</label>
              <input readonly="" maxlength="100" name="nome" type="text" class="form-control" value="<?php echo $vUsuNome; ?>" />
            </div>
          </div>
          <div class="col-md-5">
            <div class="form-group bmd-form-group has-info">
              <label class="bmd-label-floating">Email</label>
              <input readonly="" maxlength="150" name="email" type="text" class="form-control" value="<?php echo $vUsuEmail; ?>" />
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4">
            <div class="form-group bmd-form-group has-info">
              <label class="bmd-label-floating">Ativo</label>
              <input readonly="" maxlength="80" name="ativo" type="text" class="form-control" value="<?php echo $strAtivo; ?>" />
            </div>
          </div>
          <div class="col-md-8">
            <div class="form-group bmd-form-group has-info">
              <label class="bmd-label-floating">Cadastrado Por</label>
              <input readonly="" maxlength="80" name="cadastrado_por" type="text" class="form-control" value="<?php echo $vUsaUsuario; ?>" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<a href="<?php echo base_url() ?>Usuario" class="btn btn-default pull-right">
  &#60; Voltar
  <div class="ripple-container"></div>
</a>
<div class="clearfix"></div>