<?php

namespace App\Controller;

use App\Model\TaskManager;

class TaskController extends AbstractController
{
    private TaskManager $manager;

    public function __construct()
    {
        parent::__construct();
        $this->manager = new TaskManager();
    }

    /**
     * Add task
     */
    public function add(): string
    {
        if (!empty($_SESSION['user_id'])) {
            echo 'Mon USER : ' . $_SESSION['user_id'];
        } else {
            header('Location: /login');
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $task = array_map('htmlentities', array_map('trim', $_POST));

            if (empty($task['label'])) {
                $errors['label'] = 'Enter a label';
            }

            if (empty($task['priority'])) {
                $errors['priority'] = 'Enter a priority';
            }

            if (empty($errors)) {
                $task['status'] = TaskManager::STATUS_PENDING;

                $this->manager->insert($task);
            }
        }

        return $this->twig->render('Task/add.html.twig', [
            'errors' => $errors,
        ]);
    }
}
