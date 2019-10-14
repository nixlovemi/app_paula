<?php
$Pessoa         = $Cliente ?? array();
$arrPesCfgTipo  = $arrPesCfgTipo ?? array();
$htmlConfigList = $htmlConfigList ?? "";

$vPesId      = $Pessoa["pes_id"] ?? "";
$vPesEmail   = $Pessoa["pes_email"] ?? "";
$vPesNome    = $Pessoa["pes_nome"] ?? "";
$vPesAtivo   = $Pessoa["pes_ativo"] ?? "";
$usuNasc     = $Pessoa["pes_nascimento"] ?? "";
$usuTel      = $Pessoa["pes_telefone"] ?? "";
$usuCel      = $Pessoa["pes_celular"] ?? "";
$usuSexo     = $Pessoa["pes_sexo"] ?? "";
$usuCidId    = $Pessoa["pes_cid_id"] ?? "";
$cidDesc     = $Pessoa["cid_descricao"] ?? "";
$estDesc     = $Pessoa["est_descricao"] ?? "";
if(isset($Pessoa["cid_desc"])){
  $usuCidDesc = $Pessoa["cid_desc"] ?? "";
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

<form id="frmEditaCliente" method="post" action="<?php echo BASE_URL?>Cliente/postEditar">
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
                <input readonly="" maxlength="80" name="id" id="id" type="text" class="form-control" value="<?php echo $vPesId; ?>" />
              </div>
            </div>
            <div class="col-md-5">
              <div class="form-group bmd-form-group has-info">
                <label class="bmd-label-floating">Nome</label>
                <input maxlength="100" name="nome" type="text" class="form-control" value="<?php echo $vPesNome; ?>" />
              </div>
            </div>
            <div class="col-md-5">
              <div class="form-group bmd-form-group has-info">
                <label class="bmd-label-floating">Email</label>
                <input maxlength="150" name="email" type="text" class="form-control" value="<?php echo $vPesEmail; ?>" />
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
            <div class="col-md-3">
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
            <div class="col-md-3">
              <div class="form-group bmd-form-group has-info">
                <label class="label-control bmd-label-static text-default">Ativo</label>
                <select name="ativo" class="form-control" size="">
                  <?php
                  foreach($arrAtivo as $id => $text){
                    $selected = ($id == $vPesAtivo) ? "selected": "";
                    echo "<option value='$id' $selected>$text</option>";
                  }
                  ?>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group bmd-form-group has-info">
                <label class="label-control bmd-label-static text-default">Cidade</label>
                <input maxlength="100" name="cidade" type="text" class="form-control inpt-seleciona-modal" data-id="<?=$usuCidId?>" data-controller="Json" data-action="jsonCidadeSeleciona" data-title="Pesquisar Cidade" value="<?=$usuCidDesc?>" />
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
                  foreach($arrPesCfgTipo as $UsuCfgTipo){
                    $value = $UsuCfgTipo["pct_id"] ?? "";
                    $text  = $UsuCfgTipo["pct_descricao"] ?? "";

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
              <button type="button" class="btn btn-info btn-sm" onclick="jsonAddPesCfg( $('form#frmEditaCliente #id').val(), $('form#frmEditaCliente #configuracao').val(), $('form#frmEditaCliente #valor').val() );">
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
    Voltar
    <div class="ripple-container"></div>
  </a>
  <button type="submit" class="btn btn-info pull-right">Salvar</button>
  <div class="clearfix"></div>
</form>