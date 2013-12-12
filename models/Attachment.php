<?php

namespace Figeor\Models;

use Figeor\Core\System;
use \PDO;

class Attachment implements IModel {

    private $id;
    private $user;
    private $task;
    private $name;
    private $url;

    public function __construct($id) {
        $DBH = System::getInstance()->getDBH();
        $Q = $DBH->prepare('SELECT * FROM ' . System::TABLE_ATTACHMENTS . ' WHERE id=:i LIMIT 1');
        $Q->bindValue(':i', $id, PDO::PARAM_INT);
        $Q->execute();
        if ($Q->rowCount()) {
            $R = $Q->fetch();
            $this->name = $R['name'];
            $this->url = $R['url'];
        }
    }

    public static function create($initialValues) {

        $file = $initialValues['files']['fileUrl'];
        $dirname = 'data/' . System::currentUser()->getId();
        if (!file_exists($dirname)) {
            mkdir($dirname);
        }
        $files = scandir($dirname, 1);
        $files = array_diff($files, array('.', '..'));
        $newName = 1 + (int) preg_replace('/\..*$/', '', max($files));
        $newUrl = $dirname . '/' . $newName . '.' . self::fileExtension($file['name']);
        move_uploaded_file($file["tmp_name"], $newUrl);

        $DBH = System::getInstance()->getDBH();
        $Q = $DBH->prepare('INSERT INTO ' . System::TABLE_ATTACHMENTS . ' (name, url) VALUES (:n, :u)');
        $Q->bindValue(':n', $initialValues['fileName'], PDO::PARAM_STR);
        $Q->bindValue(':u', $newUrl, PDO::PARAM_STR);
        $Q->execute();

        $newId = $DBH->lastInsertId();

        $task = $initialValues['task'];
        $DBH->exec('INSERT INTO ' . System::TABLE_TASK_ATTACHMENTS . ' (task,file) VALUES (' . $task . ', ' . $newId . ');');

        return new self($newId);
    }

    private static function fileExtension($fileName) {
        $ex = explode('.', $fileName);
        return end($ex);
    }

    public static function exists($id) {
        $DBH = System::getInstance()->getDBH();
        $Q = $DBH->prepare('SELECT 1 FROM ' . System::TABLE_ATTACHMENTS . ' WHERE id_f=:i LIMIT 1');
        $Q->bindValue(':i', $id, PDO::PARAM_INT);
        $Q->execute();
        return $Q->rowCount() ? true : false;
    }

    public function update() {
        $DBH = System::getInstance()->getDBH();
        $Q = $DBH->prepare('UPDATE ' . System::TABLE_ATTACHMENTS . ' SET name=:n, url=:url WHERE id=:id');
        $Q->bindValue(':id', $this->id, PDO::PARAM_INT);
        $Q->bindValue(':n', $this->name, PDO::PARAM_STR);
        $Q->bindValue(':url', $this->url, PDO::PARAM_STR);
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

//    public function getUser() {
//        return $this->user;
//    }
//    public function getTask() {
//        return $this->task;
//    }

    public function getUrl() {
        return $this->url;
    }

}

