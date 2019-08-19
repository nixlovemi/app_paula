<?php
$pesNome  = $pesNome ?? "";
$pesFoto  = $pesFoto ?? base_url() . FOTO_DEFAULT;
$pesEmail = $pesEmail ?? "";
?>

<form method="post" action="<?php echo base_url() ?>GrpConfig/postIndex">
  <div class="row">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header card-header-info">
          <h4 class="card-title">Dados</h4>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group bmd-form-group has-info">
                <label class="bmd-label-floating">Nome</label>
                <input maxlength="100" name="nome" type="text" class="form-control" value="<?php echo $pesNome; ?>" />
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header card-header-info">
          <h4 class="card-title">Alterar Senha</h4>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group bmd-form-group has-info">
                <label class="bmd-label-floating">Nova Senha</label>
                <input maxlength="60" name="nova_senha" type="password" class="form-control" value="" />
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group bmd-form-group has-info">
                <label class="bmd-label-floating">Repita a Nova Senha</label>
                <input maxlength="60" name="repita_senha" type="password" class="form-control" value="" />
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group bmd-form-group has-info">
                <label class="bmd-label-floating">Senha Anterior</label>
                <input maxlength="60" name="anterior_senha" type="password" class="form-control" value="" />
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <i><small>* Se você precisar resetar sua senha, entre em contato com o administrador do grupo.</small></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card card-profile">
        <div class="card-avatar">
          <a href="#pablo">
            <img class="img" src="<?php echo $pesFoto; ?>" />
          </a>
        </div>
        <div class="card-body">
          <h4 class="card-title"><?php echo $pesNome; ?></h4>
          <h6 class="card-category text-gray"><?php echo $pesEmail; ?></h6>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-8"></div>
    <div class="col-md-4">
      <a href="<?=base_url()?>SisGrupo" class="btn btn-default pull-right">
        Voltar
        <div class="ripple-container"></div>
      </a>
      <button type="submit" class="btn btn-info pull-right">Salvar</button>
      <div class="clearfix"></div>
    </div>
  </div>
</form>