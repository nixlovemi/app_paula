<?php
$htmlLista = $htmlLista ?? "";
?>

<div class="row">
  <div class="col-md-12 ml-auto mr-auto">
    <a href="<?php echo base_url() ?>Pessoa/novo" class="btn btn-info btn-lg">
      Novo
      <div class="ripple-container"></div>
    </a>
  </div>
</div>
<div class="card">
  <div class="card-header card-header-info">
    <h3 class="card-title">Pessoa</h3>
    <p class="card-category">
      Controle das pessoas que farão parte dos seus grupos. Isso inclui pessoas do staff e clientes.
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