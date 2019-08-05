<?php
$Grupo      = $Grupo ?? array();
$arrPessoas = $arrPessoas ?? array();

$vDescricao = $Grupo["gru_descricao"] ?? "";
$vInicio    = $Grupo["gru_dt_inicio"] ?? "";
$vTermino   = $Grupo["gru_dt_termino"] ?? "";

$strDtIni   = ($vInicio != "") ? date("d/m/Y", strtotime($vInicio)): "";
$strDtFim   = ($vTermino != "") ? date("d/m/Y", strtotime($vTermino)): "";
?>

<form method="post" action="<?php echo base_url() ?>Grupo/postNovo">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-info">
          <h4 class="card-title">Grupo</h4>
          <p class="card-category">Controle dos grupos gerenciados por você. Crie ou visualize informações dos grupos aqui.</p>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group bmd-form-group has-info">
                <label class="bmd-label-floating">Descrição</label>
                <input maxlength="80" name="descricao" type="text" class="form-control" value="<?php echo $vDescricao; ?>" />
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group bmd-form-group has-info">
                <label class="label-control bmd-label-static">Início</label>
                <input name="inicio" type="text" class="form-control datepicker" value="<?php echo $strDtIni; ?>" />
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group bmd-form-group has-info">
                <label class="label-control bmd-label-static">Término</label>
                <input name="termino" type="text" class="form-control datepicker" value="<?php echo $strDtFim; ?>" />
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
          <h4 class="card-title">Participantes</h4>
        </div>
        <div class="card-body">
          <?php
          $arrLoop = array_chunk($arrPessoas, 3);
          foreach($arrLoop as $Pessoas){
            echo "<div class='row'>";
            foreach($Pessoas as $Pessoa){
              $pesId   = $Pessoa["pes_id"] ?? "";
              $pesNome = $Pessoa["pes_nome"] ?? "";
              $petDesc = $Pessoa["pet_descricao"] ?? "";

              echo "<div class='col-md-4'>";
              echo "  <div class='form-check'>";
              echo "    <label class='form-check-label'>";
              echo "      <input name='participantes[]' class='form-check-input' type='checkbox' value='$pesId' /> $pesNome [$petDesc]";
              echo "      <span class='form-check-sign'>";
              echo "        <span class='check'></span>";
              echo "      </span>";
              echo "    </label>";
              echo "  </div>";
              echo "</div>";
            }
            echo "</div>";
          }
          ?>
        </div>
      </div>
    </div>
  </div>

  <a href="<?php echo base_url() ?>Grupo" class="btn btn-default pull-right">
    Voltar
    <div class="ripple-container"></div>
  </a>
  <button type="submit" class="btn btn-info pull-right">Salvar</button>
  <div class="clearfix"></div>
</form>