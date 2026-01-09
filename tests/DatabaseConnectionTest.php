<?php

declare(strict_types=1);

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DatabaseConnectionTest extends WebTestCase
{
    protected string $databaseHost;
    protected int $databasePort;
    protected string $databaseUser;
    protected string $databasePassword;
    protected string $databaseName;

    public function setUp(): void
    {
        $this->databaseHost = getenv('DB_HOST');
        $this->databasePort = (int)getenv('DB_PORT');
        $this->databaseUser = getenv('DB_USER');
        $this->databasePassword = getenv('DB_PASSWORD');
        $this->databaseName = getenv('DB_NAME');
    }

    public function testConnection(): void
    {
        $connection = new \PDO(
            "postgresql:host={$this->databaseHost};port={$this->databasePort};dbname={$this->databaseName}",
            $this->databaseUser,
            $this->databasePassword
        );
        $this->assertNotFalse($connection);
    }

}