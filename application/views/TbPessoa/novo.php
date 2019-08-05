<?php
$Pessoa        = $Pessoa ?? array();
$arrPessoaTipo = $arrPessoaTipo ?? array();

$vTipo         = $Pessoa["pes_pet_id"] ?? "";
$vNome         = $Pessoa["pes_nome"] ?? "";
$vEmail        = $Pessoa["pes_email"] ?? "";
?>

<form method="post" action="<?php echo base_url() ?>Pessoa/postNovo">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-info">
          <h4 class="card-title">Nova Pessoa</h4>
          <p class="card-category">Controle das pessoas que farão parte dos seus grupos. Isso inclui pessoas do staff e clientes.</p>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group bmd-form-group has-info">
                <select name="tipo" class="form-control" size="">
                  <option value="">Escolher tipo ...</option>
                  <?php
                  foreach($arrPessoaTipo as $PessoaTipo){
                    $petId    = $PessoaTipo["pet_id"];
                    $petDesc  = $PessoaTipo["pet_descricao"];
                    $selected = ($vTipo == $petId) ? "selected": "";

                    echo "<option $selected value='$petId'>Tipo: $petDesc</option>";
                  }
                  ?>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group bmd-form-group has-info">
                <label class="bmd-label-floating">Nome</label>
                <input maxlength="100" name="nome" type="text" class="form-control" value="<?php echo $vNome; ?>" />
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group bmd-form-group has-info">
                <label class="bmd-label-floating">Email</label>
                <input maxlength="150" name="email" type="text" class="form-control" value="<?php echo $vEmail; ?>" />
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group bmd-form-group has-info">
                <label class="bmd-label-floating">Senha</label>
                <input maxlength="60" name="senha" type="password" class="form-control" value="" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <a href="<?php echo base_url() ?>Pessoa" class="btn btn-default pull-right">
    Voltar
    <div class="ripple-container"></div>
  </a>
  <button type="submit" class="btn btn-info pull-right">Salvar</button>
  <div class="clearfix"></div>
</form>