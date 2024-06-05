<?php

namespace App\Controller;

use App\Model\UserManager;

class UserController extends AbstractController
{
    private UserManager $manager;

    public function __construct()
    {
        parent::__construct();
        $this->manager = new UserManager();
    }

    public function register(): string
    {
        return $this->twig->render('register.html.twig', [

        ]);
    }

    /**
     * Log to the application
     */
    public function login(): string
    {
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = array_map('htmlentities', array_map('trim', $_POST));

            $user = $this->manager->selectOneByPseudo($data['pseudo']);

            if (empty($user)) {
                $errors[] = 'Aucun utilisateur ne porte ce pseudo';
            } else {
                if (password_verify($data['password'], $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];

                    header('Location: /');
                } else {
                    $errors[] = 'Le mot de passe ne correspond pas';
                }
            }
        }

        return $this->twig->render('login.html.twig', ['errors' => $errors]);
    }

    public function logout(): void
    {
        unset($_SESSION['user_id']);

        header('Location: /');
    }
}
