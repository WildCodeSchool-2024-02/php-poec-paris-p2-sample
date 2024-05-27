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

        return $this->twig->render('Task/add.html.twig', [
            'errors' => $errors,
        ]);
    }
}
