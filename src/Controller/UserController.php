<?php

namespace App\Controller;

use App\Model\UserManager;

class UserController extends AbstractController
{
    public const MAX_UPLOAD_SIZE = 5000000;
    public const EXTENSIONS_ALLOWED = ['jpg', 'jpeg', 'png', 'gif'];

    private UserManager $manager;

    public function __construct()
    {
        parent::__construct();
        $this->manager = new UserManager();
    }

    public function register(): string
    {
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = array_map('htmlentities', array_map('trim', $_POST));

            $targetDir = 'avatars/';
            $targetFile = $targetDir . basename($_FILES['avatar']['name']);
            $typeFile = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
            $dimensionsImage = getimagesize($_FILES['avatar']['tmp_name']);

            if (!$dimensionsImage) {
                $errors[] = 'Votre avatar n\'est pas une image';
            }

            if ($_FILES['avatar']['size'] > self::MAX_UPLOAD_SIZE) {
                $errors[] = 'Votre avatar ne peut pas dépasser 5Mo';
            }

            if (!in_array($typeFile, self::EXTENSIONS_ALLOWED)) {
                $errors[] = 'Votre avatar n\'as pas le bon format (' . implode(', ', self::EXTENSIONS_ALLOWED) . ')';
            }

            if (file_exists($targetFile) || !move_uploaded_file($_FILES['avatar']['tmp_name'], $targetFile)) {
                $errors[] = 'Votre avatar n\'a pas pu être ajouté';
            }

            if (empty($data['firstname'])) {
                $errors[] = 'Il manque le prénom';
            }

            if (empty($data['lastname'])) {
                $errors[] = 'Il manque le nom';
            }

            if (empty($data['pseudo'])) {
                $errors[] = 'Il manque le pseudo';
            }

            $user = $this->manager->selectOneByPseudo($data['pseudo']);

            if ($user) {
                $errors[] = 'Ce pseudo existe déjà';
            }

            if (empty($data['password'])) {
                $errors[] = 'Il manque le mot de passe';
            } elseif ($data['password'] !== $data['password_confirmation']) {
                $errors[] = 'Apprends à écrire';
            }

            if (empty($errors)) {
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
                $data['avatar'] = basename($_FILES['avatar']['name']);

                $userId = $this->manager->insert($data);

                $_SESSION['user_id'] = $userId;

                header('Location: /');
                exit();
            }
        }

        return $this->twig->render('register.html.twig', [
            'errors' => $errors
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
