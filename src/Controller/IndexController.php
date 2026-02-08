<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends AbstractController
{
    private string $title = "Bienvenue sur la premiere page";

    public function index(): Response
    {
        return $this->render('View/index.html.twig', [
            'title' => $this->title,
            'age'   => 18
        ]);
    }
}
