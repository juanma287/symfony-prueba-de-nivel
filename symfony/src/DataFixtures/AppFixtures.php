<?php

namespace App\DataFixtures;

use App\Entity\Empresa;
use App\Entity\Sector;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
 
        $sector = new Sector();
        $sector->setNombre('Finanzas');
        $manager->persist($sector);

        $sector2 = new Sector();
        $sector2->setNombre('Automotor');
        $manager->persist($sector2);
      
        $manager->flush();

        for ($i=0; $i < 12 ; $i++) { 
            $emmpresa = new Empresa();
            $emmpresa->setNombre('JP Morgan '.$i);
            $emmpresa->setEmail('juanma@gmail.com');
            $emmpresa->setTelefono('3498 479936');
            $emmpresa->setSector($sector);
            $manager->persist($emmpresa);
        }
        $manager->flush();




       
    }
}
