<?php
use Configuration\Config;
use Configuration\Route;
use SQL\RepositorioSQL;


$config = include $_SERVER['DOCUMENT_ROOT'].'/controller/Configuration/Route.php';

include Config::getModelUsuario();
include Config::getRepositorioJSON();
include Config::getRepositorioSQL();
include Config::getSignUpValidator();

class Registro
{
    private $validator;
    private $dataBase;
    private $usuario;

    const NO_ERROR = '';

    public function __construct(RepositorioUsuarios $dataBase, Usuario $usuario)
    {
        $this->validator = new SignUpValidator();
        $this->dataBase = $dataBase;
        $this->usuario = $usuario;
    }

    public function submitInput()    {
      $this->validator->validate($this->usuario, $this->dataBase);
      if ($this->validator->isUserValid() === NO_ERROR)
          if ($this->validator->isEmailValid() === NO_ERROR)
              if ($this->validator->isPasswordValid() === NO_ERROR)
                  if ($this->validator->isPasswordConfirmValid() === NO_ERROR){
                      $this->usuario->setPassword(password_hash($this->usuario->getPassword(), PASSWORD_DEFAULT));
                      $this->registerUser();
                  }
    }

    private function registerUser(){
      $this->dataBase->submitUser($this->usuario);
    }

    public function getValidator(){
      return $this->validator;
    }
}

$DB = new RepositorioSQL();
$id = $DB->getRepositorioUsuarios()->getUsersCount() + 1;
$nombre = $_POST['nombre'];
$email = $_POST['email'];
$password = $_POST['password'];
$passwordConfirm = $_POST['passwordConfirm'];

$usuario = new Usuario($id, $nombre, null, $email, null, $password, $passwordConfirm);
$registro = new Registro($DB->getRepositorioUsuarios(), $usuario);
$registro->submitInput();
$validator = $registro->getValidator();
header('location: '.Route::getIndex().'?id=signup&nameError='.$validator->isUserValid().'&emailError='.$validator->isEmailValid().'&passError='.$validator->isPasswordValid().'&passConfirmError='.$validator->isPasswordConfirmValid());
