<?php
$PessoaCfgTipo = $PessoaCfgTipo ?? array();
$pctId          = $PessoaCfgTipo["pct_id"] ?? "";
$pctDescricao   = $PessoaCfgTipo["pct_descricao"] ?? "";
$pctAtivo       = $PessoaCfgTipo["pct_ativo"] ?? "";

$arrAtivo       = array(
  "0" => "Ativo: Não",
  "1" => "Ativo: Sim",
);
?>
<form method="post" action="<?php echo base_url() ?>PessoaCfgTipo/postEditar">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-info">
          <h4 class="card-title">Editar Tipo de configuração</h4>
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
                <input maxlength="80" name="descricao" type="text" class="form-control" value="<?php echo $pctDescricao; ?>" />
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group bmd-form-group has-info">
                <select name="ativo" class="form-control" size="">
                  <?php
                  foreach($arrAtivo as $id => $text){
                    $selected = ($id == $pctAtivo) ? "selected": "";
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

  <a href="<?php echo base_url() ?>PessoaCfgTipo" class="btn btn-default pull-right">
    Voltar
    <div class="ripple-container"></div>
  </a>
  <button type="submit" class="btn btn-info pull-right">Salvar</button>
  <div class="clearfix"></div>
</form>