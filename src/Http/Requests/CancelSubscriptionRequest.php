<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Resources\Subscription;
use Mollie\Api\Types\Method;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;

class CancelSubscriptionRequest extends ResourceHydratableRequest implements SupportsTestmodeInQuery
{
    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::DELETE;

    /**
     * The resource class the request should be casted to.
     */
    public static string $targetResourceClass = Subscription::class;

    private string $customerId;

    private string $subscriptionId;

    public function __construct(string $customerId, string $subscriptionId)
    {
        $this->customerId = $customerId;
        $this->subscriptionId = $subscriptionId;
    }

    /**
     * The resource path.
     */
    public function resolveResourcePath(): string
    {
        return "customers/{$this->customerId}/subscriptions/{$this->subscriptionId}";
    }
}
