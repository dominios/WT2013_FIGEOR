<?php

namespace Figeor\Controller;

use Figeor\Core\System;
use Figeor\Core\View;

class IndexController extends AbstractController {

    public function __construct() {
        $this->defaultAction = 'login';
    }

    protected function login() {
        $view = new View('index/login.php');
        $ret = array();
        $ret['title'] = 'Prihlasovací formulár';
        $ret['main'] = $view->renderToString();
        return $ret;
    }

    protected function logout() {
        unset($_SESSION['userId']);
        $this->redirect('/');
    }

    protected function profil() {
        $user = System::currentUser();
        $view = new View('index/profil.php');
        $view->user = $user;
        $ret = array();
        $ret['title'] = 'Môj profil';
        $ret['main'] = $view->renderToString();
        return $ret;
    }

    protected function updateProfil() {
        $user = System::currentUser();
        if ($user->getId() == $_POST['userId']) {
            $user->setEmail($_POST['userEmail']);
            $user->setSurname($_POST['userSurname']);
            $user->setName($_POST['userName']);
            $user->setUseReminders($_POST['userReminders']);
            $user->update();
            if (md5($_POST['userPass1']) == $user->getPassword()) {
                if ($_POST['userPass2'] === $_POST['userPass3'] && strlen($_POST['userPass2'])) {
                    $user->setPassword(md5($_POST['userPass2']));
                }
            }
        }
        $this->redirect('/index/profil');
    }

}

