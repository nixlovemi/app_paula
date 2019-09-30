<?php
$Pessoa     = $Cliente ?? array();
$pesNome    = $Pessoa["pes_nome"] ?? "";
$pesEmail   = $Pessoa["pes_email"] ?? "";
$pesNasc    = $Pessoa["pes_nascimento"] ?? "";
$pesTel     = $Pessoa["pes_telefone"] ?? "";
$pesCel     = $Pessoa["pes_celular"] ?? "";
$pesSexo    = $Pessoa["pes_sexo"] ?? "";
$pesCidId   = $Pessoa["pes_cid_id"] ?? "";
$pesCidDesc = $Pessoa["cid_desc"] ?? "";

$arrSexo  = array(
  "M" => "Masculino",
  "F" => "Feminino",
);
$strNasc  = ($pesNasc != "") ? date("d/m/Y", strtotime($pesNasc)): "";
?>

<form method="post" action="<?php echo base_url() ?>Cliente/postNovo">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-info">
          <h4 class="card-title">Novo Cliente</h4>
          <p class="card-category">Controle dos clientes que terão acesso ao sistema. Lembre-se de configurar os parâmetros de cada cadastro.</p>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group bmd-form-group has-info">
                <label class="bmd-label-floating">Nome</label>
                <input maxlength="100" name="nome" type="text" class="form-control" value="<?php echo $pesNome; ?>" />
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group bmd-form-group has-info">
                <label class="bmd-label-floating">Email</label>
                <input maxlength="150" name="email" type="text" class="form-control" value="<?php echo $pesEmail; ?>" />
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group bmd-form-group has-info">
                <label class="bmd-label-floating">Senha</label>
                <input maxlength="60" name="senha" type="password" class="form-control" value="" />
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

  <a href="<?php echo base_url() ?>Cliente" class="btn btn-default pull-right">
    Voltar
    <div class="ripple-container"></div>
  </a>
  <button type="submit" class="btn btn-info pull-right">Salvar</button>
  <div class="clearfix"></div>
</form>