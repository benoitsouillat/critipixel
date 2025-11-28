<?php
declare(strict_types=1);

namespace App\Tests\Entity;

use App\Model\Entity\Review;
use App\Model\Entity\VideoGame;
use App\Rating\RatingHandler;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class AverageTest extends TestCase
{
    private VideoGame $videoGame;

    public function setUp(): void
    {
        $this->videoGame = new VideoGame();
    }

    public function testAverageFour(): void
    {
        $reviews = (new ArrayCollection([
            (new Review())->setRating(5),
            (new Review())->setRating(4),
            (new Review())->setRating(3),
            (new Review())->setRating(4),
        ]));
        foreach ($reviews as $review) {
            $this->videoGame->getReviews()->add($review);
        }

        (new RatingHandler())->calculateAverage($this->videoGame);

        $this->assertEquals(4, $this->videoGame->getAverageRating());
    }

    public function testAverageNull(): void
    {
        $videoGame = $this->createMock(VideoGame::class);
        $videoGame->method('getReviews')->willReturn(new ArrayCollection([]));

        (new RatingHandler())->calculateAverage($videoGame);

        $videoGame->expects($this->once())->method('getAverageRating')->willReturn(null);

        $this->assertNull($videoGame->getAverageRating());
    }
}