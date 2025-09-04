<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture  implements DependentFixtureInterface
{
    public const USER_REFERENCE = 'user';

    public function __construct(private readonly UserPasswordHasherInterface $userPasswordHasher)
    {
    }
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $admin = new User();
        $admin->setNom('Doe')
            ->setPrenom('John')
            ->setPseudo('Adminator')
            ->setTelephone('07 14 57 84 77')
            ->setEmail('john.doe@gmail.com')
            ->setPassword($this->userPasswordHasher->hashPassword($admin, 'password'))
            ->setRoles(['ROLE_ADMIN']);

        $campus = $this->getReference(CampusFixtures::CAMPUS_REFERENCE . '1', Campus::class);
        $admin->setCampus($campus);

        $manager->persist($admin);

        for($i = 0; $i < 50; $i++) {
            $user = new User();
            $user->setNom($faker->lastName())
                ->setPrenom($faker->firstName())
                ->setPseudo($faker->userName())
                ->setTelephone($faker->phoneNumber())
                ->setEmail($faker->email())
                ->setPassword($this->userPasswordHasher->hashPassword($user, $faker->password()));

            $categoryName = CampusFixtures::CAMPUS_REFERENCE . $faker->numberBetween(0, 2);
            $campus = $this->getReference($categoryName, Campus::class);
            $user->setCampus($campus);

            $manager->persist($user);
            $this->addReference(self::USER_REFERENCE . $i, $user);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CampusFixtures::class,
        ];
    }
}
