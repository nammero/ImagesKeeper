<?php

namespace App\DataFixtures;

use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Exception;

class FakeUser extends Fixture
{
    /**
     * @param ObjectManager $manager
     *
     * @throws Exception
     */
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail('admin@admin.com');
        $user->setIsActive(1);
        $user->setRegistrationDate(new DateTime());
        $user->setPassword('qwer');
        $user->setRoles(['admin']);
        $manager->persist($user);

        $manager->flush();
    }
}
