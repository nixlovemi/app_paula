<?php
$Cliente        = $Pessoa ?? array();
$htmlConfigList = $htmlConfigList ?? "";

$vPesId       = $Cliente["pes_id"] ?? "";
$vPesEmail    = $Cliente["pes_email"] ?? "";
$vPesNome     = $Cliente["pes_nome"] ?? "";
$vPesAtivo    = $Cliente["pes_ativo"] ?? "";
$vUsaUsuario  = $Cliente["usa_usuario"] ?? "";
$vPesNasc     = $Cliente["pes_nascimento"] ?? "";
$vPesTel      = $Cliente["pes_telefone"] ?? "";
$vPesCel      = $Cliente["pes_celular"] ?? "";
$vPesSexo     = $Cliente["pes_sexo"] ?? "";
$vPesCidId    = $Cliente["pes_cid_id"] ?? "";
$cidDesc      = $Cliente["cid_descricao"] ?? "";
$estDesc      = $Cliente["est_descricao"] ?? "";
$vPesCidDesc  = ($cidDesc != "" && $estDesc != "") ? "$cidDesc - $estDesc": "";

$strAtivo = "";
if($vPesAtivo != ""){
  $strAtivo = ($vPesAtivo == 0) ? "Não": "Sim";
}
$arrSexo  = array(
  "M" => "Masculino",
  "F" => "Feminino",
);
$strNasc = ($vPesNasc != "") ? date("d/m/Y", strtotime($vPesNasc)): "";
?>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-header-info">
        <h4 class="card-title">Visualizar Cliente</h4>
        <p class="card-category">Controle dos clientes que terão acesso ao sistema. Lembre-se de configurar os parâmetros de cada cadastro.</p>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-2">
            <div class="form-group bmd-form-group has-info">
              <label class="bmd-label-floating">ID</label>
              <input readonly="" maxlength="80" name="id" type="text" class="form-control" value="<?php echo $vPesId; ?>" />
            </div>
          </div>
          <div class="col-md-5">
            <div class="form-group bmd-form-group has-info">
              <label class="bmd-label-floating">Nome</label>
              <input readonly="" maxlength="100" name="nome" type="text" class="form-control" value="<?php echo $vPesNome; ?>" />
            </div>
          </div>
          <div class="col-md-5">
            <div class="form-group bmd-form-group has-info">
              <label class="bmd-label-floating">Email</label>
              <input readonly="" maxlength="150" name="email" type="text" class="form-control" value="<?php echo $vPesEmail; ?>" />
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
              <input readonly="" maxlength="15" name="telefone" type="text" class="form-control inpt-celular-ddd" value="<?=$vPesTel?>" />
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group bmd-form-group has-info">
              <label class="bmd-label-floating">Celular</label>
              <input readonly="" maxlength="15" name="celular" type="text" class="form-control inpt-celular-ddd" value="<?=$vPesCel?>" />
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-3">
            <div class="form-group bmd-form-group has-info">
              <label class="label-control bmd-label-static text-default">Sexo</label>
              <input readonly="" maxlength="50" name="sexo" type="text" class="form-control" value="<?=($arrSexo[$vPesSexo]??"")?>" />
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group bmd-form-group has-info">
              <label class="bmd-label-floating">Ativo</label>
              <input readonly="" maxlength="80" name="ativo" type="text" class="form-control" value="<?php echo $strAtivo; ?>" />
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group bmd-form-group has-info">
              <label class="label-control bmd-label-static text-default">Cidade</label>
              <input readonly="" maxlength="100" name="cidade" type="text" class="form-control inpt-seleciona-modal" value="<?=$vPesCidDesc?>" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-header-info">
        <h4 class="card-title">Configuração</h4>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-12">
            <span id="spnListaClienteConfig">
              <?php echo $htmlConfigList; ?>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<a href="<?php echo base_url() ?>Cliente" class="btn btn-default pull-right">
  &#60; Voltar
  <div class="ripple-container"></div>
</a>
<div class="clearfix"></div>