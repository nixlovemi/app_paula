<?php
$Pessoa        = $Pessoa ?? array();
$arrPessoaTipo = $arrPessoaTipo ?? array();

$vId           = $Pessoa["pes_id"] ?? "";
$vTipo         = $Pessoa["pes_pet_id"] ?? "";
$vNome         = $Pessoa["pes_nome"] ?? "";
$vEmail        = $Pessoa["pes_email"] ?? "";
$vAtivo        = $Pessoa["pes_ativo"] ?? "";

$arrAtivo       = array(
  "0" => "Ativo: Não",
  "1" => "Ativo: Sim",
);

#pes_foto
?>

<form method="post" action="<?php echo base_url() ?>Pessoa/postEditar">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-success">
          <h4 class="card-title">Editar Pessoa</h4>
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
            <div class="col-md-5">
              <div class="form-group bmd-form-group has-success">
                <label class="bmd-label-floating">Nome</label>
                <input maxlength="100" name="nome" type="text" class="form-control" value="<?php echo $vNome; ?>" />
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-7">
              <div class="form-group bmd-form-group has-success">
                <label class="bmd-label-floating">Email</label>
                <input maxlength="150" name="email" type="text" class="form-control" value="<?php echo $vEmail; ?>" />
              </div>
            </div>
            <div class="col-md-5">
              <div class="form-group bmd-form-group has-success">
                <select name="ativo" class="form-control" size="">
                  <?php
                  foreach($arrAtivo as $id => $text){
                    $selected = ($id == $vAtivo) ? "selected": "";
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

  <a href="<?php echo base_url() ?>Pessoa" class="btn btn-info pull-right">
    Voltar
    <div class="ripple-container"></div>
  </a>
  <button type="submit" class="btn btn-success pull-right">Salvar</button>
  <div class="clearfix"></div>
</form>