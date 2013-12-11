<?php

namespace Figeor\Models;

use Figeor\Core\System;
use \PDO;

class User implements IModel {

    private $id;
    private $name;
    private $surname;
    private $email;
    private $password;

    public function __construct($id) {
        $DBH = System::getInstance()->getDBH();
        $Q = $DBH->prepare('SELECT * FROM ' . System::TABLE_USERS . ' WHERE id=:i LIMIT 1');
        $Q->bindValue(':i', $id, PDO::PARAM_INT);
        $Q->execute();
        if ($Q->rowCount()) {
            $R = $Q->fetch();
            $this->id = $R['id'];
            $this->name = $R['name'];
            $this->surname = $R['surname'];
            $this->email = $R['email'];
        }
    }

    public static function create($initialValues) {
        $DBH = System::getInstance()->getDBH();
        $Q = $DBH->prepare('INSET INTO ' . System::TABLE_USERS . ' (email) VALUES (:em)');
        $Q->bindValue(':em', $initialValues['email'], PDO::PARAM_STR);
        $Q->execute();
        return new self($DBH->lastInsertId());
    }

    public static function exists($id) {
        $DBH = System::getInstance()->getDBH();
        $Q = $DBH->prepare('SELECT 1 FROM ' . System::TABLE_USERS . ' WHERE id_u=:i LIMIT 1');
        $Q->bindValue(':i', $id, PDO::PARAM_INT);
        $Q->execute();
        return $Q->rowCount() ? true : false;
    }

    public function update() {
        $DBH = System::getInstance()->getDBH();
        $Q = $DBH->prepare('UPDATE ' . System::TABLE_USERS . ' SET name=:n, surname=:sn, email=:em WHERE id_u=:id');
        $Q->bindValue(':id', $this->id, PDO::PARAM_INT);
        $Q->bindValue(':n', $this->name, PDO::PARAM_STR);
        $Q->bindValue(':sn', $this->surname, PDO::PARAM_STR);
        $Q->bindValue(':em', $this->email, PDO::PARAM_INT);
        $Q->execute();
    }

    public function isDeletable() {
        return true;
    }

    public function delete() {
        $DBH = System::getInstance()->getDBH();
        $DBH->exec('DELETE FROM ' . System::TABLE_PROJECTS . ' WHERE id_p=' . $this->id);
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getSurname() {
        return $this->surname;
    }

    public function setSurname($surname) {
        $this->surname = $surname;
        return $this;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        /* todo */
    }

}

