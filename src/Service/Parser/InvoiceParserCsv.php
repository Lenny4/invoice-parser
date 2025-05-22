<?php

declare(strict_types=1);

namespace App\Service\Parser;

use App\Class\Dto\InvoiceParserDto;
use InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class InvoiceParserCsv
{
    public function __construct(
        private readonly SerializerInterface $serializer
    )
    {
    }

    /**
     * @return InvoiceParserDto[]
     */
    public function parse(
        string $filePath,
        string $delimiter = "\t",
        string $enclosure = '"',
        string $escape = '\\'
    ): array
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new InvalidArgumentException(sprintf('The file "%s" does not exist or is not readable.', $filePath));
        }
        $csvContent = file_get_contents($filePath);
        $rows = [];
        $lines = explode("\n", $csvContent);

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }
            $parsedLine = str_getcsv($line, $delimiter, $enclosure, $escape);
            $rows[] = [
                'nom' => $parsedLine[2],
                'montant' => (float)$parsedLine[0],
                'devise' => $parsedLine[1],
            ];
        }
        $json = json_encode($rows, JSON_THROW_ON_ERROR);
        return $this->serializer->deserialize($json, InvoiceParserDto::class . '[]', 'json', [
            AbstractNormalizer::GROUPS => ['parser']
        ]);
    }
}
