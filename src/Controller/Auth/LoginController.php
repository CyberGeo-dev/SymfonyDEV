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
    public function login(Request $request, EntityManagerInterface $manager, SessionInterface $session): Response
    {
        $req = $request->request;

        // Comme le prof : on vérifie qu'on a bien username + password
        if ($req->count() > 0 && $req->get('username') !== null && $req->get('password') !== null) {

            $username = $req->get('username');
            $password = $req->get('password');

            // Adapté à ton projet : Entity = Users
            $user = $manager->getRepository(Users::class)->findOneBy(['username' => $username]);

            // Comme le prof : si user existe et password ok
            if ($user !== null && password_verify($password, $user->getPassword())) {

                $this->title = "Bienvenue " . $user->getUsername() . " sur la première page";

                // Comme le prof : on stocke dans la session un tableau "filter"
                $session->set('filter', [
                    'idRole'   => $user->getRelation()->getId(), // relation = Role
                    'username' => $user->getUsername(),
                    'idUser'   => $user->getId(),
                ]);

                // Comme le prof : on retourne sur la page index (home)
                return $this->render('view/index.html.twig', [
                    'title' => $this->title,
                ]);
            }
        }

        // Comme le prof : erreur login/password => on renvoie index avec errorLogin
        return $this->render('view/index.html.twig', [
            'title'      => $this->title,
            'errorLogin' => 'Error Login/Password',
        ]);
    }
}
