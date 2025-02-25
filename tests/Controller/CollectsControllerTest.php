<?php

namespace App\Tests\Controller;

use App\Entity\Collects;
use App\Repository\CollectsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class CollectsControllerTest extends WebTestCase{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $collectRepository;
    private string $path = '/collects/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->collectRepository = $this->manager->getRepository(Collects::class);

        foreach ($this->collectRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Collect index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'collect[titre]' => 'Testing',
            'collect[nomProduit]' => 'Testing',
            'collect[quantite]' => 'Testing',
            'collect[lieu]' => 'Testing',
            'collect[dateDebut]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->collectRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Collects();
        $fixture->setTitre('My Title');
        $fixture->setNomProduit('My Title');
        $fixture->setQuantite('My Title');
        $fixture->setLieu('My Title');
        $fixture->setDateDebut('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Collect');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Collects();
        $fixture->setTitre('Value');
        $fixture->setNomProduit('Value');
        $fixture->setQuantite('Value');
        $fixture->setLieu('Value');
        $fixture->setDateDebut('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'collect[titre]' => 'Something New',
            'collect[nomProduit]' => 'Something New',
            'collect[quantite]' => 'Something New',
            'collect[lieu]' => 'Something New',
            'collect[dateDebut]' => 'Something New',
        ]);

        self::assertResponseRedirects('/collects/');

        $fixture = $this->collectRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getTitre());
        self::assertSame('Something New', $fixture[0]->getNomProduit());
        self::assertSame('Something New', $fixture[0]->getQuantite());
        self::assertSame('Something New', $fixture[0]->getLieu());
        self::assertSame('Something New', $fixture[0]->getDateDebut());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Collects();
        $fixture->setTitre('Value');
        $fixture->setNomProduit('Value');
        $fixture->setQuantite('Value');
        $fixture->setLieu('Value');
        $fixture->setDateDebut('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/collects/');
        self::assertSame(0, $this->collectRepository->count([]));
    }
}
