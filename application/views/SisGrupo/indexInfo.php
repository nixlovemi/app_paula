<?php
$GrupoTimeline = $GrupoTimeline ?? array();
$arrStaff      = $arrStaff ?? array();
$vGruDescricao = $vGruDescricao ?? "";
$htmlPosts     = $htmlPosts ?? "";
?>

<div class="row">
  <div class="col-md-8">
    <?=$htmlPosts?>
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
              $foto  = ($pessoa["pes_foto"] != "") ? BASE_URL.$pessoa["pes_foto"]: BASE_URL.FOTO_DEFAULT;
              $nome  = $pessoa["pes_nome"] ?? "";
              $url   = BASE_URL . "SisGrupo/indexInfo/$grpId";
              ?>
              <li>
                <div style="margin-right:10px;" class="profile-photo-small pull-left">
                  <a class="text-info" href="<?=$url?>">
                    <img src="<?=$foto?>" alt="Circle Image" class="rounded-circle img-fluid">
                  </a>
                </div>
                <span>
                  <a style="color:#3C4858 !important;" class="text-info" href="<?=$url?>">
                    <?=$nome?>
                  </a>
                </span>
              </li>
              <?php
            }
            ?>
          </ul>
          <?php
        }
        ?>
      </div>
    </div>
  </div>
</div>