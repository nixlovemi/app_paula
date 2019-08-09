<?php
$GrupoTimeline = $GrupoTimeline ?? array();
$arrPostagens  = $arrPostagens ?? array();
$arrSalvos     = $arrSalvos ?? array();
$arrArquivos   = $arrArquivos ?? array();
$vGruDescricao = $vGruDescricao ?? "";

$titulo        = $GrupoTimeline["grt_titulo"] ?? "";
$descricao     = $GrupoTimeline["grt_texto"] ?? "";
$publico       = $GrupoTimeline["grt_publico"] ?? 1;

$strPublico    = ($publico == 1) ? "checked=''": "";

/*
 *[grt_id] => 2
  [grt_gru_id] => 8
  [grt_grp_id] => 1
  [grt_data] => 2019-08-02 15:15:53
  [grt_titulo] => Primeiro Post!
  [grt_texto] => Bacon ipsum dolor amet burgdoggen brisket tongue beef rump. Venison ribeye flank picanha boudin ham hock shoulder. Tri-tip sausage turkey corned beef salami, ham hock alcatra cow t-bone.
  [grt_publico] => 1
  [grt_ativo] => 1
  [grt_resposta_id] =>
  [gru_id] => 8
  [gru_dt_inicio] => 2019-07-29 00:00:00
  [gru_dt_termino] => 2019-08-29 00:00:00
  [gru_ativo] => 1
  [grupo_ativo] => Sim
  [usu_nome] => Carla Cecília Lourenço Parra
  [str_dt_inicio] =>
  [str_dt_termino] =>
  [grp_id] => 1
  [grp_gru_id] => 8
  [grp_pes_id] => 3
  [grp_ativo] => 1
  [grupo_pessoa_ativo] => Sim
  [pes_nome] => Leandro 2
  [pes_email] => nixlovemi2@gmail.com
  [pes_foto] => template/assets/img/pessoas/leandro-parra.jpg
  [pet_descricao] => Cliente
  [pet_cliente] => 1
 */
?>

<div class="row">
  <div class="col-md-9">
    <div class="card">
      <div class="card-header card-header-info">
        <h4 class="card-title">Nova Postagem</h4>
      </div>
      <div class="card-body" style="padding-bottom:5px;">
        <form id="frmNovaPostagem" action="<?php echo base_url() ?>GrupoTimeline/postNovo" method="post" enctype="multipart/form-data">
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
        </form>
      </div>
    </div>

    <h4>Postagens - <?= $vGruDescricao ?></h4>

    <div class="row">
      <div class="col-md-12">
        <?php
        foreach($arrPostagens as $postagem){
          $id       = $postagem["grt_id"] ?? "";
          $pesId    = $postagem["grp_pes_id"] ?? "";
          $titulo   = $postagem["grt_titulo"] ?? "";
          $pessoa   = $postagem["pes_nome"] ?? "";
          $data     = $postagem["grt_data"] ?? "";
          $texto    = $postagem["grt_texto"] ?? "";
          $foto     = $postagem["pes_foto"] ?? FOTO_DEFAULT;

          $idUsuLogado       = pegaUsuarioLogadoId() ?? 0;
          $grpPessoaLogado   = pegaGrupoPessoaLogadoId() ?? -1;
          $ehPostagemPropria = ($idUsuLogado == $pesId);
          $strData           = ($data != "") ? date("d/m H:m", strtotime($data)): "";
          $strDataF          = ($data != "") ? date("d/m/Y H:m:i", strtotime($data)): "";
          $strTexto          = nl2br($texto);
          $ehFavoritado      = isset($arrSalvos[$id][$grpPessoaLogado]);
          $strFavoritado     = ($ehFavoritado) ? "display:block;": "display:none;";

          // anexos
          $arquivosPost    = $arrArquivos[$id] ?? array();
          $arquivosPostImg = $arquivosPost["imagens"] ?? array();
          $arquivosPostAud = $arquivosPost["audio"] ?? array();
          $arquivosPostVid = $arquivosPost["video"] ?? array();
          $arquivosPostDiv = $arquivosPost["documentos"] ?? array();
          // ======
          ?>
          <div class="row item-postagem" id="item-postagem-<?= $id ?>">
            <div class="col-md-12">
              <div class="postagem-inner postagem-inner-top">
                <div style="margin-right:10px;" class="profile-photo-small pull-left">
                  <img src="<?= base_url() . $foto ?>" alt="Circle Image" class="rounded-circle img-fluid" />
                </div>
                <span class="titulo">
                  <?=$titulo?>
                </span>
                <br />
                <span title="<?=$strDataF?>" class="autor"><?=$pessoa?> | <?=$strData?></span>

                <ul class="ul-top-sis-grupo-postagem navbar-nav pull-right">
                  <li class="dropdown nav-item">
                    <a href="javascript:;" class="dropdown-toggle nav-link text-info" data-toggle="dropdown" aria-expanded="false">
                      <i class="material-icons">more_horiz</i>
                    </a>
                    <div class="dropdown-menu">
                      <h6 class="dropdown-header">Ações</h6>
                      <?php
                      if($ehPostagemPropria){
                        ?>
                        <a href="javascript:;" onclick="deletarPostagem(<?= $id ?>)" class="dropdown-item dropdown-item-info">Excluir</a>
                        <?php
                      } else {
                        ?>
                        <a href="javascript:;" onclick="favoritarPostagem(<?= $id ?>)" class="dropdown-item dropdown-item-info">Salvar Favorito</a>
                        <?php
                      }

                      /*
                      <a href="javascript:;" class="dropdown-item">Something else here</a>
                      <div class="dropdown-divider"></div>
                      <a href="javascript:;" class="dropdown-item">Separated link</a>
                      <div class="dropdown-divider"></div>
                      <a href="javascript:;" class="dropdown-item">One more separated link</a>
                      */
                      ?>
                    </div>
                  </li>
                  <li class="nav-item li-favoritado" style="<?=$strFavoritado?>">
                    <i class="material-icons text-success">favorite</i>
                  </li>
                </ul>
              </div>
            </div>
            <div class="col-md-12">
              <div class="postagem-inner postagem-inner-bot">
                <?=$strTexto?>
                
                <?php
                // @todo talvez criar uma view pra cada tipo de anexo
                if(count($arquivosPostImg) > 0){
                  ?>
                  <div class="fcbkGrid" style="display:none;">
                    <?php
                    foreach($arquivosPostImg as $arquivo){
                      echo "<input type='hidden' class='img_url' value='".base_url() . $arquivo["gta_caminho"]."' />";
                    }
                    ?>
                  </div>
                  <?php
                }
                
                if(count($arquivosPostAud) > 0){
                  foreach($arquivosPostAud as $arquivo){
                    echo "<audio>";
                    echo "  <source src='".base_url() . $arquivo["gta_caminho"]."'>";
                    echo "</audio>";
                  }
                }

                if(count($arquivosPostVid) > 0){
                  foreach($arquivosPostVid as $arquivo){
                    if(eh_link_youtube($arquivo["gta_caminho"])){
                      ?>
                      <style>.embed-container { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%; } .embed-container iframe, .embed-container object, .embed-container embed { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }</style>
                      <div class='embed-container'><iframe src='https://www.youtube.com/embed/<?php echo pegaStrLinkYoutube($arquivo["gta_caminho"]); ?>' frameborder='0' allowfullscreen></iframe></div>
                      <?php
                    } else {
                      $url = base_url() . $arquivo["gta_caminho"];
                    
                      #echo "<div class='wrapper'>";
                      #echo "  <div class='videocontent'>";
                      echo "    <video class='post-video' controls class='video-js vjs-layout-medium' preload='auto'>";
                      echo "      <source src='$url'>";
                      echo "    </video>";
                      #echo "  </div>";
                      #echo "</div>";
                    }
                  }
                }
                ?>
              </div>
            </div>
            <?php
            if(count($arquivosPostDiv) > 0){
              ?>
              <div class="col-md-12">
                <div class="postagem-inner postagem-inner-bot">
                  <ul class="ul-base-arquivos">
                    <?php
                    #@todo melhorar a aparencia dos links
                    foreach($arquivosPostDiv as $arquivo){
                      $link = base_url() . $arquivo["gta_caminho"];
                      $nome = basename($link);

                      echo "<li>";
                      echo "  <a class='btn btn-link btn-info btn-sm' href='$link' target='_blank'>";
                      echo "    <i class='material-icons'>attach_file</i>";
                      echo "    $nome";
                      echo "  </a>";
                      echo "</li>";
                    }
                    ?>
                  </ul>
                </div>
              </div>
              <?php
            }
            ?>
          </div>
          <?php
        }
        ?>
      </div>
    </div>

    <?php
    /*
    <div class="card" style="margin-top: 50px;">
      <div class="card-header card-header-info">
        <h4 class="card-title">Timeline do Grupo - <?= $vGruDescricao ?></h4>
      </div>
      <div class="card-body" style="padding-bottom:5px;">
        <?php
        foreach($arrPostagens as $postagem){
          ?>
          <div class="row item-postagem">
            <div class="col-md-12">
              <div style="margin-right:10px;" class="profile-photo-small pull-left">
                <img src="https://demos.creative-tim.com/material-kit/assets/img/faces/avatar.jpg" alt="Circle Image" class="rounded-circle img-fluid" />
              </div>
              <span class="titulo">título</span>
              <br />
              <span class="autor">Leandro Parra 2 | 02/08/19 14:59</span>
              <div class="ripple-container"></div>
            </div>
            <div class="col-md-12">
              asd asd asd asd asd
            </div>
          </div>
          <?php
        }
        ?>
      </div>
    </div>
    */
    ?>
  </div>
  <div class="col-md-3">

  </div>
</div>