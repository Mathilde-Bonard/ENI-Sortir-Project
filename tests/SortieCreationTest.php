<?php

namespace App\Tests;

use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\UserRepository;
use ContainerVJK8ZEY\getEtatFixturesService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SortieCreationTest extends WebTestCase
{
    public function testDisplayAddSortieReturns200(): void
    {
        $client = static::createClient();
        $client->request('GET', '/add');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testDisplayFormCreationSortir(): void
    {
        $client = static::createClient();

        $user = static::getContainer()->get(UserRepository::class)->findOneBy(['email' => 'john.doe@gmail.com']);
        $client->loginUser($user);

        $crawler = $client->request('GET', '/add');

        $etat = static::getContainer()->get(EtatRepository::class)->findOneBy(['id' => 1]);
        $lieu = static::getContainer()->get(LieuRepository::class)->findOneBy(['id' => 1]);
        $campus= static::getContainer()->get(CampusRepository::class)->findOneBy(['id' => 1]);
        $organisateur = static::getContainer()->get(UserRepository::class)->findOneBy(['id' => 4]);

        $form['sortie[nom]'] = 'Sortie escalade';
        $form['sortie[dateDebut]'] = '2025-09-12T12:30';
        $form['sortie[duree]'] = 4;
        $form['sortie[dateLimiteInscription]'] = '2025-09-11T12:30';
        $form['sortie[nbInscriptionMax]'] = 10;
        $form['sortie[duree]'] = 4;
        $form['sortie[infoSortie]'] = 'Sortie en plein air';
        $form['sortie[etat]'] = $etat->getId();
        $form['sortie[campus]'] = $campus->getId();
        $form['sortie[lieu]'] = $lieu->getId();
        $form['sortie[organisateur]'] = $organisateur->getId();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Creation page : Sortie');

    }
}
