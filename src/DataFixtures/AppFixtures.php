<?php

namespace App\DataFixtures;

use Faker\Factory;
use Faker\Generator;
use App\Entity\Product;
use DateTimeImmutable;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // use the factory to create a Faker\Generator instance
        /** @var  Generator*/
        $faker = Factory::create('fr_FR');


        for ($i=0; $i < 50; $i++) { 
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
        $manager->flush();
    }
}
