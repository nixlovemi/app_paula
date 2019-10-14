<?php
$Pessoa     = $Pessoa ?? array();

$vId        = $Pessoa["pes_id"] ?? "";
$vTipo      = $Pessoa["pet_descricao"] ?? "";
$vNome      = $Pessoa["pes_nome"] ?? "";
$vEmail     = $Pessoa["pes_email"] ?? "";
$vAtivo     = $Pessoa["pes_ativo"] ?? "";
$usuNasc    = $Pessoa["pes_nascimento"] ?? "";
$usuTel     = $Pessoa["pes_telefone"] ?? "";
$usuCel     = $Pessoa["pes_celular"] ?? "";
$usuSexo    = $Pessoa["pes_sexo"] ?? "";
$usuCidId   = $Pessoa["pes_cid_id"] ?? "";
$cidDesc    = $Pessoa["cid_descricao"] ?? "";
$estDesc    = $Pessoa["est_descricao"] ?? "";
$usuCidDesc = ($cidDesc != "" && $estDesc != "") ? "$cidDesc - $estDesc": "";

$vStrAtivo = ($vAtivo == 1) ? "Sim": "Não";
$arrSexo  = array(
  "M" => "Masculino",
  "F" => "Feminino",
);
$strNasc = ($usuNasc != "") ? date("d/m/Y", strtotime($usuNasc)): "";
?>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-header-info">
        <h4 class="card-title">Visualizar Pessoa</h4>
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
          <div class="col-md-5">
            <div class="form-group bmd-form-group has-info">
              <label class="bmd-label-floating">Tipo</label>
              <input readonly="" maxlength="100" name="tipo" type="text" class="form-control" value="<?php echo $vTipo; ?>" />
            </div>
          </div>
          <div class="col-md-5">
            <div class="form-group bmd-form-group has-info">
              <label class="bmd-label-floating">Nome</label>
              <input readonly="" maxlength="100" name="nome" type="text" class="form-control" value="<?php echo $vNome; ?>" />
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-7">
            <div class="form-group bmd-form-group has-info">
              <label class="bmd-label-floating">Email</label>
              <input readonly="" maxlength="150" name="email" type="text" class="form-control" value="<?php echo $vEmail; ?>" />
            </div>
          </div>
          <div class="col-md-5">
            <div class="form-group bmd-form-group has-info">
              <label class="bmd-label-floating">Ativo</label>
              <input readonly="" maxlength="150" name="ativo" type="text" class="form-control" value="<?php echo $vStrAtivo; ?>" />
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4">
            <div class="form-group bmd-form-group has-info">
              <label class="label-control bmd-label-static text-default">Nascimento</label>
              <input readonly="" maxlength="10" name="nascimento" type="text" class="form-control" value="<?=$strNasc?>" />
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group bmd-form-group has-info">
              <label class="bmd-label-floating">Telefone</label>
              <input readonly="" maxlength="15" name="telefone" type="text" class="form-control inpt-celular-ddd" value="<?=$usuTel?>" />
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group bmd-form-group has-info">
              <label class="bmd-label-floating">Celular</label>
              <input readonly="" maxlength="15" name="celular" type="text" class="form-control inpt-celular-ddd" value="<?=$usuCel?>" />
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4">
            <div class="form-group bmd-form-group has-info">
              <label class="label-control bmd-label-static text-default">Sexo</label>
              <input readonly="" maxlength="50" name="sexo" type="text" class="form-control" value="<?=($arrSexo[$usuSexo]??"")?>" />
            </div>
          </div>
          <div class="col-md-8">
            <div class="form-group bmd-form-group has-info">
              <label class="label-control bmd-label-static text-default">Cidade</label>
              <input readonly="" maxlength="100" name="cidade" type="text" class="form-control inpt-seleciona-modal" value="<?=$usuCidDesc?>" />
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
<div class="clearfix"></div>
