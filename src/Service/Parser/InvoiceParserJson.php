<?php

declare(strict_types=1);

namespace App\Service\Parser;

use App\Class\Dto\InvoiceParserDto;
use InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class InvoiceParserJson
{
    public function __construct(
        private readonly SerializerInterface $serializer
    )
    {
    }

    /**
     * @return InvoiceParserDto[]
     */
    public function parse(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new InvalidArgumentException("File not found: $filePath");
        }
        $content = file_get_contents($filePath);
        return $this->serializer->deserialize($content, InvoiceParserDto::class . '[]', 'json', [
            AbstractNormalizer::GROUPS => ['parser']
        ]);
    }
}
