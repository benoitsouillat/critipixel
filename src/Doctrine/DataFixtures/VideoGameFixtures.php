<?php

namespace App\Doctrine\DataFixtures;

use App\Model\Entity\Review;
use App\Model\Entity\Tag;
use App\Model\Entity\User;
use App\Model\Entity\VideoGame;
use App\Rating\CalculateAverageRating;
use App\Rating\CountRatingsPerValue;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

use function array_fill_callback;

final class VideoGameFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly Generator $faker,
        private readonly CalculateAverageRating $calculateAverageRating,
        private readonly CountRatingsPerValue $countRatingsPerValue
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $users = $manager->getRepository(User::class)->findAll();

        $tags = [];
        $tagNames = ['Aventure', 'Action', 'Horreur', 'Plateforme', 'Famille'];
        foreach ($tagNames as $tagName) {
            $tag = (new Tag())->setName($tagName);
            $manager->persist($tag);
            $tags[] = $tag;
        }

        $videoGames = [];
        for ($i = 0; $i < 50; $i++) {
            $videoGame = (new VideoGame)
                ->setTitle(sprintf('Jeu vidéo %d', $i))
                ->setDescription($this->faker->paragraphs(10, true))
                ->setReleaseDate(new DateTimeImmutable())
                ->setTest($this->faker->paragraphs(6, true))
                ->setRating(($i % 5) + 1)
                ->setImageName(sprintf('video_game_%d.png', $i))
                ->setImageSize(2_098_872);

            for ($j = 0; $j < rand(2,4); $j++) {
                $randomTag = $tags[array_rand($tags)];
                $videoGame->addTag($randomTag);
            }

            $manager->persist($videoGame);
            $videoGames[] = $videoGame;
        }

        for ($i = 0; $i < 20; $i++) {
            $review = (new Review)
                ->setVideoGame($videoGames[array_rand($videoGames)])
                ->setUser($users[array_rand($users)])
                ->setRating(($i % 5) + 1)
                ->setComment($this->faker->paragraphs(6, true));

            $manager->persist($review);
        }

        foreach ($videoGames as $videoGame) {
            $this->calculateAverageRating->calculateAverage($videoGame);
            $this->countRatingsPerValue->countRatingsPerValue($videoGame);
        }

        $manager->flush();

    }

    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }
}
