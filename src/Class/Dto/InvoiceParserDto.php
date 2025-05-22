<?php

declare(strict_types=1);

namespace App\Class\Dto;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

class InvoiceParserDto
{
    #[SerializedName('nom')]
    #[Groups('parser')]
    public string $name;

    #[SerializedName('montant')]
    #[Groups('parser')]
    public float $amount;

    #[SerializedName('devise')]
    #[Groups('parser')]
    public string $currency;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }
}
