<?php
//@todo melhorar CSS inline

$vGruDescricao = $vGruDescricao ?? "";
$arrPostagens  = $arrPostagens ?? array();
$arrArquivos   = $arrArquivos ?? array();
$arrSalvos     = $arrSalvos ?? array();
$arrResp       = $arrResp ?? array();
$vPesNome      = $vPesNome ?? "";

$strPesNome    = "";
if($vPesNome != ""){
  $strPesNome = "<h4><b>$vPesNome</b></h4>";
}
?>

<h4>Postagens - <?= $vGruDescricao ?></h4>
<?=$strPesNome?>
<div class="row">
  <div class="col-md-12">
    <?php
    if(count($arrPostagens) <= 0){
      require_once(APPPATH."/helpers/notificacao_helper.php");
      echo exibe_info("Nenhuma postagem para exibir.");
    } else {
      foreach($arrPostagens as $postagem){
        $id       = $postagem["grt_id"] ?? "";
        $gruId    = $postagem["grt_gru_id"] ?? "";
        $pesId    = $postagem["grp_pes_id"] ?? "";
        $titulo   = $postagem["grt_titulo"] ?? "";
        $pessoa   = $postagem["pes_nome"] ?? "";
        $data     = $postagem["grt_data"] ?? "";
        $texto    = $postagem["grt_texto"] ?? "";
        $foto     = $postagem["pes_foto"] ?? FOTO_DEFAULT;

        $idUsuLogado       = pegaUsuarioLogadoId() ?? 0;
        $grpPessoaLogado   = pegaGrupoPessoaLogadoId() ?? -1;
        $ehAdminLogado     = ehAdminGrupo($gruId);
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

        // resposta
        $loopRespostas = $arrResp[$id] ?? array();
        // ========
        ?>
        <div class="row item-postagem" id="item-postagem-<?=$id?>">
          <div class="col-md-12">
            <div class="postagem-inner postagem-inner-top" style="padding-left:10px; padding-right:10px;">
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
                    if($ehAdminLogado){
                      ?>
                      <a href="javascript:;" onclick="favoritarPostagem(<?= $id ?>)" class="dropdown-item dropdown-item-info">Salvar Favorito</a>
                      <a href="javascript:;" onclick="deletarPostagem(<?= $id ?>)" class="dropdown-item dropdown-item-info">Excluir</a>
                      <?php
                    } else {
                      if($ehPostagemPropria){
                        ?>
                        <a href="javascript:;" onclick="deletarPostagem(<?= $id ?>)" class="dropdown-item dropdown-item-info">Excluir</a>
                        <?php
                      } else {
                        ?>
                        <a href="javascript:;" onclick="favoritarPostagem(<?= $id ?>)" class="dropdown-item dropdown-item-info">Salvar Favorito</a>
                        <?php
                      }
                    }
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
            <div class="postagem-inner postagem-inner-bot" style="padding-left:10px; padding-right:10px;">
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
                    echo "<video class='post-video' controls class='video-js vjs-layout-medium' preload='auto'>";
                    echo "  <source src='$url'>";
                    echo "</video>";
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
          <div class="col-md-12">
            <div class="postagem-inner postagem-inner-bot dv-resposta" style="border-bottom:none; padding-bottom:0;">
              <?php
              if(count($loopRespostas) > 0){
                echo geraHtmlRespostas($loopRespostas);
              }
              ?>
            </div>
          </div>
          <div class="col-md-12">
            <div class="postagem-inner postagem-inner-bot">
              <?php
              $foto2 = pegaFotoLogado();
              ?>
              
              <div class="row">
                <div class="col-md-1 dv-img-comentario">
                  <a class="text-info" href="javascript:;">
                    <img src="<?=$foto2?>" alt="Circle Image" class="rounded-circle img-fluid img-comentario">
                  </a>
                </div>
                <div class="col-md-11 dv-area-comentario">
                  <div class="form-group bmd-form-group has-info" style="margin-top:0; padding-bottom:0;">
                    <textarea data-id="<?=$id?>" style="padding-top:0; padding-bottom:0;" placeholder="Escreva um comentário" class="form-control" rows="1" name="comentario"></textarea>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <?php
      }
    }
    ?>
  </div>
</div>