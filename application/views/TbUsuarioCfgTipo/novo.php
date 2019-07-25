<?php
$UsuarioCfgTipo = $UsuarioCfgTipo ?? array();
$uctDescricao   = $UsuarioCfgTipo["uct_descricao"] ?? "";
?>
<form method="post" action="<?php echo base_url() ?>UsuarioCfgTipo/postNovo">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-success">
          <h4 class="card-title">Novo Tipo de configuração</h4>
          <p class="card-category">Cada nova configuração deve ser programada no servidor. </p>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group bmd-form-group has-success">
                <label class="bmd-label-floating">Descrição</label>
                <input maxlength="80" name="descricao" type="text" class="form-control" value="<?php echo $uctDescricao; ?>" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <a href="<?php echo base_url() ?>UsuarioCfgTipo" class="btn btn-info pull-right">
    Voltar
    <div class="ripple-container"></div>
  </a>
  <button type="submit" class="btn btn-success pull-right">Salvar</button>
  <div class="clearfix"></div>
</form>