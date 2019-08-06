<?php
$frmId   = $frmId ?? "#frmNovaPostagem";
$idAnexo = $idAnexo ?? date("YmdHis");
?>

<div class="row">
  <div class="col-md-12">
    <div class="file-field">
      <a href="javascript:;" class="btn btn-danger btn-sm float-left" onclick="deletaAnexo(this);">
        <i class="material-icons">delete</i>
      </a>
      <div class="btn btn-default btn-sm float-left">
        <span onclick="$('<?= $frmId; ?> #anexo<?= $idAnexo; ?>').click();">Anexo:</span>
        <input class="anexo" id="anexo<?= $idAnexo; ?>" type="file" name="arquivos[]" accept="image/png, image/jpeg, application/pdf, .xls, .xlsx, .doc, .docx, .ogg, .mp3, .mp4, .mp4, video/*" />
        <span onclick="$('<?= $frmId; ?> #anexo<?= $idAnexo; ?>').click();" class="lbl_anexo">Selecione o arquivo ...</span>
      </div>
    </div>
  </div>
</div>