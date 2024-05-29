<?php

namespace App\Controller;

use App\Model\UserManager;

class UserController extends AbstractController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Log to the application
     */
    public function login(): string
    {
        return $this->twig->render('login.html.twig', []);
    }
}
