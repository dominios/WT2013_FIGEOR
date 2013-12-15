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
    private $attachments;

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
            $this->attachments = $this->fetchAttachments();
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
        $Q = $DBH->prepare('SELECT 1 FROM ' . System::TABLE_TASKS . ' WHERE id=:i LIMIT 1');
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

    public function getDateFinished($format = null) {
        return $format === null ? $this->dateFinished : date($format, $this->dateFinished);
    }

    public function isFinished() {
        return is_numeric($this->dateFinished) ? true : false;
    }

    public function isFinishable() {
        $finishable = true;
        if ($this->hasSubTasks()) {
            foreach ($this->getSubTasks() as $sub) {
                $finishable &= $sub->isFinished();
            }
        }
        return $finishable;
    }

    public function setDateFinished($dateFinished) {
        $this->dateFinished = $dateFinished;
        return $this;
    }

    public function getDeadline($format = null) {
        return $format === null ? $this->deadline : date($format, $this->deadline);
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

    public function getUser() {
        $dbh = System::getInstance()->getDBH();
        $r = $dbh->query('SELECT u.id AS uid
            FROM ' . System::TABLE_USERS . ' u
            JOIN ' . System::TABLE_USER_PROJECTS . ' up ON u.id = up.user
            JOIN ' . System::TABLE_PROJECT_TASKS . ' pt ON up.project = pt.task
            WHERE pt.task = ' . $this->id . ';')->fetch();
        return new User($r['uid']);
    }

    public function getDateCreated() {
        return $this->dateCreated;
    }

    public function getProject() {
        $dbh = System::getInstance()->getDBH();
        $r = $dbh->query('SELECT p.id AS pid
            FROM ' . System::TABLE_PROJECTS . ' p
            JOIN ' . System::TABLE_PROJECT_TASKS . ' pt ON p.id = pt.project
            WHERE pt.task = ' . $this->getId() . ';')->fetch();
        return new Project($r['pid']);
    }

    public function getParentTask() {
        return $this->parentTask;
    }

    public function hasSubTasks() {
        $dbh = System::getInstance()->getDBH();
        $q = $dbh->query('SELECT id
            FROM ' . System::TABLE_TASKS . '
            WHERE parentTask IS NOT NULL && parentTask = ' . $this->id . ';');
        return $q->rowCount() ? true : false;
    }

    public function getSubTasks() {
        $ret = array();
        $dbh = System::getInstance()->getDBH();
        foreach ($dbh->query('SELECT id
            FROM ' . System::TABLE_TASKS . '
            WHERE parentTask IS NOT NULL && parentTask = ' . $this->id . ';') as $r) {
            $ret[] = new Task($r['id']);
        }
        return $ret;
    }

    public function fetchAttachments() {
        $ret = array();
        $dbh = System::getInstance()->getDBH();
        foreach ($dbh->query('SELECT a.id FROM ' . System::TABLE_ATTACHMENTS . ' a
            JOIN ' . System::TABLE_TASK_ATTACHMENTS . ' ta ON a.id = ta.file
            WHERE ta.task = ' . $this->getId() . ';') as $r) {
            $ret[] = new Attachment($r['id']);
        }
        return $ret;
    }

    public function getAttachments() {
        return $this->attachments;
    }

    public function getFinishDay() {
        if (!$this->isFinished()) {
            return null;
        }
        $project = $this->getProject();

        $dStart = new \DateTime("@" . $project->getDateCreated());
        $dEnd = new \DateTime("@" . $this->dateFinished);
        $dDiff = $dStart->diff($dEnd);
        return $dDiff->days;
    }

}

