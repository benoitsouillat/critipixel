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

        $videoGames = array_fill_callback(0, 50, fn (int $index): VideoGame => (new VideoGame)
            ->setTitle(sprintf('Jeu vidéo %d', $index))
            ->setDescription($this->faker->paragraphs(10, true))
            ->setReleaseDate(new DateTimeImmutable())
            ->setTest($this->faker->paragraphs(6, true))
            ->setRating(($index % 5) + 1)
            ->setImageName(sprintf('video_game_%d.png', $index))
            ->setImageSize(2_098_872)
        );

        $reviews = array_fill_callback(0, 20, function (int $index) use ($videoGames, $users) : Review {
            $randomVideoGame = $videoGames[array_rand($videoGames)];
            $randomUser = $users[array_rand($users)];
            $review = (new Review)
                ->setVideoGame($randomVideoGame)
                ->setRating($index % 5 + 1)
                ->setUser($randomUser)
                ->setComment($this->faker->paragraphs(6, true));

            return $review;
        });

        $tagsName = ['Aventure', 'Action', 'Horreur', 'Plateforme', 'Famille'];
        $tags = array_fill_callback(0, 5, function () use (&$tagsName): Tag {
            $name = array_pop($tagsName);
            return (new Tag())->setName($name);
        });

        foreach ($videoGames as $videoGame) {
            for ($i = 0; $i < rand(2, 4); $i++) {
                $randomTag = $tags[array_rand($tags)];
                $videoGame->addTag($randomTag);
            }
            $this->calculateAverageRating->calculateAverage($videoGame);
            $this->countRatingsPerValue->countRatingsPerValue($videoGame);
        }

        array_walk($videoGames, [$manager, 'persist']);
        array_walk($reviews, [$manager, 'persist']);
        array_walk($tags, [$manager, 'persist']);

        $manager->flush();

    }

    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }
}
