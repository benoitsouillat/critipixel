<?php

namespace App\Tests\Functional;

use App\Model\Entity\Tag;
use App\Model\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ReviewControllerTest extends WebTestCase
{
    private KernelBrowser|null $client = null;

    /** @var EntityRepository<User>|null */
    private ?EntityRepository $userRepository = null;

    private ?UrlGeneratorInterface $urlGenerator = null;
    private ?User $user = null;
    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
        $container = $this->client->getContainer();
        $entityManager = $container->get(EntityManagerInterface::class);

        $this->userRepository = $entityManager->getRepository(User::class);

        $this->urlGenerator = $this->client->getContainer()->get('router.default');
        $this->user = $this->userRepository->findOneByEmail('user+1@email.com');
        $this->client->loginUser($this->user);
    }

    /* Vérifie l'existence de la route listant les jeux vidéo */
    public function testVideoGameListIsUp(): void
    {
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('video_games_list'));
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testPostReview(): void
    {
        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('video_games_show', ['slug' => 'jeu-video-3']));
        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('Poster')->form();
        $form['review[rating]'] = '4';
        $form['review[comment]'] = 'Great game!';
        $this->client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

}