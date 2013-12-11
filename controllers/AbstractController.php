<?php

namespace Figeor\Controller;

class AbstractController {

    protected $action = null;
    protected $defaultAction = '';

    public final function doAction($action) {
        if (method_exists($this, $action)) {
            $methodName = $action;
        } else {
            $methodName = $this->defaultAction;
        }
        return $this->{$methodName}();
    }

    public static function parseURL() {

        if (sizeof($_POST) && isset($_POST['action'])) {
            $action = preg_replace('/^./', '', $_POST['action']);
            $arr = explode('/', $action, 2);
            return array('controller' => $arr[0], 'action' => $arr[1]);
        }

        $action = '';
        $request = $_SERVER['REQUEST_URI'];
        $keys = explode('/', $request, 4);
        // zacina sa / takze na indexe 0 je prazdny string
        // 1 - controller
        // 2 - akcia
        // 3 - zatial zvysok parametre (GET)
        $controller = $keys[1];
        if ($keys[2] !== null) {
            $action = $keys[2];
        }
        $params = explode('/', preg_replace('/\?.*/', '', $keys[3])); // vyhodi vsetko ?... cize "stary get"

        if (count($params) >= 2) {
            unset($_GET);
            for ($i = 0; $i < count($params); $i++) {
                $_GET[$params[$i++]] = $params[$i];
            }
        } else {
            unset($_GET);
            $_GET['id'] = $params[0];
        }

        return array('controller' => $controller, 'action' => $action);
    }

}

