<?php

namespace Figeor\Controller;

use Figeor\Core\View;
use Figeor\Models\Task;
use Figeor\Models\Project;
use Figeor\Core\System;

class TasksController extends AbstractController {

    protected function view() {
        if (isset($_GET['id'])) {
            $view = new View('tasks/view.php');
            $ret = array();
            $ret['title'] = 'Detail úlohy';
            $ret['main'] = $view->renderToString();
            return $ret;
        }
    }

    protected function add() {
        $view = new View('tasks/form.php');
        $view->task = null;
        $ret = array();
        $ret['title'] = 'Pridať úlohu';
        $ret['main'] = $view->renderToString();
        return $ret;
    }

    protected function edit() {
        $view = new View('tasks/form.php');
        $task = new \Figeor\Models\Task($_GET['id']);
        $view->task = $task;
        $ret = array();
        $ret['title'] = 'Detail úlohy';
        $ret['main'] = $view->renderToString();
        return $ret;
    }

    protected function submitForm() {

        if (isset($_POST['taskId'])) {
            $task = new Task($_POST['taskId']);
        } else {
            $initialValues = array();
            $initialValues['project'] = $_POST['taskProject'];
            if (isset($_POST['parentTask'])) {
                $initialValues['parentTask'] = $_POST['parentTask'];
            }
            $task = Task::create($initialValues);
        }
        $task->setDeadline($_POST['taskDeadline']);
        $task->setName($_POST['taskName']);
        $task->setDescription($_POST['taskDescription']);
        $task->setPoints($_POST['taskPoints']);
        $task->setPriority($_POST['taskPriority']);
        $task->update();
        $this->redirect('/tasks/edit/' . $task->getId());
    }

}
