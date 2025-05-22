<?php

declare(strict_types=1);

namespace App\Service\Parser;

use App\Class\Dto\InvoiceParserDto;
use App\Entity\Invoice;
use App\Repository\InvoiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use UnexpectedValueException;

class InvoiceParser
{
    public function __construct(
        private readonly InvoiceParserCsv       $invoiceParserCsv,
        private readonly InvoiceParserJson      $invoiceParserJson,
        private readonly InvoiceRepository      $invoiceRepository,
        private readonly EntityManagerInterface $em,
    )
    {
    }

    public function parse(string $filePath): int
    {
        $invoiceDtos = null;
        $mimeTypeExtension = $this->getMimeTypeOrExtension($filePath);
        switch ($mimeTypeExtension) {
            case 'application/json':
                $invoiceDtos = $this->invoiceParserJson->parse($filePath);
                break;
            case 'csv':
                $invoiceDtos = $this->invoiceParserCsv->parse($filePath);
                break;
        }
        if (is_null($invoiceDtos)) {
            throw new UnexpectedValueException('Unsupported file type');
        }
        $invoices = $this->invoiceRepository->findBy(['name' => array_map(static function (InvoiceParserDto $invoiceDto) {
            return $invoiceDto->getName();
        }, $invoiceDtos)]);
        foreach ($invoiceDtos as $invoiceDto) {
            $invoice = current(array_filter($invoices, static function (Invoice $invoice) use ($invoiceDto) {
                return $invoice->getName() === $invoiceDto->getName();
            }));
            if (!$invoice) {
                $invoice = new Invoice();
            }
            $invoice
                ->setName($invoiceDto->getName())
                ->setAmount($invoiceDto->getAmount())
                ->setCurrency($invoiceDto->getCurrency());
            $this->em->persist($invoice);
        }
        $this->em->flush();
        return count($invoiceDtos);
    }

    private function getMimeTypeOrExtension(string $filePath): string
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filePath);
        finfo_close($finfo);
        if ($mimeType === 'text/plain') {
            return pathinfo($filePath, PATHINFO_EXTENSION);
        }
        return $mimeType;
    }
}
