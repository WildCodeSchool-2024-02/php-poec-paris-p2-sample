<?php

namespace App\Controller;

use App\Model\TaskManager;
use App\Model\UserManager;

class TaskController extends AbstractController
{
    private TaskManager $manager;
    private UserManager $userManager;

    public function __construct()
    {
        parent::__construct();
        $this->manager = new TaskManager();
        $this->userManager = new UserManager();
    }

    /**
     * Add task
     */
    public function add(): string
    {
        if (empty($_SESSION['user_id'])) {
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
                $task['user_id'] = $_SESSION['user_id'];

                $this->manager->insert($task);
            }
        }

        return $this->twig->render('Task/add.html.twig', [
            'user' => $this->userManager->selectOneById($_SESSION['user_id']),
            'errors' => $errors,
        ]);
    }
}
