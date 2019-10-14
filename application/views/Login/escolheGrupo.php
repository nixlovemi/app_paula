<?php
$vGruposPessoa = $vGruposPessoa ?? array();
?>

<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8" />
    <link rel="apple-touch-icon" sizes="76x76" href="<?php echo BASE_URL; ?>template/assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>template/assets/img/favicon.png">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>
      Grupo - Escolher
    </title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <!--     Fonts and icons     -->
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">
    <!-- CSS Files -->
    <link href="<?php echo BASE_URL; ?>template/assets/css/material-dashboard.css?v=2.1.1" rel="stylesheet" />
    <link href="<?php echo BASE_URL; ?>template/assets/demo/demo.css" rel="stylesheet" />
  </head>

  <body class="bg-login-grupo">
    <div class="wrapper ">
      <div class="col-md-6 col-sm-8 ml-auto mr-auto">
        <div class="card card-signup">
          <form id="formLogin" class="form" method="POST" action="<?php echo BASE_URL; ?>Login/grupoLogin">
            <div class="card-header card-header-info text-center">
              <h4 class="card-title">Escolha o Grupo</h4>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table">
                  <thead class="text-info">
                    <tr>
                      <th align='left'>
                        Grupo
                      </th>
                      <th align='center'>
                        In&iacute;cio
                      </th>
                      <th align='center'>
                        T&eacute;rmino
                      </th>
                      <th align='center'>
                        &nbsp;
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    foreach($vGruposPessoa as $grupoPessoa){
                      $grp_id         = $grupoPessoa["grp_id"] ?? "0";
                      $gru_descricao  = $grupoPessoa["gru_descricao"] ?? "";
                      $gru_dt_inicio  = ($grupoPessoa["gru_dt_inicio"] != "") ? date('d/m/Y', strtotime($grupoPessoa["gru_dt_inicio"])): "";
                      $gru_dt_termino = ($grupoPessoa["gru_dt_termino"] != "") ? date('d/m/Y', strtotime($grupoPessoa["gru_dt_termino"])): "";

                      $link           = BASE_URL . "SisGrupo/postEscolheGrupo/$grp_id";
                      $button         = "
                        <a href='$link' class='btn btn-info pull-right'>
                          Entrar
                          <i class='material-icons'>arrow_forward</i>
                        </a>
                      ";

                      echo "<tr>";
                      echo "  <td align='left'>$gru_descricao</td>";
                      echo "  <td align='center'>$gru_dt_inicio</td>";
                      echo "  <td align='center'>$gru_dt_termino</td>";
                      echo "  <td align='center'>$button</td>";
                      echo "</tr>";
                    }
                    ?>
                  </tbody>
                </table>
              </div>
              <center>
                <a href='<?=BASE_URL?>Login/grupo' class='btn btn-default'>
                  <i class='material-icons'>arrow_back</i>
                  Voltar
                </a>
              </center>
            </div>
          </form>
        </div>
      </div>
    </div>
  </body>
  <script src="<?php echo BASE_URL; ?>template/assets/js/core/jquery.min.js"></script>
  <script>
    $('#formLogin').keydown(function(e) {
      var key = e.which;
      if (key === 13) {
        // As ASCII code for ENTER key is "13"
        $('#formLogin').submit(); // Submit form code
      }
    });

    function fechaAviso(){
      $('div.alert').remove();
    }
  </script>
</html>