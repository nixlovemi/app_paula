<?php
$UsuarioCfgTipo = $UsuarioCfgTipo ?? array();
$uctId          = $UsuarioCfgTipo["uct_id"] ?? "";
$uctDescricao   = $UsuarioCfgTipo["uct_descricao"] ?? "";
$uctAtivo       = $UsuarioCfgTipo["uct_ativo"] ?? "";

$arrAtivo       = array(
  "0" => "Ativo: Não",
  "1" => "Ativo: Sim",
);
?>
<form method="post" action="<?php echo base_url() ?>UsuarioCfgTipo/postEditar">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-success">
          <h4 class="card-title">Editar Tipo de configuração</h4>
          <p class="card-category">Cada nova configuração deve ser programada no servidor. </p>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-2">
              <div class="form-group bmd-form-group has-success">
                <label class="bmd-label-floating">ID</label>
                <input readonly="" maxlength="80" name="id" type="text" class="form-control" value="<?php echo $uctId; ?>" />
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group bmd-form-group has-success">
                <label class="bmd-label-floating">Descrição</label>
                <input maxlength="80" name="descricao" type="text" class="form-control" value="<?php echo $uctDescricao; ?>" />
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group bmd-form-group has-success">
                <select name="ativo" class="form-control" size="">
                  <?php
                  foreach($arrAtivo as $id => $text){
                    $selected = ($id == $uctAtivo) ? "selected": "";
                    echo "<option value='$id' $selected>$text</option>";
                  }
                  ?>
                </select>
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