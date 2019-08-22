<!--
=========================================================
 Material Dashboard - v2.1.1
=========================================================

 Product Page: https://www.creative-tim.com/product/material-dashboard
 Copyright 2019 Creative Tim (https://www.creative-tim.com)
 Licensed under MIT (https://github.com/creativetimofficial/material-dashboard/blob/master/LICENSE.md)

 Coded by Creative Tim

=========================================================

 The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software. -->

<?php
function checaMenuSelecionado($controller, $menu_selecionado, $CI)
{
  $CONTROLLER_CI   = $CI->router->fetch_class() ?? "";
  $dashSelecionado = ($CONTROLLER_CI == "$controller") && (!$menu_selecionado);

  if($dashSelecionado){
    $strSelecionado  = "active";
    $SELECIONADO_CI  = true;
  } else {
    $strSelecionado  = "";
    $SELECIONADO_CI  = false;
  }

  return array($strSelecionado, $SELECIONADO_CI);
}

$BASE_URL         = base_url();
$ARR_MENU         = $this->session->template_menu ?? array();
$TITULO           = $titulo ?? "";
$ARR_NOTIFICACAO  = $this->session->flashdata('geraNotificacao') ?? array();
$ARR_REC_LISTA_CI = $this->session->recarregaListaCi ?? array();
$USUARIO_LOGADO   = $this->session->usuario_info ?? array();
$GRP_ID           = $this->session->grp_id ?? NULL;

// pega grupo pela pessoa
require_once(APPPATH."/models/TbGrupoPessoa.php");
$retGRP        = pegaGrupoPessoa($GRP_ID);
$GrupoPessoa   = ($retGRP["erro"]) ? array(): $retGRP["GrupoPessoa"];
$gruId         = $GrupoPessoa["grp_gru_id"] ?? "";

$ehAdminLogado = ehAdminGrupo($gruId);
// ======================

// qdo tiver na tela do grupo
if($GRP_ID > 0 && !$ehAdminLogado){
  $URL_HOME       = $BASE_URL . "SisGrupo";
  $URL_LOGOUT     = $BASE_URL . "Login/grupo";
  $URL_CONFIG     = $BASE_URL . "GrpConfig";
  $IMG_SIDEBAR    = "sidebar-3.jpg";
  $EH_GRUPO       = true;
} else {
  $URL_HOME       = ($USUARIO_LOGADO->admin == 1) ? $BASE_URL . "Dashboard": $BASE_URL . "Sistema";
  $URL_LOGOUT     = ($USUARIO_LOGADO->admin == 1) ? $BASE_URL: $BASE_URL . "Login/sistema";
  $URL_CONFIG     = "UsuConfig";
  $IMG_SIDEBAR    = ($USUARIO_LOGADO->admin == 1) ? "sidebar-1.jpg": "sidebar-3.jpg";
  $EH_GRUPO       = false;
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="utf-8" />
  <link rel="apple-touch-icon" sizes="76x76" href="<?php echo $BASE_URL; ?>template/assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="<?php echo $BASE_URL; ?>template/assets/img/favicon.png">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <title>
    VC+LEVE | Sistema
  </title>
  <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
  <!--     Fonts and icons     -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">
  <!-- CSS Files -->
  <link href="<?php echo $BASE_URL; ?>template/assets/css/material-dashboard.css?v=2.1.1" rel="stylesheet" />
  <link href="<?php echo $BASE_URL; ?>assets/FcbkGrid/images-grid.css" rel="stylesheet" />
  <link href="<?php echo $BASE_URL; ?>assets/VideoAudioPlayer/css/stylised.css" rel="stylesheet" />
  <link href="<?php echo $BASE_URL; ?>assets/Croppie/croppie.css" rel="stylesheet" />
  <!--<link href="https://vjs.zencdn.net/7.6.0/video-js.css" rel="stylesheet">-->
  <!-- CSS Just for demo purpose, don't include it in your project -->
  <link href="<?php echo $BASE_URL; ?>template/assets/demo/demo.css" rel="stylesheet" />

  <script src="<?php echo $BASE_URL; ?>template/assets/js/core/jquery.min.js"></script>
  <script src="<?php echo $BASE_URL; ?>assets/videojs-ie8.min.js"></script>
  <!--<script src="https://vjs.zencdn.net/ie8/1.1.2/videojs-ie8.min.js"></script>-->
</head>

<body class="" data-base-url="<?php echo $BASE_URL; ?>">
  <?php
  if(isset($ARR_NOTIFICACAO["titulo"]) && isset($ARR_NOTIFICACAO["mensagem"]) && isset($ARR_NOTIFICACAO["tipo"])){
    if($ARR_NOTIFICACAO["titulo"] != "" && $ARR_NOTIFICACAO["mensagem"] != "" && $ARR_NOTIFICACAO["tipo"] != ""){
      echo "<div style='display:none;' id='dvMostraNotificacao' data-titulo='".$ARR_NOTIFICACAO["titulo"]."' data-tipo='".$ARR_NOTIFICACAO["tipo"]."'>";
      echo "" . $ARR_NOTIFICACAO["mensagem"];
      echo "</div>";
    }
  }

  foreach($ARR_REC_LISTA_CI as $idListaCi => $jsonListaCi){
    echo "<input type='hidden' class='inptHddnRecListaCi' id='inptHddnRecListaCi__$idListaCi' value='$jsonListaCi'  />";
  }
  ?>

  <div class="wrapper">
    <div class="sidebar" data-color="azure" data-background-color="white" data-image="<?php echo $BASE_URL; ?>template/assets/img/<?php echo $IMG_SIDEBAR; ?>">
      <!--
        Tip 1: You can change the color of the sidebar using: data-color="purple | azure | green | orange | danger"

        Tip 2: you can also add an image using data-image tag
      -->
      <div class="logo">
        <a href="http://www.creative-tim.com" class="simple-text logo-normal">
          VC+ LEVE
        </a>
      </div>
    
      <div class="sidebar-wrapper">
        <ul class="nav">
          <?php
          list($strSelecionado, $MENU_SELEC) = checaMenuSelecionado("Dashboard", false, $this);
          ?>
          <li class="nav-item <?php echo $strSelecionado; ?>">
            <a class="nav-link" href="<?php echo $URL_HOME ?>">
              <i class="material-icons">dashboard</i>
              <p>Área Inicial</p>
            </a>
          </li>

          <?php
          foreach($ARR_MENU as $menuItem){
            $descricao  = $menuItem["descricao"];
            $icone      = $menuItem["icone"];
            $controller = $menuItem["controller"];
            $action     = $menuItem["action"];

            $urlLink    = $BASE_URL . "$controller/$action";
            list($strSelecionado, $MENU_SELEC) = checaMenuSelecionado($controller, $MENU_SELEC, $this);
            ?>
            <li class="nav-item <?php echo $strSelecionado; ?>">
              <a class="nav-link" href="<?php echo $urlLink; ?>">
                <?php echo $icone; ?>
                <p><?php echo $descricao; ?></p>
              </a>
            </li>
            <?php
          }
          ?>
        </ul>
      </div>
    </div>
    <div class="main-panel">
      <!-- Navbar -->
      <nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top ">
        <div class="container-fluid">
          <div class="navbar-wrapper">
            <?php /*<a class="navbar-brand" href="#pablo">Dashboard</a>*/ ?>
            <?php echo $TITULO; ?>
          </div>
          <button class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
            <span class="sr-only">Toggle navigation</span>
            <span class="navbar-toggler-icon icon-bar"></span>
            <span class="navbar-toggler-icon icon-bar"></span>
            <span class="navbar-toggler-icon icon-bar"></span>
          </button>
          <div class="collapse navbar-collapse justify-content-end">
            <form class="navbar-form">
              <?php
              /*
              <div class="input-group no-border">
                <input type="text" value="" class="form-control" placeholder="Pesquisar...">
                <button type="submit" class="btn btn-white btn-round btn-just-icon">
                  <i class="material-icons">search</i>
                  <div class="ripple-container"></div>
                </button>
              </div>
              */
              ?>
            </form>

            <ul class="navbar-nav">
              <?php
              /*
              <li class="nav-item">
                <a class="nav-link" href="#pablo">
                  <i class="material-icons">dashboard</i>
                  <p class="d-lg-none d-md-block">
                    Stats
                  </p>
                </a>
              </li>
              */
              ?>
              <li class="nav-item dropdown">
                <a class="nav-link" href="http://example.com" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="material-icons">notifications</i>
                  <span class="notification">5</span>
                  <p class="d-lg-none d-md-block">
                    Alertas
                  </p>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                  <a class="dropdown-item dropdown-item-info" href="#">Mike John responded to your email</a>
                  <a class="dropdown-item dropdown-item-info" href="#">You have 5 new tasks</a>
                  <a class="dropdown-item dropdown-item-info" href="#">You're now friend with Andrew</a>
                  <a class="dropdown-item dropdown-item-info" href="#">Another Notification</a>
                  <a class="dropdown-item dropdown-item-info" href="#">Another One</a>
                </div>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link" href="#pablo" id="navbarDropdownProfile" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="material-icons">person</i>
                  <p class="d-lg-none d-md-block">
                    Perfil
                  </p>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownProfile">
                  <?php #<a class="dropdown-item dropdown-item-info" href="#">Perfil</a> ?>
                  <a class="dropdown-item dropdown-item-info" href="<?=$URL_CONFIG?>">Configurações</a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item dropdown-item-info" href="<?php echo $URL_LOGOUT; ?>">Sair</a>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </nav>
      <!-- End Navbar -->
      <div class="content">
        <div class="container-fluid">
          <?= $contents ?>
        </div>
      </div>
      <footer class="footer">
        <div class="container-fluid">
        </div>
      </footer>
    </div>
  </div>
  <div>

  </div>
  <!--   Core JS Files   -->
  <script src="<?php echo $BASE_URL; ?>template/assets/js/core/popper.min.js"></script>
  <script src="<?php echo $BASE_URL; ?>template/assets/js/core/bootstrap-material-design.min.js"></script>
  <script src="<?php echo $BASE_URL; ?>template/assets/js/plugins/perfect-scrollbar.jquery.min.js"></script>
  <!-- Plugin for the momentJs  -->
  <script src="<?php echo $BASE_URL; ?>template/assets/js/plugins/moment.min.js"></script>
  <!--  Plugin for Sweet Alert -->
  <script src="<?php echo $BASE_URL; ?>template/assets/js/plugins/sweetalert2.js"></script>
  <!-- Forms Validations Plugin -->
  <script src="<?php echo $BASE_URL; ?>template/assets/js/plugins/jquery.validate.min.js"></script>
  <!-- Plugin for the Wizard, full documentation here: https://github.com/VinceG/twitter-bootstrap-wizard -->
  <script src="<?php echo $BASE_URL; ?>template/assets/js/plugins/jquery.bootstrap-wizard.js"></script>
  <!--	Plugin for Select, full documentation here: http://silviomoreto.github.io/bootstrap-select -->
  <script src="<?php echo $BASE_URL; ?>template/assets/js/plugins/bootstrap-selectpicker.js"></script>
  <!--  Plugin for the DateTimePicker, full documentation here: https://eonasdan.github.io/bootstrap-datetimepicker/ -->
  <script src="<?php echo $BASE_URL; ?>template/assets/js/plugins/bootstrap-datetimepicker.min.js"></script>
  <!--  DataTables.net Plugin, full documentation here: https://datatables.net/  -->
  <script src="<?php echo $BASE_URL; ?>template/assets/js/plugins/jquery.dataTables.min.js"></script>
  <!--	Plugin for Tags, full documentation here: https://github.com/bootstrap-tagsinput/bootstrap-tagsinputs  -->
  <script src="<?php echo $BASE_URL; ?>template/assets/js/plugins/bootstrap-tagsinput.js"></script>
  <!-- Plugin for Fileupload, full documentation here: http://www.jasny.net/bootstrap/javascript/#fileinput -->
  <script src="<?php echo $BASE_URL; ?>template/assets/js/plugins/jasny-bootstrap.min.js"></script>
  <!--  Full Calendar Plugin, full documentation here: https://github.com/fullcalendar/fullcalendar    -->
  <script src="<?php echo $BASE_URL; ?>template/assets/js/plugins/fullcalendar.min.js"></script>
  <!-- Vector Map plugin, full documentation here: http://jvectormap.com/documentation/ -->
  <script src="<?php echo $BASE_URL; ?>template/assets/js/plugins/jquery-jvectormap.js"></script>
  <!--  Plugin for the Sliders, full documentation here: http://refreshless.com/nouislider/ -->
  <script src="<?php echo $BASE_URL; ?>template/assets/js/plugins/nouislider.min.js"></script>
  <!-- Include a polyfill for ES6 Promises (optional) for IE11, UC Browser and Android browser support SweetAlert -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/core-js/2.4.1/core.js"></script>
  <!-- Library for adding dinamically elements -->
  <script src="<?php echo $BASE_URL; ?>template/assets/js/plugins/arrive.min.js"></script>
  <!--  Google Maps Plugin    -->
  <!-- <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE"></script> -->
  <!-- Chartist JS -->
  <script src="<?php echo $BASE_URL; ?>template/assets/js/plugins/chartist.min.js"></script>
  <!--  Notifications Plugin    -->
  <script src="<?php echo $BASE_URL; ?>template/assets/js/plugins/bootstrap-notify.js"></script>
  <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="<?php echo $BASE_URL; ?>template/assets/js/material-dashboard.js?v=2.1.1" type="text/javascript"></script>
  <script src="<?php echo $BASE_URL; ?>assets/FcbkGrid/images-grid.js" type="text/javascript"></script>
  <script src="<?php echo $BASE_URL; ?>assets/VideoAudioPlayer/mediastyler.js" type="text/javascript"></script>
  <script src="<?php echo $BASE_URL; ?>assets/Croppie/croppie.min.js" type="text/javascript"></script>
  <!--<script src='https://vjs.zencdn.net/7.6.0/video.js'></script>-->
  <!-- Material Dashboard DEMO methods, don't include it in your project! -->
  <script>
    // jquery mask ==========
    !function(e){"function"==typeof define&&define.amd?define(["jquery"],e):"object"==typeof exports?e(require_once("jquery")):e(jQuery)}(function(e){var t,n=navigator.userAgent,a=/iphone/i.test(n),i=/chrome/i.test(n),r=/android/i.test(n);e.mask={definitions:{9:"[0-9]",a:"[A-Za-z]","*":"[A-Za-z0-9]"},autoclear:!0,dataName:"rawMaskFn",placeholder:"_"},e.fn.extend({caret:function(e,t){var n;if(0!==this.length&&!this.is(":hidden"))return"number"==typeof e?(t="number"==typeof t?t:e,this.each(function(){this.setSelectionRange?this.setSelectionRange(e,t):this.createTextRange&&((n=this.createTextRange()).collapse(!0),n.moveEnd("character",t),n.moveStart("character",e),n.select())})):(this[0].setSelectionRange?(e=this[0].selectionStart,t=this[0].selectionEnd):document.selection&&document.selection.createRange&&(n=document.selection.createRange(),e=0-n.duplicate().moveStart("character",-1e5),t=e+n.text.length),{begin:e,end:t})},unmask:function(){return this.trigger("unmask")},mask:function(n,o){var c,l,u,f,s,h,g;if(!n&&this.length>0){var m=e(this[0]).data(e.mask.dataName);return m?m():void 0}return o=e.extend({autoclear:e.mask.autoclear,placeholder:e.mask.placeholder,completed:null},o),c=e.mask.definitions,l=[],u=h=n.length,f=null,e.each(n.split(""),function(e,t){"?"==t?(h--,u=e):c[t]?(l.push(new RegExp(c[t])),null===f&&(f=l.length-1),e<u&&(s=l.length-1)):l.push(null)}),this.trigger("unmask").each(function(){var m=e(this),d=e.map(n.split(""),function(e,t){if("?"!=e)return c[e]?k(t):e}),p=d.join(""),v=m.val();function b(){if(o.completed){for(var e=f;e<=s;e++)if(l[e]&&d[e]===k(e))return;o.completed.call(m)}}function k(e){return e<o.placeholder.length?o.placeholder.charAt(e):o.placeholder.charAt(0)}function y(e){for(;++e<h&&!l[e];);return e}function x(e,t){var n,a;if(!(e<0)){for(n=e,a=y(t);n<h;n++)if(l[n]){if(!(a<h&&l[n].test(d[a])))break;d[n]=d[a],d[a]=k(a),a=y(a)}A(),m.caret(Math.max(f,e))}}function j(e){S(),m.val()!=v&&m.change()}function R(e,t){var n;for(n=e;n<t&&n<h;n++)l[n]&&(d[n]=k(n))}function A(){m.val(d.join(""))}function S(e){var t,n,a,i=m.val(),r=-1;for(t=0,a=0;t<h;t++)if(l[t]){for(d[t]=k(t);a++<i.length;)if(n=i.charAt(a-1),l[t].test(n)){d[t]=n,r=t;break}if(a>i.length){R(t+1,h);break}}else d[t]===i.charAt(a)&&a++,t<u&&(r=t);return e?A():r+1<u?o.autoclear||d.join("")===p?(m.val()&&m.val(""),R(0,h)):A():(A(),m.val(m.val().substring(0,r+1))),u?t:f}m.data(e.mask.dataName,function(){return e.map(d,function(e,t){return l[t]&&e!=k(t)?e:null}).join("")}),m.one("unmask",function(){m.off(".mask").removeData(e.mask.dataName)}).on("focus.mask",function(){var e;m.prop("readonly")||(clearTimeout(t),v=m.val(),e=S(),t=setTimeout(function(){m.get(0)===document.activeElement&&(A(),e==n.replace("?","").length?m.caret(0,e):m.caret(e))},10))}).on("blur.mask",j).on("keydown.mask",function(e){if(!m.prop("readonly")){var t,n,i,r=e.which||e.keyCode;g=m.val(),8===r||46===r||a&&127===r?(n=(t=m.caret()).begin,(i=t.end)-n==0&&(n=46!==r?function(e){for(;--e>=0&&!l[e];);return e}(n):i=y(n-1),i=46===r?y(i):i),R(n,i),x(n,i-1),e.preventDefault()):13===r?j.call(this,e):27===r&&(m.val(v),m.caret(0,S()),e.preventDefault())}}).on("keypress.mask",function(t){if(!m.prop("readonly")){var n,a,i,o=t.which||t.keyCode,c=m.caret();t.ctrlKey||t.altKey||t.metaKey||o<32||!o||13===o||(c.end-c.begin!=0&&(R(c.begin,c.end),x(c.begin,c.end-1)),(n=y(c.begin-1))<h&&(a=String.fromCharCode(o),l[n].test(a))&&(function(e){var t,n,a,i;for(t=e,n=k(e);t<h;t++)if(l[t]){if(a=y(t),i=d[t],d[t]=n,!(a<h&&l[a].test(i)))break;n=i}}(n),d[n]=a,A(),i=y(n),r?setTimeout(function(){e.proxy(e.fn.caret,m,i)()},0):m.caret(i),c.begin<=s&&b()),t.preventDefault())}}).on("input.mask paste.mask",function(){m.prop("readonly")||setTimeout(function(){var e=S(!0);m.caret(e),b()},0)}),i&&r&&m.off("input.mask").on("input.mask",function(e){var t=m.val(),n=m.caret();if(g&&g.length&&g.length>t.length){for(S(!0);n.begin>0&&!l[n.begin-1];)n.begin--;if(0===n.begin)for(;n.begin<f&&!l[n.begin];)n.begin++;m.caret(n.begin,n.begin)}else{for(S(!0);n.begin<h&&!l[n.begin];)n.begin++;m.caret(n.begin,n.begin)}b()}),S()})}})});
    // ======================
  </script>
  <script src="<?php echo $BASE_URL; ?>template/assets/demo/demo.js"></script>
  <!-- Lista CI -->
  <script src="<?php echo $BASE_URL; ?>assets/Lista_CI/Lista_CI.js"></script>
  <script>
    $(document).ready(function() {
      $().ready(function() {

        $sidebar = $('.sidebar');

        $sidebar_img_container = $sidebar.find('.sidebar-background');

        $full_page = $('.full-page');

        $sidebar_responsive = $('body > .navbar-collapse');

        window_width = $(window).width();

        fixed_plugin_open = $('.sidebar .sidebar-wrapper .nav li.active a p').html();

        if (window_width > 767 && fixed_plugin_open == 'Dashboard') {
          if ($('.fixed-plugin .dropdown').hasClass('show-dropdown')) {
            $('.fixed-plugin .dropdown').addClass('open');
          }

        }

        $('.fixed-plugin a').click(function(event) {
          // Alex if we click on switch, stop propagation of the event, so the dropdown will not be hide, otherwise we set the  section active
          if ($(this).hasClass('switch-trigger')) {
            if (event.stopPropagation) {
              event.stopPropagation();
            } else if (window.event) {
              window.event.cancelBubble = true;
            }
          }
        });

        $('.fixed-plugin .active-color span').click(function() {
          $full_page_background = $('.full-page-background');

          $(this).siblings().removeClass('active');
          $(this).addClass('active');

          var new_color = $(this).data('color');

          if ($sidebar.length != 0) {
            $sidebar.attr('data-color', new_color);
          }

          if ($full_page.length != 0) {
            $full_page.attr('filter-color', new_color);
          }

          if ($sidebar_responsive.length != 0) {
            $sidebar_responsive.attr('data-color', new_color);
          }
        });

        $('.fixed-plugin .background-color .badge').click(function() {
          $(this).siblings().removeClass('active');
          $(this).addClass('active');

          var new_color = $(this).data('background-color');

          if ($sidebar.length != 0) {
            $sidebar.attr('data-background-color', new_color);
          }
        });

        $('.fixed-plugin .img-holder').click(function() {
          $full_page_background = $('.full-page-background');

          $(this).parent('li').siblings().removeClass('active');
          $(this).parent('li').addClass('active');


          var new_image = $(this).find("img").attr('src');

          if ($sidebar_img_container.length != 0 && $('.switch-sidebar-image input:checked').length != 0) {
            $sidebar_img_container.fadeOut('fast', function() {
              $sidebar_img_container.css('background-image', 'url("' + new_image + '")');
              $sidebar_img_container.fadeIn('fast');
            });
          }

          if ($full_page_background.length != 0 && $('.switch-sidebar-image input:checked').length != 0) {
            var new_image_full_page = $('.fixed-plugin li.active .img-holder').find('img').data('src');

            $full_page_background.fadeOut('fast', function() {
              $full_page_background.css('background-image', 'url("' + new_image_full_page + '")');
              $full_page_background.fadeIn('fast');
            });
          }

          if ($('.switch-sidebar-image input:checked').length == 0) {
            var new_image = $('.fixed-plugin li.active .img-holder').find("img").attr('src');
            var new_image_full_page = $('.fixed-plugin li.active .img-holder').find('img').data('src');

            $sidebar_img_container.css('background-image', 'url("' + new_image + '")');
            $full_page_background.css('background-image', 'url("' + new_image_full_page + '")');
          }

          if ($sidebar_responsive.length != 0) {
            $sidebar_responsive.css('background-image', 'url("' + new_image + '")');
          }
        });

        $('.switch-sidebar-image input').change(function() {
          $full_page_background = $('.full-page-background');

          $input = $(this);

          if ($input.is(':checked')) {
            if ($sidebar_img_container.length != 0) {
              $sidebar_img_container.fadeIn('fast');
              $sidebar.attr('data-image', '#');
            }

            if ($full_page_background.length != 0) {
              $full_page_background.fadeIn('fast');
              $full_page.attr('data-image', '#');
            }

            background_image = true;
          } else {
            if ($sidebar_img_container.length != 0) {
              $sidebar.removeAttr('data-image');
              $sidebar_img_container.fadeOut('fast');
            }

            if ($full_page_background.length != 0) {
              $full_page.removeAttr('data-image', '#');
              $full_page_background.fadeOut('fast');
            }

            background_image = false;
          }
        });

        $('.switch-sidebar-mini input').change(function() {
          $body = $('body');

          $input = $(this);

          if (md.misc.sidebar_mini_active == true) {
            $('body').removeClass('sidebar-mini');
            md.misc.sidebar_mini_active = false;

            $('.sidebar .sidebar-wrapper, .main-panel').perfectScrollbar();

          } else {

            $('.sidebar .sidebar-wrapper, .main-panel').perfectScrollbar('destroy');

            setTimeout(function() {
              $('body').addClass('sidebar-mini');

              md.misc.sidebar_mini_active = true;
            }, 300);
          }

          // we simulate the window Resize so the charts will get updated in realtime.
          var simulateWindowResize = setInterval(function() {
            window.dispatchEvent(new Event('resize'));
          }, 180);

          // we stop the simulation of Window Resize after the animations are completed
          setTimeout(function() {
            clearInterval(simulateWindowResize);
          }, 1000);

        });
      });
    });
  </script>
  <script>
    $(document).ready(function() {
      // Javascript method's body can be found in assets/js/demos.js
      md.initDashboardPageCharts();

    });
  </script>
</body>

</html>
