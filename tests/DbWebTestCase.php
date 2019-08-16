<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class DbWebTestCase extends WebTestCase
{
    /**
     * Test client
     */
    protected $client;

    /**
     * Entity manager
     */
    protected $entityManager;

    /**
     * Before each test
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();

        $this->client->disableReboot();
        $this->entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $this->entityManager->beginTransaction();
        $this->entityManager->getConnection()->setAutoCommit(false);
    }

    /**
     * After each test
     */
    public function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->rollback();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}