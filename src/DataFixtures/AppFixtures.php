<?php

namespace App\DataFixtures;

use App\Entity\Genre;
use App\Entity\Movie;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $matrixMovie = new Movie();
        $matrixMovie->setTitle('The Matrix');
        $matrixMovie->setDescription('A computer hacker learns from mysterious rebels about the true nature of his reality and his role in the war against its controllers.');
        $matrixMovie->setReleaseDate(new \DateTime('1999-03-31'));
        $manager->persist($matrixMovie);

        $genreAction = new Genre();
        $genreAction->setName('Action');
        $matrixMovie->setGenre($genreAction);
        $manager->persist($genreAction);

        $blackPantherMovie = new Movie();
        $blackPantherMovie->setTitle('Black Panther');
        $blackPantherMovie->setDescription('After the death of his father, T\'Challa returns home to the African nation of Wakanda to take his rightful place as king.');
        $blackPantherMovie->setReleaseDate(new \DateTime('2018-02-16'));
        $manager->persist($blackPantherMovie);

        $blackPantherMovie->setGenre($genreAction);

        $bluesBrothersMovie = new Movie();
        $bluesBrothersMovie->setTitle('The Blues Brothers');
        $bluesBrothersMovie->setDescription('Jake Blues, just out from prison, puts together his old band to save the Catholic home where he and brother Elwood were raised.');
        $bluesBrothersMovie->setReleaseDate(new \DateTime('1980-06-20'));
        $manager->persist($bluesBrothersMovie);

        $genreMusical = new Genre();
        $genreMusical->setName('Musical');
        $bluesBrothersMovie->setGenre($genreMusical);
        $manager->persist($genreMusical);

        $admin = new User();
        $admin
            ->setEmail('john.doe@admin.com')
            ->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $admin->setPassword($this->hasher->hashPassword($admin, 'admin1234'));
        $manager->persist($admin);

        $manager->flush();
    }
}
