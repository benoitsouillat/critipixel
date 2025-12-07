<?php

namespace App\Tests\Entity;

use App\Model\Entity\VideoGame;
use PHPUnit\Framework\TestCase;

class VideoGameTest extends TestCase
{
    private VideoGame $videoGame;

    public function setUp(): void
    {
        $this->videoGame = new VideoGame();
    }

    public function testIsTrue(): void
    {
        $this->videoGame->setTitle('Title')
            ->setDescription('Description of the video game')
            ->setReleaseDate(new \DateTimeImmutable('2023-01-01'));

        self::assertSame('Title', $this->videoGame->getTitle());
        self::assertSame('Description of the video game', $this->videoGame->getDescription());
        self::assertEquals(new \DateTimeImmutable('2023-01-01'), $this->videoGame->getReleaseDate());
    }

    public function testIsFalse(): void
    {
        $this->videoGame->setTitle('Title')
            ->setDescription('Description of the video game')
            ->setReleaseDate(new \DateTimeImmutable('2023-01-01'));

        self::assertNotSame('Wrong Title', $this->videoGame->getTitle());
        self::assertNotSame('Wrong description', $this->videoGame->getDescription());
        self::assertNotSame(new \DateTimeImmutable('2022-12-31'), $this->videoGame->getReleaseDate());
    }
}
