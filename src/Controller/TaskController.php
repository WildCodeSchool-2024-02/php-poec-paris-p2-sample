<?php

namespace App\Controller;

use App\Model\TaskManager;

class TaskController extends AbstractController
{
    private TaskManager $taskManager;

    public function __construct()
    {
        parent::__construct();
        $this->taskManager = new TaskManager();
    }

    /**
     * Add task
     */
    public function add(): string
    {
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

                $this->taskManager->insert($task);
            }
        }

        return $this->twig->render('Task/add.html.twig', [
            'errors' => $errors,
        ]);
    }
}
