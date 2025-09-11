<?php

namespace App\Tests;

use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SortieCreationTest extends WebTestCase
{
    public function testDisplayAddSortieReturns200(): void
    {
        $client = static::createClient();
        $client->request('GET', '/add');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testFormCreationSortie(): void
    {
        $client = static::createClient();

        $user = static::getContainer()->get(UserRepository::class)->findOneBy(['email' => 'john.doe@gmail.com']);
        $client->loginUser($user);

        $crawler = $client->request('GET', '/add');

        // Récupération des éléments déjà défini en BDD pour remplir le formulaire
        $etat = static::getContainer()->get(EtatRepository::class)->findOneBy(['id' => 1]);
        $lieu = static::getContainer()->get(LieuRepository::class)->findOneBy(['id' => 1]);
        $campus= static::getContainer()->get(CampusRepository::class)->findOneBy(['id' => 1]);
        $organisateur = static::getContainer()->get(UserRepository::class)->findOneBy(['id' => 4]);

        // Création du formulaire par détection du bouton submit nommé ici "Créer"
        // Submit inclu
        $form = $client->submitForm('Créer', [
            'sortie_creation[nom]' => 'Sortie test persiste',
            'sortie_creation[dateHeureDebut]' => '2025-09-12T12:30',
            'sortie_creation[duree]' => 4,
            'sortie_creation[dateLimiteInscription]' => '2025-09-11T12:30',
            'sortie_creation[nbInscriptionMax]' => 10,
            'sortie_creation[infosSortie]' => 'Sortie en plein air',
            'sortie_creation[etat]' => $etat->getId(),
            'sortie_creation[campus]' => $campus->getId(),
            'sortie_creation[lieu]' => $lieu->getId(),
            'sortie_creation[organisateur]' => $organisateur->getId()
        ]);

        // Vérifie que la redirection se fait après le submit
        $this->assertResponseRedirects('/',302);
        // Obligation de follow la route pour vérifier l'affichage du message qui ne peut se réaliser autrement
        $client->followRedirect();
        $this->assertSelectorTextContains('.flash-success', 'Sortie ajoutée !');

        // Vérification persistance des données
        $sortie = static::getContainer()->get(SortieRepository::class)->findOneBy(['nom' => 'Sortie test persiste']);
        $this->assertNotNull($sortie);

    }
}
