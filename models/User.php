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
    private $reminders;

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
            $this->password = $R['password'];
            $this->reminders = (bool) $R['reminders'];
        }
    }

    public static function create($initialValues) {
        $DBH = System::getInstance()->getDBH();
        $Q = $DBH->prepare('INSERT INTO ' . System::TABLE_USERS . ' (email) VALUES (:em)');
        $Q->bindValue(':em', $initialValues['email'], PDO::PARAM_STR);
        $Q->execute();
        return new self($DBH->lastInsertId());
    }

    public static function exists($id) {
        $DBH = System::getInstance()->getDBH();
        $Q = $DBH->prepare('SELECT 1 FROM ' . System::TABLE_USERS . ' WHERE id=:i LIMIT 1');
        $Q->bindValue(':i', $id, PDO::PARAM_INT);
        $Q->execute();
        return $Q->rowCount() ? true : false;
    }

    public function update() {
        $DBH = System::getInstance()->getDBH();
        $Q = $DBH->prepare('UPDATE ' . System::TABLE_USERS . ' SET name=:n, surname=:sn, email=:em, reminders=:r WHERE id=:id');
        $Q->bindValue(':id', $this->id, PDO::PARAM_INT);
        $Q->bindValue(':n', $this->name, PDO::PARAM_STR);
        $Q->bindValue(':sn', $this->surname, PDO::PARAM_STR);
        $Q->bindValue(':em', $this->email, PDO::PARAM_INT);
        $Q->bindValue(':r', $this->reminders, PDO::PARAM_INT);
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

    public function setName($name) {
        $this->name = $name;
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
        $this->password = $password;
        $DBH = System::getInstance()->getDBH();
        $Q = $DBH->prepare('UPDATE ' . System::TABLE_USERS . ' SET password=:p WHERE id=:id');
        $Q->bindValue(':id', $this->id, PDO::PARAM_INT);
        $Q->bindValue(':p', $this->password, PDO::PARAM_STR);
        $Q->execute();
    }

    public function useReminders() {
        return (bool) $this->reminders;
    }

    public function setUseReminders($bool) {
        $this->reminders = (bool) $bool;
    }

    public function getTasksByDays($days) {
        $dbh = System::getInstance()->getDBH();
        $ret = array();
        $timeLimit = time() + ($days * 24 * 3600);
        $timeCondition = $days !== false ? ' && t.deadline <= ' . $timeLimit : '';
        foreach ($dbh->query('SELECT t.id AS taskId FROM ' . System::TABLE_TASKS . ' t
            JOIN ' . System::TABLE_PROJECT_TASKS . ' pt ON t.id = pt.task
            JOIN ' . System::TABLE_USER_PROJECTS . ' up ON up.project = pt.project
            WHERE up.user = ' . $this->getId() . ' && t.dateFinished IS NULL && t.deadline >= ' . time() . $timeCondition . '
            ORDER BY
                CASE WHEN t.deadline > 0 && t.deadline IS NOT NULL THEN 0 ELSE 1 END ASC,
                CASE WHEN t.priority != 0 && t.priority IS NOT NULL THEN 0 ELSE 1 END ASC;') as $r) {
            $ret[] = new Task($r['taskId']);
        }
        return $ret;
    }

    public static function getByEmail($email) {
        $dbh = System::getInstance()->getDBH();
        $q = $dbh->query('SELECT id FROM ' . System::TABLE_USERS . ' WHERE email="' . $email . '"');
        if ($q->rowCount()) {
            $r = $q->fetch();
            return new User($r['id']);
        } else {
            return null;
        }
    }

}

