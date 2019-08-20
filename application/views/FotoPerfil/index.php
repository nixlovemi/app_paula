<style>
  .image-upload > input{
    display: none;
  }
  .image-upload i{
    width: 80px;
    cursor: pointer;
    font-size: 50px;
  }
  #frmAlterarFotoPerfil #spn_nome_foto{
    display: block;
  }
</style>

<form id="frmAlterarFotoPerfil" method="post" enctype="multipart/form-data">
  <div class="image-upload">
    <label for="file-alterar-foto-perfil">
      <!--<img src="https://goo.gl/pB9rpQ"/>-->
      <i class="material-icons text-info">photo_camera</i>
      <span id="spn_nome_foto">Selecione ..</span>
    </label>

    <input id="file-alterar-foto-perfil" type="file" accept=".png, .jpg" />
  </div>
</form>