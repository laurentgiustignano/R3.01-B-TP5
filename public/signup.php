<?php
if(!session_id())
  session_start();

use Iutrds\Tp5\Authentification;
use Iutrds\Tp5\BddConnect;
use Iutrds\Tp5\Exceptions\AuthentificationException;
use Iutrds\Tp5\Exceptions\BddConnectException;
use Iutrds\Tp5\MariaDBUserRepository;
use Iutrds\Tp5\Messages;


require_once 'header.php';
require_once '../vendor/autoload.php';

$bdd = new BddConnect();

try {
  $pdo = $bdd->connexion();
}
catch(BddConnectException $e) {
  Messages::goHome(
    $e->getMessage(),
    $e->getType(),
    "index.php");
  die();
}

$trousseau = new MariaDBUserRepository($pdo);
$auth = new Authentification($trousseau);

if($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    if(empty($_POST['email']) || empty($_POST['password']) || empty($_POST['repassword'])) {
      throw new AuthentificationException("Accès interdit", "danger");
    }
    $retour = $auth->register($_POST['email'], $_POST['password'], $_POST['repassword']);
    $message = "Vous êtes enregistré. Vous pouvez vous authentifier";
    $type = "success";
  }
  catch(AuthentificationException $e) {
    $message = $e->getMessage();
    $type = $e->getType();
  }
}
else {
  $message = "Accès interdit";
  $type = "danger";
}

Messages::goHome($message, $type, "index.php");

require_once 'footer.php';