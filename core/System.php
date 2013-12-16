<?php

namespace Figeor\Core;

use \PDO;
use \PDOException;

class System {

    const TABLE_PROJECTS = 'projects';
    const TABLE_USERS = 'users';
    const TABLE_ATTACHMENTS = 'files';
    const TABLE_TASKS = 'tasks';
    const TABLE_REMINDERS = 'reminders';
    const TABLE_USER_PROJECTS = 'user_projects';
    const TABLE_PROJECT_TASKS = 'project_tasks';
    const TABLE_TASK_REMINDERS = 'task_reminders';
    const TABLE_TASK_ATTACHMENTS = 'task_files';

    private $DBH;
    private static $instance = null;
    private $user = null;

    private function __construct() {
        $config = parse_ini_file('config.ini');
        $this->DBH = $this->connectToDatabase($config['host'], $config['database'], $config['user'], $config['password']);
    }

    private function __clone() {
        ;
    }

    /**
     * @return System instancia
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    private final function connectToDatabase($host, $database, $username, $password) {
        try {
            $DBH = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $username, $password);
            $DBH->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING); // ZMENIT POTOM LEN NA EXCEPTION
            $DBH->query('SET NAMES utf8');
        } catch (PDOException $e) {
            header("HTTP/1.1 503 Service Unavailable");
            die('Nepodarilo sa spojisť s databázou!');
        }
        return $DBH;
    }

    /**
     * @return PDO databazovy handler
     */
    public function getDBH() {
        return $this->DBH;
    }

    public static function currentUser() {
        if (isset($_SESSION['userId'])) {
            return new \Figeor\Models\User($_SESSION['userId']);
        } else {
            return null;
        }
    }

    public static function checkLogin() {
        if (isset($_POST['email']) && isset($_POST['password'])) {
            $pwd = md5($_POST['password']);
            $email = $_POST['email'];
            $dbh = self::getInstance()->getDBH();
            $q = $dbh->prepare('SELECT * FROM ' . self::TABLE_USERS . ' WHERE email=:em LIMIT 1;');
            $q->bindValue(':em', $email, PDO::PARAM_STR);
            $q->execute();
            if ($q->rowCount()) {
                $r = $q->fetch();
                if ($r['password'] == $pwd) {
                    $_SESSION['userId'] = $r['id'];
                }
            }
            $url = '/tasks/view';
            ob_clean();
            header('Location: ' . $url, false, 301);
            ob_end_flush();
            exit;
        }
    }

}

