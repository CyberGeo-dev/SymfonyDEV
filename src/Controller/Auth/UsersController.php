<?php

namespace App\Controller\Auth;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UsersController extends AbstractController
{
    #[Route('/account/new', name: 'account')]
    public function accountForm(Request $request, EntityManagerInterface $manager): Response
    {
        return $this->render('View/auth/account.html.twig');
    }
}
