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
    $LoginMessage = $this->session->flashdata('LoginMessage') ?? "";
    fecha_session();

    $vUsuario = $this->session->flashdata('login_usuario') ?? "";
    $this->load->view('Login/LoginAdmin', array(
      "vUsuario"  => $vUsuario,
      "vLoginMsg" => $LoginMessage,
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
      $this->session->set_flashdata('LoginMessage', $retLogin->msg);
      $this->session->set_flashdata('login_usuario', $vUsuario);
      
      redirect(BASE_URL);
    } else {
      $Usuario = json_decode($retLogin->infoUsr);
      
      require_once(APPPATH."/models/TbMenu.php");
      $arrMenu = geraArrMenuUsuario($Usuario->id, $Usuario->admin);
      inicia_session($arrMenu, $Usuario);
      
      redirect(BASE_URL . 'Dashboard');
    }
  }

  public function sistema()
  {
    require_once(APPPATH."/models/Session.php");
    $LoginMessage = $this->session->flashdata('LoginMessage') ?? "";
    fecha_session();

    $vUsuario = $this->session->flashdata('login_usuario') ?? "";
    $this->load->view('Login/LoginSistema', array(
      "vUsuario"  => $vUsuario,
      "vLoginMsg" => $LoginMessage,
    ));
  }

  public function sistemaLogin()
  {
    require_once(APPPATH."/models/Session.php");
    require_once(APPPATH."/models/Login.php");
    $variaveisPost = processaPost();
    $vUsuario      = $variaveisPost->usuario ?? "";
    $vSenha        = $variaveisPost->senha ?? "";

    $retLogin = executaLogin($vUsuario, $vSenha, false);
    if($retLogin->erro){
      $this->session->set_flashdata('LoginMessage', $retLogin->msg);
      $this->session->set_flashdata('login_usuario', $vUsuario);

      redirect(BASE_URL . 'Login/sistema');
    } else {
      $Usuario = json_decode($retLogin->infoUsr);

      require_once(APPPATH."/models/TbMenu.php");
      $arrMenu = geraArrMenuUsuario($Usuario->id, $Usuario->admin);
      inicia_session($arrMenu, $Usuario);
      inicia_usuario_grps();

      redirect(BASE_URL . 'Sistema');
    }
  }

  public function grupo()
  {
    require_once(APPPATH."/models/Session.php");
    $LoginMessage = $this->session->flashdata('LoginMessage') ?? "";
    fecha_session();

    $vUsuario = $this->session->flashdata('login_usuario') ?? "";
    $this->load->view('Login/LoginGrupo', array(
      "vUsuario"  => $vUsuario,
      "vLoginMsg" => $LoginMessage,
    ));
  }

  public function grupoLogin()
  {
    // @todo fazer tela quando pessoa for cadastrada por dois usuários diferentes
    // provavelmente fazer uma tela antes pra ela escolher em qual grupo/usuário vai logar
    require_once(APPPATH."/models/Session.php");
    require_once(APPPATH."/models/Login.php");
    $variaveisPost = processaPost();
    $vUsuario      = $variaveisPost->usuario ?? "";
    $vSenha        = $variaveisPost->senha ?? "";

    $retLogin = executaLogin($vUsuario, $vSenha, false, true);
    if($retLogin->erro){
      $this->session->set_flashdata('LoginMessage', $retLogin->msg);
      $this->session->set_flashdata('login_usuario', $vUsuario);

      redirect(BASE_URL . 'Login/grupo');
    } else {
      $Usuario = json_decode($retLogin->infoUsr);

      require_once(APPPATH."/models/TbMenu.php");
      $arrMenu = geraArrMenuUsuario($Usuario->id, false, true);
      inicia_session($arrMenu, $Usuario, true, -1); #coloco -1 pra iniciar, depois altero pro certo

      // pega grupos dessa pessoa
      require_once(APPPATH."/models/TbGrupoPessoa.php");
      $retGrp = pegaGruposPessoaId($Usuario->id);
      if($retGrp["erro"]){
        $this->session->set_flashdata('LoginMessage', $retGrp["msg"]);
        $this->session->set_flashdata('login_usuario', $vUsuario);

        redirect(BASE_URL . 'Login/grupo');
      } else {
        $GruposPessoa          = $retGrp["GruposPessoa"] ?? array();
        $vGrpId                = $GruposPessoa[0]["grp_id"] ?? NULL; #@todo aqui peguei o primeiro que veio / o certo é mostrar tds e a pessoa escolher
        $this->session->grp_id = $vGrpId;
        
        redirect(BASE_URL . 'SisGrupo');
      }
    }
  }
}