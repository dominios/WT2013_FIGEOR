<?php

namespace Figeor\Models;

use Figeor\Core\System;
use \PDO;

class Project implements IModel {

    private $id;
    private $user;
    private $name;
    private $description;
    private $dateCreated;
    private $deadline;
    private $dateFinished;
    private $tasks;

    public function __construct($id) {
        $DBH = System::getInstance()->getDBH();
        $Q = $DBH->prepare('SELECT * FROM ' . System::TABLE_PROJECTS . ' WHERE id=:i LIMIT 1');
        $Q->bindValue(':i', $id, PDO::PARAM_INT);
        $Q->execute();
        if ($Q->rowCount()) {
            $R = $Q->fetch();
            $this->id = $R['id'];
            $this->user = new User($R['id']);
            $this->name = $R['name'];
            $this->description = $R['description'];
            $this->dateCreated = $R['dateCreated'];
            $this->dateFinished = $R['dateFinished'];
            $this->deadline = $R['deadline'];
            $this->tasks = $this->fetchTasks();
        }
    }

    public static function create($initialValues) {
        $DBH = System::getInstance()->getDBH();
        $Q = $DBH->prepare('INSERT INTO ' . System::TABLE_PROJECTS . ' (name, user, dateCreated) VALUES (:n, :iu, :dc);');
        $Q->bindValue(':iu', System::currentUser()->getId(), PDO::PARAM_INT);
        $Q->bindValue(':dc', time());
        $Q->bindValue(':n', 'Nový projekt');
        $Q->execute();
        $newId = $DBH->lastInsertId();
        $DBH->exec('INSERT INTO ' . System::TABLE_USER_PROJECTS . ' (user,project) VALUES (' . System::currentUser()->getId() . ', ' . $newId . ');');
        return new self($newId);
    }

    public static function exists($id) {
        $DBH = System::getInstance()->getDBH();
        $Q = $DBH->prepare('SELECT 1 FROM ' . System::TABLE_PROJECTS . ' WHERE id = :i LIMIT 1');
        $Q->bindValue(':i', $id, PDO::PARAM_INT);
        $Q->execute();
        return $Q->rowCount() ? true : false;
    }

    public static function fetchByUser(User $user) {
        $DBH = System::getInstance()->getDBH();
        $ret = array();
        foreach ($DBH->query('SELECT * FROM ' . System::TABLE_USER_PROJECTS . ' WHERE user = "' . $user->getId() . '"') as $r) {
            $ret[] = new Project($r['project']);
        }
        return $ret;
    }

    public function update() {
        $DBH = System::getInstance()->getDBH();
        $Q = $DBH->prepare('UPDATE ' . System::TABLE_PROJECTS . ' SET name = :n, description = :d, dateFinished = :dc, deadline=:dl WHERE id = :id');
        $Q->bindValue(':id', $this->id, PDO::PARAM_INT);
        $Q->bindValue(':n', $this->name, PDO::PARAM_STR);
        $Q->bindValue(':d', $this->description, PDO::PARAM_STR);
        $Q->bindValue(':dc', $this->dateFinished, PDO::PARAM_INT);
        $Q->bindValue(':dl', $this->deadline, PDO::PARAM_INT);
        $Q->execute();
    }

    public function isDeletable() {
        return true;
    }

    public function delete() {
        $DBH = System::getInstance()->getDBH();
        $DBH->exec('DELETE FROM ' . System::TABLE_PROJECTS . ' WHERE id_p = ' . $this->id);
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

    public function getDateFinished($format = null) {
        return $format === null ? $this->dateFinished : date($format, $this->dateFinished);
    }

    public function setDateFinished($dateFinished) {
        $this->dateFinished = $dateFinished;
        return $this;
    }

    public function getNumberOfTasks($includeSubTasks = false) {
        $DBH = System::getInstance()->getDBH();
        $R = $DBH->query('SELECT COUNT(*) FROM ' . System::TABLE_PROJECT_TASKS . ' WHERE project = "' . $this->id . '"')->fetch();
        return $R['COUNT(*)'];
    }

    public function getLastActivity($format = null) {
        $DBH = System::getInstance()->getDBH();
        $r = $DBH->query('SELECT GREATEST(dateCreated,dateFinished) AS greatest
            FROM ' . System::TABLE_TASKS . ' t
            JOIN  ' . System::TABLE_PROJECT_TASKS . ' pt ON t.id = pt.task
            WHERE pt.project = ' . $this->getId() . ' && dateCreated IS NOT NULL && dateFinished IS NOT NULL
        ;')->fetch();
        if ($r['greatest'] === null) {
            $r['greatest'] = $this->dateCreated;
        }
        return $format === null ? $r['greatest'] : date($format, $r['greatest']);
    }

    private function fetchTasks() {
        $DBH = System::getInstance()->getDBH();
        $ret = array();
        foreach ($DBH->query('SELECT task FROM ' . System::TABLE_PROJECT_TASKS . ' pt JOIN ' . System::TABLE_TASKS . ' t ON pt.task = t.id WHERE project = "' . $this->id . '" && parentTask IS NULL') as $R) {
            $ret[] = new Task($R['task']);
        }
        return $ret;
    }

    public function getTasks() {
        return $this->tasks;
    }

    public function getDeadline($format = null) {
        return $format === null ? $this->deadline : date($format, $this->deadline);
    }

    public function setDeadline($deadline) {
        $this->deadline = $deadline;
        return $this;
    }

    public function getPointsOverall() {
        $dbh = System::getInstance()->getDBH();
        $r = $dbh->query('SELECT SUM(points) AS sum
            FROM ' . System::TABLE_TASKS . ' t
            JOIN ' . System::TABLE_PROJECT_TASKS . ' pt ON t.id = pt.task
            WHERE project = ' . $this->getId() . ';')->fetch();
        return $r['sum'];
    }

    public function getBurntPoints() {
        $dbh = System::getInstance()->getDBH();
        $r = $dbh->query('SELECT SUM(points) AS sum
            FROM ' . System::TABLE_TASKS . ' t
            JOIN ' . System::TABLE_PROJECT_TASKS . ' pt ON t.id = pt.task
            WHERE project = ' . $this->getId() . ' && t.dateFinished IS NOT NULL;')->fetch();
        return $r['sum'] == null ? 0 : $r['sum'];
    }

    public function getAllTaskCount() {
        $dbh = System::getInstance()->getDBH();
        $r = $dbh->query('SELECT COUNT(*) AS count
            FROM ' . System::TABLE_TASKS . ' t
            JOIN ' . System::TABLE_PROJECT_TASKS . ' pt ON t.id = pt.task
            WHERE project = ' . $this->getId() . ';')->fetch();
        return $r['count'];
    }

    public function getOverallDurationDays() {
        $dStart = new \DateTime("@$this->dateCreated");
        $dEnd = new \DateTime("@" . $this->deadline);
        $dDiff = $dStart->diff($dEnd);
        return $dDiff->days;
    }

    public function getCurrentDay() {
        $dStart = new \DateTime("@$this->dateCreated");
        $dEnd = new \DateTime("@" . time());
        $dDiff = $dStart->diff($dEnd);
        return $dDiff->days;
    }

    public function getDateCreated($format = null) {
        return $format === null ? $this->dateCreated : date($format, $this->dateCreated);
    }

}

