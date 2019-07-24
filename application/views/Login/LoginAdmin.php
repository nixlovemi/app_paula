<?php
$vLoginMsg = $vLoginMsg ?? "";
?>

<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8" />
    <link rel="apple-touch-icon" sizes="76x76" href="<?php echo BASE_URL; ?>template/assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>template/assets/img/favicon.png">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>
      Entrar
    </title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <!--     Fonts and icons     -->
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">
    <!-- CSS Files -->
    <link href="<?php echo BASE_URL; ?>template/assets/css/material-dashboard.css?v=2.1.1" rel="stylesheet" />
    <link href="<?php echo BASE_URL; ?>template/assets/demo/demo.css" rel="stylesheet" />
  </head>

  <body class="bg-login">
    <div class="wrapper ">
      <div class="col-md-4 col-sm-6 ml-auto mr-auto">
        <div class="card card-signup">
          <form id="formLogin" class="form" method="POST" action="<?php echo BASE_URL; ?>Login/executaLogin">
            <div class="card-header card-header-success text-center">
              <h4 class="card-title">Entrar</h4>
            </div>
            <div class="card-body">
              <span class="bmd-form-group">
                <div class="input-group has-success">
                  <div class="input-group-prepend">
                    <span class="input-group-text">
                      <i class="material-icons">mail</i>
                    </span>
                  </div>
                  <input name="usuario" type="email" class="form-control" placeholder="Email..." value="<?php echo $vUsuario; ?>" />
                </div>
              </span>
              <span class="bmd-form-group">
                <div class="input-group has-success">
                  <div class="input-group-prepend">
                    <span class="input-group-text">
                      <i class="material-icons">lock_outline</i>
                    </span>
                  </div>
                  <input name="senha" type="password" class="form-control" placeholder="Senha..." />
                </div>
              </span>
              <?php
              if($vLoginMsg != ""){
                ?>
                <div class="alert alert-warning">
                  <button onclick="fechaAviso()" type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <i class="material-icons">close</i>
                  </button>
                  <span>
                    <?php echo $vLoginMsg; ?>
                  </span>
                </div>
                <?php
              }
              ?>
            </div>
            <div class="footer text-center">
              <a href="javascript:;" onClick="$('#formLogin').submit();" class="btn btn-success align-center">
                Entrar
                <div class="ripple-container"></div>
              </a>
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