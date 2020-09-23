<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $yann = new User();
        $yann->setFirstName('Yann')
            ->setLastName('Terrien')
            ->setEmail('yann@yopmail.com')
            ->setRoles(["ROLE_ADMIN"])
            ->setPassword('secret');
        $manager->persist($yann);

        $thomas = new User();
        $thomas->setFirstName('Thomas')
            ->setLastName('Terrien')
            ->setEmail('thomas@yopmail.com')
            ->setRoles(["ROLE_ADMIN"])
            ->setPassword('secret');
        $manager->persist($thomas);

        $julie = new User();
        $julie->setFirstName('Julie')
            ->setLastName('Trannois')
            ->setEmail('julie@yopmail.com')
            ->setRoles(["ROLE_ADMIN"])
            ->setPassword('secret');
        $manager->persist($julie);

        $manager->flush();
    }
}
