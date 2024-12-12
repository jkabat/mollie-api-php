<?php

namespace Mollie\Api\Http\Data;

use DateTimeInterface;

class PaymentRoute extends Data
{
    public Money $amount;

    public string $organizationId;

    public ?DateTimeInterface $delayUntil;

    public function __construct(
        Money $amount,
        string $organizationId,
        ?DateTimeInterface $delayUntil = null
    ) {
        $this->amount = $amount;
        $this->organizationId = $organizationId;
        $this->delayUntil = $delayUntil;
    }

    public function toArray(): array
    {
        return [
            'amount' => $this->amount,
            'destination' => [
                'type' => 'organization',
                'organizationId' => $this->organizationId,
            ],
            'delayUntil' => $this->delayUntil
        ];
    }
}
