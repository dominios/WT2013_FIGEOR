<?php

namespace Figeor\Controller;

use Figeor\Core\View;
use Figeor\Models\Task;
use Figeor\Models\Project;
use Figeor\Core\System;
use Figeor\Models\Attachment;

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

    protected function delete() {
        if (isset($_GET['id'])) {
            $task = new Task($_GET['id']);
            $project = $task->getProject();
            $task->delete();
        }
        $this->redirect('/projects/view/' . $project->getId());
    }

    protected function addAttachment() {
        $view = new View('tasks/addAttachment.php');
        $ret = array();
        $ret['title'] = 'Upload prílohy';
        $ret['main'] = $view->renderToString();
        return $ret;
    }

    protected function attachmentSubmit() {
        if ($_FILES["fileUrl"]["error"] > 0) {
            echo "Error: " . $_FILES["fileUrl"]["error"] . "<br>";
            die;
        } else {
            $initials = array('files' => $_FILES, 'task' => $_POST['taskId'], 'fileName' => $_POST['fileName']);
            Attachment::create($initials);
            $task = new Task($_POST['taskId']);
            $project = $task->getProject();
            $this->redirect('/projects/view/' . $project->getId());
        }
    }

}
