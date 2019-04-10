<?php

namespace App\DataFixtures;

use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Exception;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class FakeUser extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

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
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'qwerty'));
        $user->setRoles(['ROLE_ADMIN']);
        $manager->persist($user);

        $manager->flush();
    }
}
