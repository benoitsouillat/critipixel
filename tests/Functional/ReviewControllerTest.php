<?php

namespace App\Tests\Functional;

use App\Model\Entity\Tag;
use App\Model\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ReviewControllerTest extends WebTestCase
{
    private KernelBrowser|null $client = null;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
        $this->userRepository = $this->getEntityManager()->getRepository(User::class);
        $this->tagsRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(Tag::class);


        $this->urlGenerator = $this->client->getContainer()->get('router.default');
        $this->user = $this->userRepository->findOneByEmail('user+1@email.com');
        $this->client->loginUser($this->user);
    }



    /* Vérifie l'existence de la route listant les jeux vidéo */
    public function testVideoGameListIsUp(): void
    {
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('video_games_list'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testPostReview(): void
    {
        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('video_games_show', ['slug' => 'jeu-video-3']));
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Poster')->form();
        $form['review[rating]'] = 4;
        $form['review[comment]'] = 'Great game!';
        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

}