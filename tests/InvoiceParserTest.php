<?php

declare(strict_types=1);

namespace App\Tests;

use App\Service\Parser\InvoiceParser;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class InvoiceParserTest extends KernelTestCase
{
    private $entityManager;

    public function testParseJson(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $connection = $this->createMock(Connection::class);
        $this->entityManager->method('getConnection')->willReturn($connection);

        $connection->expects($this->exactly(10))->method('executeStatement');

        $invoiceParser = new InvoiceParser($this->entityManager);

        $invoiceParser->parse('data/invoices.json');
    }

    public function testParseCsv(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $connection = $this->createMock(Connection::class);
        $this->entityManager->method('getConnection')->willReturn($connection);

        $connection->expects($this->exactly(10))->method('executeStatement');

        $invoiceParser = new InvoiceParser($this->entityManager);

        $invoiceParser->parse('data/invoices.csv');
    }

}

