<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller
{

  public function __construct()
  {
    CI_Controller::__construct();
    $this->load->helper("utils_helper");
  }

  public function index()
  {
    require_once(APPPATH."/models/Session.php");
    fecha_session();

    $vUsuario = $this->session->flashdata('login_usuario') ?? "";
    $vRetMsg  = $this->session->flashdata('login_ret_msg') ?? "";
    $this->load->view('Login/LoginAdmin', array(
      "vUsuario"=>$vUsuario,
      "vRetMsg" =>$vRetMsg,
    ));
  }

  public function executaLogin()
  {
    require_once(APPPATH."/models/Session.php");
    require_once(APPPATH."/models/Login.php");
    $variaveisPost = processaPost();
    $vUsuario      = $variaveisPost->usuario ?? "";
    $vSenha        = $variaveisPost->senha ?? "";

    $retLogin = executaLogin($vUsuario, $vSenha, true);
    if($retLogin->erro){
      $this->session->set_flashdata('login_ret_msg', $retLogin->msg);
      $this->session->set_flashdata('login_usuario', $vUsuario);
      
      fecha_session();
      redirect(BASE_URL);
    } else {
      $Usuario = json_decode($retLogin->infoUsr);
      
      require_once(APPPATH."/models/TbMenu.php");
      $arrMenu = geraArrMenuUsuario($Usuario->id, $Usuario->admin);
      inicia_session($arrMenu, $Usuario);
      
      redirect(BASE_URL . 'Dashboard');
    }
  }
}