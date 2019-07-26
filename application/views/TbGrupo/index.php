<?php
$htmlLista = $htmlLista ?? "";
?>

<div class="row">
  <div class="col-md-12 ml-auto mr-auto">
    <a href="<?php echo base_url() ?>Grupo/novo" class="btn btn-info btn-lg">
      Novo
      <div class="ripple-container"></div>
    </a>
  </div>
</div>
<div class="card">
  <div class="card-header card-header-success">
    <h3 class="card-title">Grupo</h3>
    <p class="card-category">
      Controle dos grupos gerenciados por você. Crie ou visualize informações dos grupos aqui.
    </p>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-md-12">
        <?php echo $htmlLista; ?>
      </div>
    </div>
  </div>
</div>