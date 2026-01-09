<?php

declare(strict_types=1);

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DatabaseConnectionTest extends WebTestCase
{
    public function testConnection(): void
    {
        self::bootKernel();

        $connection = self::getContainer()->get(Connection::class);

        $result = $connection->executeQuery('SELECT 1')->fetchOne();

        $this->assertEquals(1, $result, "La base de données n'a pas répondu correctement.");
    }

}