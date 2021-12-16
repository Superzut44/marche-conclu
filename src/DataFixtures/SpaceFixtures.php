<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Space;

class SpaceFixtures extends Fixture
{
    private const TYPESPACE = ['Bureau privé', 'Co-Working', 'Salle de réunion', 'Open Space', 'Espace de stockage'];
    private const LOCATION = ['Reims', 'Charleville', 'Lyon', 'Paris', 'Epernay'];
    private const SPACE_COUNT = 30;
    private const SPACE_IMAGES = [
        'dane-deaner.jpg', 'coworking.jpeg', 'open_space.jpg',
        'openspace.jpg', 'salle_de_reunion.jpg', 'stockage.jpg'
    ];

    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= self::SPACE_COUNT; $i++) {
            $space = new Space();
            $space->setName('Espace ' . $i);
            $randImage = array_rand(self::SPACE_IMAGES);
            $space->setPhotos(self::SPACE_IMAGES[$randImage]);
            $space->setSurface(rand(5, 200));
            $typespace = array_rand(self::TYPESPACE);
            $space->setCategory(self::TYPESPACE[$typespace]);
            $space->setCapacity(rand(10, 100));
            $location = array_rand(self::LOCATION);
            $space->setLocation(self::LOCATION[$location]);
            $this->addReference($space->getName(), $space);
            $manager->persist($space);
        }

        $manager->flush();
    }
}
