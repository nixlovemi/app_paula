<?php
$GrupoTimeline  = $GrupoTimeline ?? array();
$arrStaff       = $arrStaff ?? array();
$ulrStaff       = $urlStaff ?? "SisGrupo/indexInfo";
$urlTdsPosts    = $urlTdsPosts ?? "";
$vGruDescricao  = $vGruDescricao ?? "";
$htmlPosts      = $htmlPosts ?? "";
$mostraNovoPost = $mostraNovoPost ?? false;
$urlNovoPostRed = $urlNovoPostRed ?? BASE_URL . "SisGrupo";

$titulo         = $GrupoTimeline["grt_titulo"] ?? "";
$descricao      = $GrupoTimeline["grt_texto"] ?? "";
$publico        = $GrupoTimeline["grt_publico"] ?? 1;

$strPublico     = ($publico == 1) ? "checked=''": "";
$grpIdLogado    = pegaGrupoPessoaLogadoId();
if(!$grpIdLogado > 0){
  $grpIdLogado  = $vGrpLogado ?? "";
}
?>

<div class="row">
  <div class="col-md-8">
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
                    <label class="bmd-label-floating">Título (opcional)</label>
                    <input maxlength="100" name="titulo" type="text" class="form-control" value="<?= $titulo; ?>" />
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group bmd-form-group has-info" style="">
                    <label class="bmd-label-floating">Escreva algo para o grupo</label>
                    <textarea class="form-control" rows="2" name="descricao"><?= trim($descricao); ?></textarea>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-4">
                  <div class="togglebutton" style="position:relative; top:8px;">
                    <label>
                      <input type="checkbox" <?= $strPublico; ?> name="publico" />
                      <span class="toggle"></span>
                      Público
                    </label>
                  </div>
                  <div class="ripple-container"></div>
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
  <div class="col-md-4">
    <div class="card">
      <div class="card-header card-header-info">
        <h4 class="card-title">Informação</h4>
      </div>
      <div class="card-body" style="padding-bottom:5px;">
        <?php
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
          <?php
        }
        ?>
      </div>
    </div>
  </div>
</div>