<?php
$Usuario  = $Usuario ?? array();
$usuNome  = $Usuario["usu_nome"] ?? "";
$usuEmail = $Usuario["usu_email"] ?? "";
?>

<form method="post" action="<?php echo base_url() ?>Usuario/postNovo">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-success">
          <h4 class="card-title">Novo Usuário</h4>
          <p class="card-category">Controle dos usuários (clientes) que terão acesso ao sistema. Lembre-se de configurar os parâmetros de cada cadastro.</p>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group bmd-form-group has-success">
                <label class="bmd-label-floating">Nome</label>
                <input maxlength="100" name="nome" type="text" class="form-control" value="<?php echo $usuNome; ?>" />
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group bmd-form-group has-success">
                <label class="bmd-label-floating">Email</label>
                <input maxlength="150" name="email" type="text" class="form-control" value="<?php echo $usuEmail; ?>" />
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group bmd-form-group has-success">
                <label class="bmd-label-floating">Senha</label>
                <input maxlength="60" name="senha" type="password" class="form-control" value="" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <a href="<?php echo base_url() ?>Usuario" class="btn btn-danger pull-right">
    Cancelar
    <div class="ripple-container"></div>
  </a>
  <button type="submit" class="btn btn-success pull-right">Salvar</button>
  <div class="clearfix"></div>
</form>