<?php
$GrupoTimeline     = $GrupoTimeline ?? array();
$arrStaff          = $arrStaff ?? array();
$ulrStaff          = $urlStaff ?? "SisGrupo/indexInfo";
$urlTdsPosts       = $urlTdsPosts ?? "";
$urlPostsFavoritos = $urlPostsFavoritos ?? "";
$urlMeusPosts      = $urlMeusPosts ?? "";
$urlProgramados    = $urlProgramados ?? "";
$urlPrivada        = $urlPrivada ?? "";
$vGruDescricao     = $vGruDescricao ?? "";
$htmlPosts         = $htmlPosts ?? "";
$mostraNovoPost    = $mostraNovoPost ?? false;
$urlNovoPostRed    = $urlNovoPostRed ?? BASE_URL . "SisGrupo";
$retProgresso      = $retProgresso ?? array();

$descricao      = $GrupoTimeline["grt_texto"] ?? "";
$publico        = $GrupoTimeline["grt_publico"] ?? 1;
$programado     = $GrupoTimeline["grt_dt_programado"] ?? "";

$strPublico     = ($publico == 1) ? "checked=''": "";
$grpIdLogado    = pegaGrupoPessoaLogadoId();
if(!$grpIdLogado > 0){
  $grpIdLogado  = $vGrpLogado ?? "";
}
$strProgramado  = ($programado <> "") ? date("d/m/Y H:i", strtotime($programado)): "";

$ehStaff =  false;
foreach($arrStaff as $staff){
  if($staff["grp_id"] == $grpIdLogado){
    $ehStaff = true;
    break;
  }
}
?>

<div class="row" id="dv-sisgrupo">
  <div class="col-md-8" id="dv-sisgrupo-postagens">
    <?php
    if($mostraNovoPost){
      ?>
      <div class="card">
        <div class="card-header card-header-info">
          <h4 class="card-title">Nova Postagem</h4>
        </div>
        <div class="card-body" style="padding-bottom:5px;">
          <form id="frmNovaPostagem" action="<?php echo base_url() ?>Json/postNovoTimelineGrupo" method="post" enctype="multipart/form-data">
            <div class="" style="margin-bottom:0; padding-bottom:0;">
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group bmd-form-group has-info" style="">
                    <label class="bmd-label-floating">Escreva algo para o grupo</label>
                    <textarea class="form-control" rows="2" name="descricao"><?= trim($descricao); ?></textarea>
                  </div>
                </div>
              </div>
              <?php
              if($ehStaff){
                ?>
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group bmd-form-group has-info" style="">
                      <label class="label-control bmd-label-static text-default">Programar</label>
                      <input name="programar" type="text" class="form-control datepicker_time" value="<?=$strProgramado?>" />
                    </div>
                  </div>
                </div>
                <?php
              }
              ?>

              <div class="row">
                <div class="col-md-4">
                  <?php
                  if($ehStaff){
                    ?>
                    <input type="hidden" name="publico" value="on" />
                    <?php
                  } else {
                    ?>
                    <div class="togglebutton" style="position:relative; top:8px;">
                      <label>
                        <input type="checkbox" <?= $strPublico; ?> name="publico" />
                        <span class="toggle"></span>
                        Público
                      </label>
                    </div>
                    <div class="ripple-container"></div>
                    <?php
                  }
                  ?>
                </div>
                <div class="col-md-8">
                  <button type="button" onclick="$('#frmNovaPostagem').submit()" class="btn btn-info btn-sm pull-right">Publicar</button>
                  <a href="javascript:;" data-toggle="dropdown" aria-expanded="false" class="btn btn-default btn-sm pull-right dropdown-toggle">
                    <i class="material-icons">attach_file</i>
                    Anexar
                  </a>
                  <div class="dropdown-menu">
                    <a href="javascript:;" class="dropdown-item dropdown-item-info" onclick="fncAnexarArqPostagem()">Do Computador</a>
                    <a href="javascript:;" class="dropdown-item dropdown-item-info" onclick="fncAnexarArqPostagemYt()">Youtube</a>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group bmd-form-group has-info" style="">
                    <div id="lista-anexos" style="border-top: solid 1px #CCC; width: 100%; padding-top:8px;">
                  </div>
                  </div>
                </div>
              </div>
            </div>
            <input type="hidden" name="urlNovoPostRed" value="<?=$urlNovoPostRed?>" />
            <input type="hidden" name="grpIdLogado" value="<?=$grpIdLogado?>" />
          </form>
        </div>
      </div>
      <?php
    }

    echo $htmlPosts;
    ?>
  </div>
  <div class="col-md-4" id="dv-sisgrupo-info">
    <div class="card">
      <div class="card-header card-header-info">
        <h4 class="card-title">Informação</h4>
      </div>
      <div class="card-body" style="padding-bottom:5px;">
        <?php
        $progresso = $retProgresso["progresso"] ?? NULL;
        $difAtual  = $retProgresso["dif_atual"] ?? 0;
        if($progresso != NULL){
          echo "<p class='text-info'>Você está com ".number_format($progresso, 3, ",", ".")."% do objetivo concluído. Faltam apenas ".number_format($difAtual, 3, ",", ".")."KG.</p>";
        }

        $arrLoopStaff = [];
        $arrLoopStaff["dono"]  = [];
        $arrLoopStaff["staff"] = [];

        foreach($arrStaff as $pessoa){
          if($pessoa["grp_usu_id"] != 0){
            $arrLoopStaff["dono"][] = $pessoa;
          } else {
            $arrLoopStaff["staff"][] = $pessoa;
          }
        }

        if(count($arrLoopStaff) > 0){
          ?>
          <ul class="informacao-grupo-side">
            <?php
            $arrLoop = array_merge($arrLoopStaff["dono"], $arrLoopStaff["staff"]);

            foreach($arrLoop as $pessoa){
              $grpId = $pessoa["grp_id"] ?? "";
              $foto  = $pessoa["pes_foto"] ?? FOTO_DEFAULT;
              $nome  = $pessoa["pes_nome"] ?? "";
              $url   = "$ulrStaff/$grpId";
              ?>
              <li>
                <div style="margin-right:10px;" class="profile-photo-small pull-left">
                  <a class="text-info" href="<?=base_url() . $url?>">
                    <img src="<?=base_url() . $foto?>" alt="Circle Image" class="rounded-circle img-fluid">
                  </a>
                </div>
                <span>
                  <a style="color:#3C4858 !important;" class="text-info" href="<?=base_url() . $url?>">
                    <span data-id="<?=$grpId?>" id="grupo-staff-notificacao-<?=$grpId?>" class="notification grupo-staff-notificacao"></span>
                    <?=$nome?>
                  </a>
                </span>
              </li>
              <?php
            }
            ?>
          </ul>
          <a style="width:100%;" class="btn" href="<?=base_url() . $urlTdsPosts?>">
            Todas postagens do grupo
          </a>
          <a style="width:100%;" class="btn" href="<?=base_url() . $urlMeusPosts?>">
            Minhas Postagens
          </a>
          <?php
          if($ehStaff){
            ?>
            <a style="width:100%;" class="btn" href="<?=base_url() . $urlProgramados?>">
              <i class="material-icons">alarm</i>
              &nbsp;
              Postagens Programadas
            </a>
            <a style="width:100%;" class="btn" href="<?=base_url() . $urlPrivada?>">
              <i class="material-icons">visibility_off</i>
              &nbsp;
              Postagens Privadas
            </a>
            <?php
          }
          ?>
          <a style="width:100%;" class="btn" href="<?=base_url() . $urlPostsFavoritos?>">
            <i class="material-icons">favorite</i>
            &nbsp;
            Postagens Favoritas
          </a>
          <?php
        }
        ?>
      </div>
    </div>
  </div>
</div>