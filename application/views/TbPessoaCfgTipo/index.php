<?php
$htmlLista = $htmlLista ?? "";
?>

<div class="row">
  <div class="col-md-12 ml-auto mr-auto">
    <a href="<?php echo base_url() ?>PessoaCfgTipo/novo" class="btn btn-info btn-lg">
      Novo
      <div class="ripple-container"></div>
    </a>
  </div>
</div>
<div class="card">
  <div class="card-header card-header-info">
    <h3 class="card-title">Tipo de Configuração</h3>
    <p class="card-category">
      Configurações para controle das pessoas do sistema. Cada nova configuração deve ser programada no servidor.
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