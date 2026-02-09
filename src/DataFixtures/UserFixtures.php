<?php

namespace App\DataFixtures;

use App\Entity\Role;
use App\Entity\Users;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Roles
        $role = new Role();
        $role->setName('Admin');
        $manager->persist($role);

        $role2 = new Role();
        $role2->setName('User');
        $manager->persist($role2);

        // Users
        $usernames = ['Geoffrey', 'Test', 'Toto'];

        foreach ($usernames as $key => $value) {
            $user = new Users();
            $user->setUsername($value);
            $user->setPassword(password_hash('test', PASSWORD_BCRYPT));

            if ($key === 0) {
                $user->setRelation($role);
            } else {
                $user->setRelation($role2);
            }

            $manager->persist($user);
        }

        $manager->flush();
    }
}
