<?php

namespace App\Controller\Auth;

use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class LoginController extends AbstractController
{
    private string $title = 'Connexion';

    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(
        Request $request,
        EntityManagerInterface $manager,
        SessionInterface $session
    ): Response
    {
        $req = $request->request;

        if (
            $req->count() > 0 &&
            $req->get('username') !== null &&
            $req->get('password') !== null
        ) {

            $username = trim((string)$req->get('username'));
            $password = (string)$req->get('password');

            $user = $manager->getRepository(Users::class)
                ->findOneBy(['username' => $username]);

            if ($user !== null && password_verify($password, $user->getPassword())) {

                $this->title = "Bienvenue " . $user->getUsername() . " sur la première page";

                // SESSION FILTER (comme syllabus)
                $session->set('filter', [
                    'idRole'   => $user->getRole()->getId(),
                    'username' => $user->getUsername(),
                    'idUser'   => $user->getId(),
                ]);

                return $this->render('View/index.html.twig', [
                    'title' => $this->title,
                ]);
            }
        }

        return $this->render('View/index.html.twig', [
            'title'      => $this->title,
            'errorLogin' => 'Error Login/Password',
        ]);
    }

    #[Route('/logout', name: 'logout')]
    public function logout(SessionInterface $session): Response
    {
        $session->remove('filter');

        return $this->render('View/index.html.twig', [
            'title' => 'Bienvenue sur la première page',
        ]);
    }
}
