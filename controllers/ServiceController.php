<?php

namespace Figeor\Controller;

use Figeor\Core\System;
use Figeor\Models\User;

class ServiceController extends AbstractController {

    protected function getalltasks() {
        $dbh = System::getInstance()->getDBH();
        $user = User::getByEmail($_GET['email']);
        $tasks = $user->getTasksByDays(isset($_GET['days']) ? $_GET['days'] : 30);
        $ret = array();
        foreach ($tasks as $task) {
            $t['name'] = $task->getName();
            $t['deadline'] = $task->getDeadline('d.m.Y H:i:s');
            $t['description'] = $task->getDescription();
            $t['priority'] = $task->getPriority();
            $ret[] = $t;
        }
        echo json_encode($ret);
        die;
    }

}

