<?php

namespace App\DataFixtures;
use App\Entity\Movie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MovieFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
      $movie = new Movie();
      $movie->setTitle('The Dark Knight');
      $movie->setReleaseYear(2008);
      $movie->setDescription('This is ...');
      $movie->setImagePath('https://cdn.pixabay.com/photo/2021/06/18/11/22/batman-6345897_960_720.jpg');
      $movie->addActor($this->getReference('actor_1'));
      $movie->addActor($this->getReference('actor_2'));
      $manager->persist($movie);

      $movie2 = new Movie();
      $movie2->setTitle('Avenger: Endgame');
      $movie2->setReleaseYear(2019);
      $movie2->setDescription('This is ...');
      $movie2->setImagePath('https://cdn.pixabay.com/photo/2018/05/08/11/36/avenger-3382834_960_720.jpg');
      $movie2->addActor($this->getReference('actor_3'));
      $movie2->addActor($this->getReference('actor_4'));
      $manager->persist($movie2);

      $manager->flush();


    }
}