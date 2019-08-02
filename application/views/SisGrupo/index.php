<?php
$GrupoTimeline = $GrupoTimeline ?? array();
$arrPostagens  = $arrPostagens ?? array();
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
      <div class="card-header card-header-success">
        <h4 class="card-title">Nova Postagem</h4>
      </div>
      <div class="card-body" style="padding-bottom:5px;">
        <form id="frmNovaPostagem" action="<?php echo base_url() ?>GrupoTimeline/postNovo" method="post" enctype="multipart/form-data">
          <div class="" style="margin-bottom:0; padding-bottom:0;">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group bmd-form-group has-success" style="">
                  <label class="bmd-label-floating">Título (opcional)</label>
                  <input maxlength="100" name="titulo" type="text" class="form-control" value="<?= $titulo; ?>" />
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group bmd-form-group has-success" style="">
                  <label class="bmd-label-floating">Escreva algo para o grupo</label>
                  <textarea class="form-control" rows="2" name="descricao">
                    <?= $descricao; ?>
                  </textarea>
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
                <a href="javascript:;" class="btn btn-info btn-sm pull-right" onclick="fncAnexarArqPostagem()">
                  <i class="material-icons">attach_file</i>
                  Anexar
                </a>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group bmd-form-group has-success" style="">
                  <div id="lista-anexos" style="border-top: solid 1px #CCC; width: 100%; padding-top:8px;">
                </div>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>

    <div class="card" style="margin-top: 50px;">
      <div class="card-header card-header-success">
        <h4 class="card-title">Timeline do Grupo - <?= $vGruDescricao ?></h4>
      </div>
      <div class="card-body" style="padding-bottom:5px;">
      </div>
    </div>
  </div>
  <div class="col-md-3">

  </div>
</div>