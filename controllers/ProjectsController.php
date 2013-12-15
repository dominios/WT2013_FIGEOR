<?php

namespace Figeor\Controller;

use Figeor\Core\System;
use Figeor\Core\View;
use Figeor\Models\User;
use Figeor\Models\Project;

class ProjectsController extends AbstractController {

    protected function view() {
        if (isset($_GET['id'])) {
            $view = new View('projects/detail.php');
            $project = new Project($_GET['id']);
            $view->project = $project;
            $view->tasks = $project->getTasks();
            $ret = array();
            $ret['title'] = $project->getName();
            $ret['main'] = $view->renderToString();
            return $ret;
        } else {
            $view = new View('tasks/list.php');
            $ret = array();
            $ret['title'] = 'Zoznam všetkých úloh na ' . $_GET['days'] . ' dní';
            $ret['main'] = $view->renderToString();
            return $ret;
        }
    }

    protected function newForm() {
        $view = new View('tasks/newForm.php');
        $ret = array();
        $ret['title'] = 'Pridať úlohu';
        $ret['main'] = $view->renderToString();
        return $ret;
    }

    protected function admin() {
        $view = new View('projects/admin.php');
        $view->projects = Project::fetchByUser(System::currentUser());
        $ret = array();
        $ret['title'] = 'Všetky projekty';
        $ret['main'] = $view->renderToString();
        return $ret;
    }

    protected function edit() {
        if (!Project::exists($_GET['id'])) {
            $this->redirect('/projects/admin');
        }
        $project = new Project($_GET['id']);
        $view = new View('projects/form.php');
        $view->project = $project;
        $ret = array();
        $ret['title'] = 'Upraviť projekt';
        $ret['main'] = $view->renderToString();
        return $ret;
    }

    protected function update() {
        if (!Project::exists($_POST['projectId'])) {
            $this->redirect('/projects/admin');
        }
        $project = new Project($_POST['projectId']);
        $project->setName($_POST['projectName']);
        $project->setDescription($_POST['projectDescription']);
        $project->setDeadline(strtotime($_POST['projectDeadline']));
        $project->update();
        $this->redirect('/projects/admin');
    }

    protected function create() {
        $project = Project::create(array('name' => 'Nový projekt'));
        $this->redirect('/projects/edit/' . $project->getId());
    }

}
