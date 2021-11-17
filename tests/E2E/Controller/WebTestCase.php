<?php

declare(strict_types=1);

namespace App\Tests\E2E\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;

abstract class WebTestCase extends BaseWebTestCase
{
    /** @var KernelBrowser */
    protected KernelBrowser $client;

    /** @var EntityManagerInterface */
    protected EntityManagerInterface $entityManager;

    /**
     * @throws \Doctrine\ORM\Tools\ToolsException
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);

        $this->resetDatabase();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
    }

    /**
     * @throws \Doctrine\ORM\Tools\ToolsException
     */
    protected function resetDatabase(): void
    {
        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }
}