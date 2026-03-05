<?php

namespace App\Controller\Auth;

use App\Entity\Role;
use App\Entity\Users;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

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

        $filter        = $session->get('filter') ?? [];
        $idRoleSession = $filter['idRole'] ?? null;
        $idUserSession = $filter['idUser'] ?? null;

        // -------------------------
        // MODE EDIT
        // -------------------------
        if ($request->attributes->get('_route') === 'account-edit') {

            $idToEdit = (int) $request->attributes->get('id');

            if ($idRoleSession == 1 || $idUserSession == $idToEdit) {

                $user = $entityManager->getRepository(Users::class)->find($idToEdit);

                if (!$user) {
                    return $this->redirectToRoute('index');
                }

                $update = true;

            } else {
                return $this->redirectToRoute('index');
            }

        } else {
            // -------------------------
            // MODE NEW
            // -------------------------
            $user = new Users();
        }

        // =========================
        // VALIDATION GROUPS (le point clé)
        // =========================
        if ($update) {
            $userForm = $this->createForm(UserType::class, $user, [
                'validation_groups' => ['Default'],
            ]);

            $userForm->remove('password');
            $userForm->remove('confirm_password');
        } else {
            $userForm = $this->createForm(UserType::class, $user, [
                'validation_groups' => ['registration'],
            ]);
        }

        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {

            /** @var Users $user */
            $user = $userForm->getData();

            if (!$update) {
                // Hash du mot de passe (en création)
                $passHashed = password_hash((string) $user->getPassword(), PASSWORD_BCRYPT);
                $user->setPassword($passHashed);

                // Optionnel : vider confirm_password
                $user->setConfirmPassword(null);

            } else {
                // Si role disabled => revient null => on remet le role de session
                if ($user->getRole() === null && $idRoleSession) {
                    $user->setRole(
                        $entityManager->getRepository(Role::class)->find($idRoleSession)
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
            'idRole'      => $idRoleSession,
        ]);
    }

    #[Route('/account/check', name: 'check', methods: ['POST'])]
    public function checkUserExist(
        Request $request,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $username = (string) $request->request->get('username', '');

        $user = $entityManager
            ->getRepository(Users::class)
            ->findOneByLowerUsername($username);

        $encoders    = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer  = new Serializer($normalizers, $encoders);

        $json = $serializer->serialize(
            $user,
            'json',
            [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                }
            ]
        );

        return new JsonResponse($json, 200, [], true);
    }
}