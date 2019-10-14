<?php
$frmId   = $frmId ?? "#frmNovaPostagem";
$idAnexo = $idAnexo ?? date("YmdHis");
$linkYt  = $linkYt ?? "";
?>

<div class="row">
  <div class="col-md-12">
    <div class="file-field">
      <a href="javascript:;" class="btn btn-danger btn-sm float-left" onclick="deletaAnexo(this);">
        <i class="material-icons">delete</i>
      </a>
      <?php
      if($linkYt != ""){
        ?>
        <input readonly="" style="width:60%; text-align:left;" class="anexo btn btn-sm float-left" id="anexo<?= $idAnexo; ?>" type="text" name="arquivos[]" value="<?= $linkYt ?>" />
        <?php
      } else {
        #@todo ver como subir AVI tbm, hoje so sobe mp4
        ?>
        <div class="btn btn-default btn-sm float-left">
          <span onclick="$('<?= $frmId; ?> #anexo<?= $idAnexo; ?>').click();">Anexo:</span>
          <input class="anexo" id="anexo<?= $idAnexo; ?>" type="file" name="arquivos[]" accept="image/png, image/jpeg, application/pdf, .ogg, .mp3, .mp4" />
          <span onclick="$('<?= $frmId; ?> #anexo<?= $idAnexo; ?>').click();" class="lbl_anexo">Selecione o arquivo ...</span>
        </div>
        <?php
      }
      ?>
    </div>
  </div>
</div>