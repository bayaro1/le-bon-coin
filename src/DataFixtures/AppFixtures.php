<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
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
        
        $manager->persist($admin);
        
        $user->setUsername('jean')
                ->setPassword($userPassword)
                ->setEmail('jean@gmail.com')
                ;
        $manager->persist($user);

        $users = [];
        for ($i=0; $i < 20; $i++) { 
            $user = new User;
            $user->setPassword($this->hasher->hashPassword($user, 'password'))
                ->setUsername($faker->name())
                ->setEmail($faker->email())
                ;
            $users[] = $user;
            $manager->persist($user);
        }
        $manager->flush();

        /**création des catégories */
        $categories = [];
        $category = new Category;
        $category->setName('Vacances');
        $manager->persist($category);
        $categories[] = $category;
        $category = new Category;
        $category->setName('Emploi');
        $manager->persist($category);
        $categories[] = $category;
        $category = new Category;
        $category->setName('Véhicules');
        $manager->persist($category);
        $categories[] = $category;
        $category = new Category;
        $category->setName('Immobilier');
        $manager->persist($category);
        $categories[] = $category;
        $category = new Category;
        $category->setName('Mode');
        $manager->persist($category);
        $categories[] = $category;
        $category = new Category;
        $category->setName('Maison');
        $manager->persist($category);
        $categories[] = $category;
        $category = new Category;
        $category->setName('Multimédia');
        $manager->persist($category);
        $categories[] = $category;
        $category = new Category;
        $category->setName('Loisirs');
        $manager->persist($category);
        $categories[] = $category;
        $category = new Category;
        $category->setName('Animaux');
        $manager->persist($category);
        $categories[] = $category;
        $category = new Category;
        $category->setName('Matériel Professionnel');
        $manager->persist($category);
        $categories[] = $category;
        $category = new Category;
        $category->setName('Services');
        $manager->persist($category);
        $categories[] = $category;
        $category = new Category;
        $category->setName('Divers');
        $manager->persist($category);
        $categories[] = $category;

        /**création des produits */
        $products = [];
        for ($i=0; $i < 100; $i++) { 
            $product = new Product;
            $manager->persist($product);
            $product->setTitle($faker->sentence(3))
                    ->setDescription($faker->paragraph(5))
                    ->setPrice($faker->randomNumber(4))
                    ->setCity($faker->city())
                    ->setPostalCode($faker->postcode())
                    ->setCreatedAt(new DateTimeImmutable())
                    ->setCategory($faker->randomElement($categories))
                    ->setUser($faker->randomElement($users))
                    ;
            
            $products[] = $product;
        }

        /**création des commentaires */

        for($i=0; $i<4000; $i++)
        {
            $comment = new Comment;
            $manager->persist($comment);
            $comment->setUser($faker->randomElement($users))
                    ->setProduct($faker->randomElement($products))
                    ->setContent($faker->paragraph(random_int(1, 5)))
                    ->setCreatedAt(new DateTimeImmutable)
                    ;
        }





        /**flush final */
        $manager->flush();
    }
}
