<?php
class MY_Controller extends CI_Controller
{
  private $_min_credentials = 100;

  function __construct()
  {
    parent::__construct();

    $credenciais = $this->session->userdata('credenciais') ?? 9999;
    $UsuarioJson = $this->session->userdata('usuario_info') ?? false;
    
    $credenciaisOk = (!isset($credenciais) or $credenciais < $this->_min_credentials);
    $idUsuarioOk   = ($UsuarioJson === false) ? false: ($UsuarioJson->id > 0 && $UsuarioJson->admin == 1);

    if($credenciaisOk === false || $idUsuarioOk === false){
      $this->session->set_flashdata('LoginMessage', 'Sua sessão expirou. Faça o login novamente.');
      redirect(BASE_URL);
      return;
    }

    // Session control
    // Example of how I give (the simple way) a user the credentials
    // I set this after a login process controlled by MY_Controller
    /*$session_data = array(
      'id' => 1,
      'username' => 'johndoe',
      'credentials' => 80, // Credentials between 0 and 100 in my case
      'email' => 'john@doe.com',
    );
    $this->session->set_userdata('user', $session_data);*/
    // End of credentials assignment

    /*if (!$this->session->userdata('user')) {
      redirect(BASE_URL);
    }*/

    /*$this->user = $this->session->userdata('user');*/

    // Uncomment to set a minimum credentials level needed to access the whole backend
    // $this->session_control(50);
  }

  /*function session_control($min_credentials = 100, $redirect = TRUE)
  {
    if (!isset($this->user['credentials']) OR $this->user['credentials'] < $min_credentials) {
      if ($redirect === TRUE) {
        redirect(BASE_URL);
      } else {
        return FALSE;
      }
    } else {
      return TRUE;
    }
  }*/
}