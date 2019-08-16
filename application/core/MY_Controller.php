<?php
class MY_Controller extends CI_Controller
{
  private $_min_credentials = 100;
  private $_credenciais     = 101;
  private $_usuario_info    = false; #tb_usuario OU tb_pessoa

  function __construct($admin=true, $grupo=false)
  {
    parent::__construct();

    $this->_credenciais  = $this->session->userdata('credenciais') ?? 9999;
    $this->_usuario_info = $this->session->userdata('usuario_info') ?? false;

    if($admin){
      #tb_usuario_admin
      $this->initAdmin();
    } else if(!$grupo) {
      #tb_usuario
      $this->initSistema();
    } else {
      #tb_pessoa
      $this->initGrupo();
    }
  }

  private function initAdmin()
  {
    $credenciaisOk = (!isset($this->_credenciais) or $this->_credenciais < $this->_min_credentials);
    $idUsuarioOk   = ($this->_usuario_info === false) ? false: ($this->_usuario_info->id > 0 && $this->_usuario_info->admin == 1);

    if($credenciaisOk === false || $idUsuarioOk === false){
      $this->session->set_flashdata('LoginMessage', 'Sua sessão expirou. Faça o login novamente.');
      redirect(BASE_URL);
      return;
    }
  }

  private function initSistema()
  {
    $credenciaisOk = (!isset($this->_credenciais) or $this->_credenciais < $this->_min_credentials);
    $ehCliente     = (isset($this->_usuario_info->cliente)) && $this->_usuario_info->cliente == 1;
    $idUsuarioOk   = ($this->_usuario_info === false) ? false: ($this->_usuario_info->id > 0 && $this->_usuario_info->admin == 0 && !$ehCliente && !isset($this->_usuario_info->usuario_cad));

    if($credenciaisOk === false || $idUsuarioOk === false){
      $this->session->set_flashdata('LoginMessage', 'Sua sessão expirou. Faça o login novamente.');
      redirect(BASE_URL . 'Login/sistema');
      return;
    }
  }

  private function initGrupo()
  {
    $credenciaisOk = (!isset($this->_credenciais) or $this->_credenciais < $this->_min_credentials);
    $ehAdmin       = (isset($this->_usuario_info->admin)) && $this->_usuario_info->admin == 1;
    $idUsuarioOk   = ($this->_usuario_info === false) ? false: ($this->_usuario_info->id > 0 && !$ehAdmin);
    $vGrpId        = $this->session->grp_id ?? NULL;

    if($credenciaisOk === false || $idUsuarioOk === false || !$vGrpId > 0){
      $this->session->set_flashdata('LoginMessage', 'Sua sessão expirou. Faça o login novamente.');
      redirect(BASE_URL . 'Login/grupo');
      return;
    }
  }
}