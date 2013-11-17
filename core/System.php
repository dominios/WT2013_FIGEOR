<?php

namespace Figeor\Core;

use \PDO;
use \PDOException;

class System {

    const TABLE_PROJECTS = 'projects';
    const TABLE_USERS = 'users';
    const TABLE_FILES = 'files';
    const TABLE_TASKSK = 'tasks';
    const TABLE_REMINDERS = 'reminders';

    private $DBH;
    private static $instance = null;

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
    }

    /**
     * @return PDO databazovy handler
     */
    public function getDBH() {
        return $this->DBH;
    }

}

