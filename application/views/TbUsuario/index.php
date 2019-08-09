<?php
$htmlLista = $htmlLista ?? "";
?>

<div class="row">
  <div class="col-md-12 ml-auto mr-auto">
    <a href="<?php echo base_url() ?>Usuario/novo" class="btn btn-info btn-lg">
      Novo
      <div class="ripple-container"></div>
    </a>
  </div>
</div>
<div class="card">
  <div class="card-header card-header-info">
    <h3 class="card-title">Usuário</h3>
    <p class="card-category">
      Controle dos usuários (clientes) que terão acesso ao sistema. Lembre-se de configurar os parâmetros de cada cadastro.
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