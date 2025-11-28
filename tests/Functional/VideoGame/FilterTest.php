<?php

declare(strict_types=1);

namespace App\Tests\Functional\VideoGame;

use App\Model\Entity\Tag;
use App\Tests\Functional\FunctionalTestCase;

final class FilterTest extends FunctionalTestCase
{

    public function provideTags(): \Generator
    {
        yield 'Action' => ['tagNames' => ['Action']];
        yield 'Aventure' => ['tagNames' => ['Aventure']];
        yield 'Famille' => ['tagNames' => ['Famille']];
        yield 'Horreur' => ['tagNames' => ['Horreur']];
        yield 'Plateforme' => ['tagNames' => ['Plateforme']];
        yield 'Plateforme & Horreur' => ['tagNames' => ['Horreur', 'Plateforme']];
    }

    public function testShouldListTenVideoGames(): void
    {
        $this->get('/');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(10, 'article.game-card');
        $this->client->clickLink('2');
        self::assertResponseIsSuccessful();
    }

    public function testShouldFilterVideoGamesBySearch(): void
    {
        $this->get('/');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(10, 'article.game-card');
        $this->client->submitForm('Filtrer', ['filter[search]' => 'Jeu vidéo 49'], 'GET');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(1, 'article.game-card');
    }

    /*#[DataProvider(className: self::class, methodName: 'provideTags')]*/
    /**
     * @param string[] $tagNames
     * @dataProvider provideTags
     */
    public function testShouldFilterVideoGamesByTags(array $tagNames): void
    {
        $this->get('/');
        self::assertResponseIsSuccessful();
        $filterTags = [];
        $crawler = $this->get('/');

        $form = $crawler->selectButton('Filtrer')->form();

        /** Récupération des tags souhaités **/
        foreach ($tagNames as $tagName) {
            $tag = $this->getEntityManager()->getRepository(Tag::class)->findOneBy(['name' => $tagName]);
            $filterTags[] = (string) $tag->getId();
        }
        $form->disableValidation();
        $this->client->submit($form, ['filter[tags]' => $filterTags]);

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('.game-card-tags');

        $this->client->getCrawler()->filter('.game-card-tags')->each(function ($node) use ($tagNames) {
            $cardText = $node->text();

            foreach ($tagNames as $expectedTag) {
                $this->assertStringContainsString(
                    $expectedTag,
                    $cardText,
                    sprintf('Le jeu affiché ne contient pas le tag "%s" alors qu\'il le devrait.', $expectedTag)
                );
            }
        });
    }
}
