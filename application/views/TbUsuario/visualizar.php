<?php
$Usuario        = $Usuario ?? array();
$htmlConfigList = $htmlConfigList ?? "";

$vUsuId      = $Usuario["usu_id"] ?? "";
$vUsuEmail   = $Usuario["usu_email"] ?? "";
$vUsuNome    = $Usuario["usu_nome"] ?? "";
$vUsuAtivo   = $Usuario["usu_ativo"] ?? "";
$vUsaUsuario = $Usuario["usa_usuario"] ?? "";
$usuNasc     = $Usuario["usu_nascimento"] ?? "";
$usuTel      = $Usuario["usu_telefone"] ?? "";
$usuCel      = $Usuario["usu_celular"] ?? "";
$usuSexo     = $Usuario["usu_sexo"] ?? "";
$usuCidId    = $Usuario["usu_cid_id"] ?? "";
$cidDesc     = $Usuario["cid_descricao"] ?? "";
$estDesc     = $Usuario["est_descricao"] ?? "";
$usuCidDesc  = ($cidDesc != "" && $estDesc != "") ? "$cidDesc - $estDesc": "";

$strAtivo = "";
if($vUsuAtivo != ""){
  $strAtivo = ($vUsuAtivo == 0) ? "Não": "Sim";
}
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
        <h4 class="card-title">Visualizar Usuário</h4>
        <p class="card-category">Controle dos usuários (clientes) que terão acesso ao sistema. Lembre-se de configurar os parâmetros de cada cadastro.</p>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-2">
            <div class="form-group bmd-form-group has-info">
              <label class="bmd-label-floating">ID</label>
              <input readonly="" maxlength="80" name="id" type="text" class="form-control" value="<?php echo $vUsuId; ?>" />
            </div>
          </div>
          <div class="col-md-5">
            <div class="form-group bmd-form-group has-info">
              <label class="bmd-label-floating">Nome</label>
              <input readonly="" maxlength="100" name="nome" type="text" class="form-control" value="<?php echo $vUsuNome; ?>" />
            </div>
          </div>
          <div class="col-md-5">
            <div class="form-group bmd-form-group has-info">
              <label class="bmd-label-floating">Email</label>
              <input readonly="" maxlength="150" name="email" type="text" class="form-control" value="<?php echo $vUsuEmail; ?>" />
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
        <div class="row">
          <div class="col-md-4">
            <div class="form-group bmd-form-group has-info">
              <label class="bmd-label-floating">Ativo</label>
              <input readonly="" maxlength="80" name="ativo" type="text" class="form-control" value="<?php echo $strAtivo; ?>" />
            </div>
          </div>
          <div class="col-md-8">
            <div class="form-group bmd-form-group has-info">
              <label class="bmd-label-floating">Cadastrado Por</label>
              <input readonly="" maxlength="80" name="cadastrado_por" type="text" class="form-control" value="<?php echo $vUsaUsuario; ?>" />
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
            <span id="spnListaUsuarioConfig">
              <?php echo $htmlConfigList; ?>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<a href="<?php echo base_url() ?>Usuario" class="btn btn-default pull-right">
  &#60; Voltar
  <div class="ripple-container"></div>
</a>
<div class="clearfix"></div>