<?php

namespace App\DataFixtures;

use App\Entity\Contact;
use App\Entity\Ingredient;
use App\Entity\Mark;
use App\Entity\Recipe;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    /**
     * @var Generator
     */
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }
    public function load(ObjectManager $manager): void
    {
        $users = [];

        $admin = new User();
        $admin->setFullName('Administrateur de CommunityKitchen');
        $admin->setPseudo(null);
        $admin->setEmail('admin@communitykitchen.fr');
        $admin->setRoles(['ROLE_USER','ROLE_ADMIN']);
        $admin->setPlainPassword('password');

        $users[] = $admin;
        $manager->persist($admin);
        //Users
        for ($l = 0; $l < 10; $l++) {
            $user = new User();
            $user->setFullName($this->faker->name)
                ->setPseudo(mt_rand(0,1) === 1 ? $this->faker->firstName : null)
                ->setEmail($this->faker->email)
                ->setRoles(['ROLE_USER'])
                ->setPlainPassword('password');

            $users[] = $user;
            $manager->persist($user);
        }

        //ingredients
        $ingredients = [];
        for ($i = 0; $i < 50; $i++) {
            $ingredient = new Ingredient();
            $ingredient->setName($this->faker->word())
            ->setPrice(mt_rand(0, 100))
            ->setUser($users[mt_rand(0, count($users) - 1)]);


            $ingredients[] = $ingredient;
            $manager->persist($ingredient);
        }

        //recipes
        $recipes = [];
        for ($j = 0; $j < 50; $j++) {
            $recipe = new Recipe();
            $recipe->setName($this->faker->word())
           ->setTime(mt_rand(0, 1) == 1 ? mt_rand(1, 1440) : null)
            ->setNbPeople(mt_rand(0, 1) == 1 ? mt_rand(1, 50) : null)
          ->setDifficulty(mt_rand(0, 1) == 1 ? mt_rand(1, 5) : null)
                ->setDescription($this->faker->text(300))
            ->setPrice(mt_rand(0, 1) == 1 ? mt_rand(1, 1000) : null)
          ->setIsFavorite(mt_rand(0, 1) == 1 ? true : false)
                ->setIsPublic(mt_rand(0, 1) == 1 ? true : false)
                ->setUser($users[mt_rand(0, count($users) - 1)]);
            for ($k = 0; $k < mt_rand(5, 15); $k++) {
                $recipe->addIngredient($ingredients[mt_rand(0, count($ingredients) - 1)]);
            }
            $recipes[] = $recipe;
            $manager->persist($recipe);
        }

        //Marks
        foreach ($recipes as $recipe) {
            for ($m = 0; $m < mt_rand(0, 4); $m++) {
                $mark = new Mark();
                $mark->setUser($users[mt_rand(0, count($users) - 1)])
                    ->setRecipe($recipe)
                    ->setMark(mt_rand(1, 5));
                $manager->persist($mark);
            }
        }

        // Contact
        for ($n = 0; $n < 5; $n++) {
            $contact = new Contact();
            $contact->setFullName($this->faker->name)
                ->setEmail($this->faker->email)
                ->setSubject('demande n°' . $n + 1)
                ->setMessage($this->faker->text());
            $manager->persist($contact);
        }


        $manager->flush();
    }
}
