<?php
$Pessoa        = $Pessoa ?? array();
$arrPessoaTipo = $arrPessoaTipo ?? array();

$vId           = $Pessoa["pes_id"] ?? "";
$vTipo         = $Pessoa["pes_pet_id"] ?? "";
$vNome         = $Pessoa["pes_nome"] ?? "";
$vEmail        = $Pessoa["pes_email"] ?? "";
$vAtivo        = $Pessoa["pes_ativo"] ?? "";
$pesNasc       = $Pessoa["pes_nascimento"] ?? "";
$pesTel        = $Pessoa["pes_telefone"] ?? "";
$pesCel        = $Pessoa["pes_celular"] ?? "";
$pesSexo       = $Pessoa["pes_sexo"] ?? "";
$pesCidId      = $Pessoa["pes_cid_id"] ?? "";
$cidDesc       = $Pessoa["cid_descricao"] ?? "";
$estDesc       = $Pessoa["est_descricao"] ?? "";
if(isset($Pessoa["cid_desc"])){
  $pesCidDesc  = $Pessoa["cid_desc"] ?? "";
} else {
  $pesCidDesc  = ($cidDesc != "" && $estDesc != "") ? "$cidDesc - $estDesc": "";
}

$arrAtivo = array(
  "0" => "Não",
  "1" => "Sim",
);
$arrSexo  = array(
  "M" => "Masculino",
  "F" => "Feminino",
);
$strNasc = ($pesNasc != "") ? date("d/m/Y", strtotime($pesNasc)): "";
?>

<form method="post" action="<?php echo base_url() ?>Pessoa/postEditar">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-info">
          <h4 class="card-title">Editar Pessoa</h4>
          <p class="card-category">Controle das pessoas que farão parte dos seus grupos. Isso inclui pessoas do staff e clientes.</p>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-2">
              <div class="form-group bmd-form-group has-info">
                <label class="bmd-label-floating">ID</label>
                <input readonly="" maxlength="100" name="id" type="text" class="form-control" value="<?php echo $vId; ?>" />
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group bmd-form-group has-info">
                <label class="label-control bmd-label-static text-default">Tipo</label>
                <select name="tipo" class="form-control" size="">
                  <option value=""></option>
                  <?php
                  foreach($arrPessoaTipo as $PessoaTipo){
                    $petId    = $PessoaTipo["pet_id"];
                    $petDesc  = $PessoaTipo["pet_descricao"];
                    $selected = ($vTipo == $petId) ? "selected": "";

                    echo "<option $selected value='$petId'>$petDesc</option>";
                  }
                  ?>
                </select>
              </div>
            </div>
            <div class="col-md-7">
              <div class="form-group bmd-form-group has-info">
                <label class="bmd-label-floating">Nome</label>
                <input maxlength="100" name="nome" type="text" class="form-control" value="<?php echo $vNome; ?>" />
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-7">
              <div class="form-group bmd-form-group has-info">
                <label class="bmd-label-floating">Email</label>
                <input maxlength="150" name="email" type="text" class="form-control" value="<?php echo $vEmail; ?>" />
              </div>
            </div>
            <div class="col-md-5">
              <div class="form-group bmd-form-group has-info">
                <label class="label-control bmd-label-static text-default">Ativo</label>
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
          <div class="row">
            <div class="col-md-4">
              <div class="form-group bmd-form-group has-info">
                <label class="label-control bmd-label-static text-default">Nascimento</label>
                <input maxlength="10" name="nascimento" type="text" class="form-control datepicker" value="<?=$strNasc?>" />
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group bmd-form-group has-info">
                <label class="bmd-label-floating">Telefone</label>
                <input maxlength="15" name="telefone" type="text" class="form-control inpt-celular-ddd" value="<?=$pesTel?>" />
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group bmd-form-group has-info">
                <label class="bmd-label-floating">Celular</label>
                <input maxlength="15" name="celular" type="text" class="form-control inpt-celular-ddd" value="<?=$pesCel?>" />
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4">
              <div class="form-group bmd-form-group has-info">
                <label class="label-control bmd-label-static text-default">Sexo</label>
                <select name="sexo" class="form-control" size="">
                  <?php
                  foreach($arrSexo as $sxSigla => $sxText){
                    $selec = ($sxSigla == $pesSexo) ? "selected": "";
                    echo "<option $selec value='$sxSigla'>$sxText</option>";
                  }
                  ?>
                </select>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group bmd-form-group has-info">
                <label class="label-control bmd-label-static text-default">Cidade</label>
                <input maxlength="100" name="cidade" type="text" class="form-control inpt-seleciona-modal" data-id="<?=$pesCidId?>" data-controller="Json" data-action="jsonCidadeSeleciona" data-title="Pesquisar Cidade" value="<?=$pesCidDesc?>" />
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