<?php

namespace App\DataFixtures;

use Faker\Factory;
use Faker\Generator;
use App\Entity\Product;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        // use the factory to create a Faker\Generator instance
        /** @var  Generator*/
        $faker = Factory::create('fr_FR');

        /**création des produits */
        for ($i=0; $i < 500; $i++) { 
            $product = new Product;
            $product->setTitle($faker->sentence(3))
                    ->setDescription($faker->paragraph(5))
                    ->setPrice($faker->randomNumber(4))
                    ->setCity($faker->city())
                    ->setPostalCode($faker->postcode())
                    ->setCreatedAt(new DateTimeImmutable())
                    ;
            $manager->persist($product);
        }

        /**création des users */
        $user = new User;
        $admin = new User;

        $userPassword = $this->hasher->hashPassword($user, 'jean64');
        $adminPassword = $this->hasher->hashPassword($admin, 'admin64');

        $admin->setUsername('admin')
                ->setPassword($adminPassword)
                ->setEmail('admin@lebongroin.fr')
                ->setRoles(['ROLE_ADMIN'])
                ;
        
        $user->setUsername('jean')
                ->setPassword($userPassword)
                ->setEmail('jean@gmail.com')
                ;
        $manager->persist($admin);
        $manager->persist($user);


        $manager->flush();
    }
}
