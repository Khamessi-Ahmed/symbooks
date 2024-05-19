<?php

namespace App\DataFixtures;

use App\Entity\Categories;
use App\Entity\Livres;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        for ($j = 1; $j <= 3; $j++) {
            $cat = new Categories();
            $cat->setLibelle($faker->name)
                ->setSlug($faker->name)
                ->setDescription($faker->paragraph(3));
            $manager->persist($cat);


            for ($i = 1; $i <= random_int(50, 100); $i++) {           # code...

                $livre = new Livres();
                //convertir datetime en datetimeimmutable
                $datetime = $faker->dateTime();
                $datetimeimmutable = \DateTimeImmutable::createFromMutable($datetime);
                $titre = $faker->name;
                $livre->setAuteur($faker->userName())
                    ->setEditedAt($datetimeimmutable)
                    ->setTitre($titre)
                    ->setQte($faker->numberBetween(0, 200))
                    ->setResume($faker->paragraph(3))
                    ->setSlug(strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $titre)))
                    ->setPrix($faker->numberBetween(10, 300))
                    ->setEditeur($faker->company())
                    ->setISBN($faker->isbn13())
                    ->setImage($faker->imageUrl())
                    ->setCategorie($cat);


                $manager->persist($livre);
            }

            $manager->flush();
        }
    }
}
