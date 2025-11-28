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

    private function collectionOfReviews(array $ratings): ArrayCollection
    {
        $reviews = new ArrayCollection();
        foreach ($ratings as $rating) {
            $reviews->add((new Review())->setRating((int) $rating));
        }
        return $reviews;
    }

    private function ReviewProvider(): \Generator
    {
        yield 'Reviews with average 5' => [
            'reviews' => $this->collectionOfReviews([5, 5]),
            'resultExpect' => 5,
        ];
        yield 'Review with average 4' => [
            'reviews' => $this->collectionOfReviews([5, 3, 4]),
            'resultExpect' => 4,
        ];
        yield 'Review with average 3' => [
            'reviews' => $this->collectionOfReviews([5, 3, 1]),
            'resultExpect' => 3,
        ];
        yield 'Review with average 2' => [
            'reviews' => $this->collectionOfReviews([2]),
            'resultExpect' => 2,
        ];
        yield 'Review with average 1' => [
            'reviews' => $this->collectionOfReviews([1, 1, 1]),
            'resultExpect' => 1,
        ];
    }

    /**
     * @param ArrayCollection<Review> $reviews
     * @param int $resultExpect
     * @dataProvider ReviewProvider
     */
    public function testAverage(ArrayCollection $reviews, int $resultExpect): void
    {
        foreach ($reviews as $review) {
            $this->videoGame->getReviews()->add($review);
        }
        (new RatingHandler())->calculateAverage($this->videoGame);

        $this->assertEquals($resultExpect, $this->videoGame->getAverageRating());
    }

    public function testAverageNull(): void
    {
        $this->videoGame->getReviews()->clear();
        (new RatingHandler())->calculateAverage($this->videoGame);

        $this->assertNull($this->videoGame->getAverageRating());
    }
}