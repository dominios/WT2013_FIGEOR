<?php

namespace Figeor\Models;

use Figeor\Core\System;
use \PDO;

class Task implements IModel {

    private $id;
    private $parentTask;
    private $name;
    private $description;
    private $dateCreated;
    private $dateFinished;
    private $deadline;
    private $points;
    private $priority;

    public function __construct($id) {
        $DBH = System::getInstance()->getDBH();
        $Q = $DBH->prepare('SELECT * FROM ' . System::TABLE_TASKS . ' WHERE id=:i');
        $Q->bindValue(':i', $id, PDO::PARAM_INT);
        $Q->execute();
        if ($Q->rowCount()) {
            $R = $Q->fetch();
            $this->id = $R['id'];
            $this->parentTask = is_numeric($R['parentTask']) ? new Task($R['parentTask']) : null;
            $this->name = $R['name'];
            $this->description = $R['description'];
            $this->dateCreated = $R['dateCreated'];
            $this->dateFinished = $R['dateFinished'];
            $this->deadline = $R['deadline'];
            $this->points = $R['points'];
            $this->priority = $R['priority'];
        }
    }

    public static function create($initialValues) {
        $project = $initialValues['project'];
        $parentTask = is_numeric($initialValues['parentTask']) ? $initialValues['parentTask'] : null;
        $DBH = System::getInstance()->getDBH();
        $Q = $DBH->prepare('INSERT INTO ' . System::TABLE_TASKS . ' (parentTask, dateCreated) VALUES (:pt, :dc)');
        $Q->bindValue(':pt', $parentTask);
        $Q->bindValue(':dc', time());
        $Q->execute();
        $newId = $DBH->lastInsertId();

        $Q = $DBH->exec('INSERT INTO ' . System::TABLE_PROJECT_TASKS . ' (project,task) VALUES (' . $project . ', ' . $newId . ');');

        return new self($newId);
    }

    public static function exists($id) {
        $DBH = System::getInstance()->getDBH();
        $Q = $DBH->prepare('SELECT 1 FROM ' . System::TABLE_TASKS . ' WHERE id_t=:i LIMIT 1');
        $Q->bindValue(':i', $id, PDO::PARAM_INT);
        $Q->execute();
        return $Q->rowCount() ? true : false;
    }

    public function update() {
        $DBH = System::getInstance()->getDBH();
        $Q = $DBH->prepare('UPDATE ' . System::TABLE_TASKS . ' SET name=:n, description=:d, dateFinished=:dc, deadline=:dl, points=:pts, priority=:prio WHERE id=:id');
        $Q->bindValue(':id', $this->id, PDO::PARAM_INT);
        $Q->bindValue(':n', $this->name, PDO::PARAM_STR);
        $Q->bindValue(':d', $this->description, PDO::PARAM_STR);
        $Q->bindValue(':dc', $this->dateFinished, PDO::PARAM_INT);
        $Q->bindValue(':dl', $this->deadline, PDO::PARAM_INT);
        $Q->bindValue(':pts', $this->points, PDO::PARAM_INT);
        $Q->bindValue(':prio', $this->priority, PDO::PARAM_INT);
        $Q->execute();
    }

    public function isDeletable() {
        return true;
    }

    public function delete() {
        $DBH = System::getInstance()->getDBH();
        $DBH->exec('DELETE FROM ' . System::TABLE_TASKS . ' WHERE id=' . $this->id);
        $DBH->exec('DELETE FROM ' . System::TABLE_PROJECT_TASKS . ' WHERE task=' . $this->id);
        foreach ($this->getSubTasks() as $sub) {
            $sub->delete();
        }
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

    public function getDateFinished() {
        return $this->dateFinished;
    }

    public function setDateFinished($dateFinished) {
        $this->dateFinished = $dateFinished;
        return $this;
    }

    public function getDeadline() {
        return $this->deadline;
    }

    public function setDeadline($deadline) {
        $this->deadline = $deadline;
        return $this;
    }

    public function getPoints() {
        return $this->points;
    }

    public function setPoints($points) {
        $this->points = $points;
        return $this;
    }

    public function getPriority() {
        return $this->priority;
    }

    public function setPriority($priority) {
        $this->priority = $priority;
        return $this;
    }

//    public function getUser() {
//        return $this->user;
//    }

    public function getDateCreated() {
        return $this->dateCreated;
    }

    public function getProject() {
        $dbh = System::getInstance()->getDBH();
        $r = $dbh->query('select *, p.id as pid from ' . System::TABLE_PROJECTS . ' p join ' . System::TABLE_PROJECT_TASKS . ' pt on p.id=pt.project where pt.task=' . $this->getId())->fetch();
        return new Project($r['pid']);
    }

    public function getParentTask() {
        return $this->parentTask;
    }

    public function hasSubTasks() {
        $dbh = System::getInstance()->getDBH();
        $q = $dbh->query('select id from ' . System::TABLE_TASKS . ' where parentTask is not null && parentTask = ' . $this->id);
        return $q->rowCount() ? true : false;
    }

    public function getSubTasks() {
        $ret = array();
        $dbh = System::getInstance()->getDBH();
        foreach ($dbh->query('select id from ' . System::TABLE_TASKS . ' where parentTask is not null && parentTask = ' . $this->id) as $r) {
            $ret[] = new Task($r['id']);
        }
        return $ret;
    }

}

