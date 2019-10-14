<?php
$Usuario        = $Usuario ?? array();
$arrUsuCfgTipo  = $arrUsuCfgTipo ?? array();
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
if(isset($Usuario["cid_desc"])){
  $usuCidDesc = $Usuario["cid_desc"] ?? "";
} else {
  $usuCidDesc = ($cidDesc != "" && $estDesc != "") ? "$cidDesc - $estDesc": "";
}

$arrAtivo = array(
  "0" => "Não",
  "1" => "Sim",
);
$arrSexo  = array(
  "M" => "Masculino",
  "F" => "Feminino",
);
$strNasc = ($usuNasc != "") ? date("d/m/Y", strtotime($usuNasc)): "";
?>

<form id="frmEditaUsuario" method="post" action="<?php echo BASE_URL?>Usuario/postEditar">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-info">
          <h4 class="card-title">Editar Usuário</h4>
          <p class="card-category">Controle dos usuários (clientes) que terão acesso ao sistema. Lembre-se de configurar os parâmetros de cada cadastro.</p>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-2">
              <div class="form-group bmd-form-group has-info">
                <label class="bmd-label-floating">ID</label>
                <input readonly="" maxlength="80" name="id" id="id" type="text" class="form-control" value="<?php echo $vUsuId; ?>" />
              </div>
            </div>
            <div class="col-md-5">
              <div class="form-group bmd-form-group has-info">
                <label class="bmd-label-floating">Nome</label>
                <input maxlength="100" name="nome" type="text" class="form-control" value="<?php echo $vUsuNome; ?>" />
              </div>
            </div>
            <div class="col-md-5">
              <div class="form-group bmd-form-group has-info">
                <label class="bmd-label-floating">Email</label>
                <input maxlength="150" name="email" type="text" class="form-control" value="<?php echo $vUsuEmail; ?>" />
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
                <input maxlength="15" name="telefone" type="text" class="form-control inpt-celular-ddd" value="<?=$usuTel?>" />
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group bmd-form-group has-info">
                <label class="bmd-label-floating">Celular</label>
                <input maxlength="15" name="celular" type="text" class="form-control inpt-celular-ddd" value="<?=$usuCel?>" />
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
                    $selec = ($sxSigla == $usuSexo) ? "selected": "";
                    echo "<option $selec value='$sxSigla'>$sxText</option>";
                  }
                  ?>
                </select>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group bmd-form-group has-info">
                <label class="label-control bmd-label-static text-default">Cidade</label>
                <input maxlength="100" name="cidade" type="text" class="form-control inpt-seleciona-modal" data-id="<?=$usuCidId?>" data-controller="Json" data-action="jsonCidadeSeleciona" data-title="Pesquisar Cidade" value="<?=$usuCidDesc?>" />
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4">
              <div class="form-group bmd-form-group has-info">
                <label class="label-control bmd-label-static text-default">Ativo</label>
                <select name="ativo" class="form-control" size="">
                  <?php
                  foreach($arrAtivo as $id => $text){
                    $selected = ($id == $vUsuAtivo) ? "selected": "";
                    echo "<option value='$id' $selected>$text</option>";
                  }
                  ?>
                </select>
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
            <div class="col-md-3">
              <div class="form-group bmd-form-group has-info">
                <select name="configuracao" id="configuracao" class="form-control" size="">
                  <?php
                  foreach($arrUsuCfgTipo as $UsuCfgTipo){
                    $value = $UsuCfgTipo["uct_id"] ?? "";
                    $text  = $UsuCfgTipo["uct_descricao"] ?? "";

                    echo "<option value='$value'>$text</option>";
                  }
                  ?>
                </select>
              </div>
            </div>
            <div class="col-md-7">
              <div class="form-group bmd-form-group has-info">
                <label class="bmd-label-floating">Valor</label>
                <input maxlength="100" name="valor" id="valor" type="text" class="form-control" value="" />
              </div>
            </div>
            <div class="col-md-2">
              <button type="button" class="btn btn-info btn-sm" onclick="jsonAddUsuCfg( $('form#frmEditaUsuario #id').val(), $('form#frmEditaUsuario #configuracao').val(), $('form#frmEditaUsuario #valor').val() );">
                Inserir
                <div class="ripple-container"></div>
              </button>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <span class="hr"></span>
            </div>
          </div>
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
    Voltar
    <div class="ripple-container"></div>
  </a>
  <button type="submit" class="btn btn-info pull-right">Salvar</button>
  <div class="clearfix"></div>
</form>