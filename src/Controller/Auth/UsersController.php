<?php

namespace App\Controller\Auth;

use App\Entity\Users;
use App\Entity\Role;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class UsersController extends AbstractController
{
    #[Route('/account/new', name: 'account')]
    #[Route('/account/edit/{id}', name: 'account-edit')]
    public function accountForm(
        Request $request,
        EntityManagerInterface $entityManager,
        SessionInterface $session
    ): Response {

        $update = false;

        if ($request->attributes->get('_route') === 'account-edit') {

            $filter = $session->get('filter');
            $idRoleUser = $filter['idRole'];
            $idUser = $filter['idUser'];

            if ($idRoleUser == 1 || $idUser == $request->attributes->get('id')) {

                $user = $entityManager
                    ->getRepository(Users::class)
                    ->find($request->attributes->get('id'));

            } else {
                return $this->redirectToRoute('index');
            }

            $update = true;

        } else {
            $user = new Users();
        }

        $userForm = $this->createFormBuilder($user)
            ->add('username', TextType::class)
            ->add('password', PasswordType::class)
            ->add('mail', EmailType::class)
            ->add('role', EntityType::class, [
                'class' => Role::class,
                'choice_label' => 'name',
            ])
            ->getForm();

        if ($update) {
            $userForm->remove('password');
        }

        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {

            $user = $userForm->getData();

            if (!$update) {
                $passHashed = password_hash($user->getPassword(), PASSWORD_BCRYPT);
                $user->setPassword($passHashed);
            } else {
                $filter = $session->get('filter');
                $idRole = $filter['idRole'];

                if ($user->getRole() == null) {
                    $user->setRole(
                        $entityManager->getRepository(Role::class)->find($idRole)
                    );
                }
            }

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('index');
        }

        return $this->render('View/auth/account.html.twig', [
            'formAccount' => $userForm->createView(),
            'editMode'    => $update,
        ]);
    }
}
