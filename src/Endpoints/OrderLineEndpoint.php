<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\Order;
use Mollie\Api\Resources\OrderLine;
use Mollie\Api\Resources\ResourceFactory;

class OrderLineEndpoint extends RestEndpoint
{
    /**
     * The resource path.
     */
    protected string $resourcePath = 'orders_lines';

    /**
     * Resource id prefix.
     * Used to validate resource id's.
     */
    protected static string $resourceIdPrefix = 'odl_';

    /**
     * Resource class name.
     */
    public static string $resource = OrderLine::class;

    /**
     * Update a specific OrderLine resource.
     *
     * Will throw an ApiException if the order line id is invalid or the resource cannot be found.
     *
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function update(string $orderId, string $orderlineId, array $data = []): ?Order
    {
        $this->parentId = $orderId;

        $this->guardAgainstInvalidId($orderlineId);

        $response = $this->client->performHttpCall(
            self::REST_UPDATE,
            $this->getPathToSingleResource(urlencode($orderlineId)),
            $this->parseRequestBody($data)
        );

        if ($response->isEmpty()) {
            return null;
        }

        /** @var Order */
        return ResourceFactory::createFromApiResult($this->client, $response, Order::class);
    }

    /**
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function updateMultiple(string $orderId, array $operations, array $parameters = []): Order
    {
        if (empty($orderId)) {
            throw new ApiException('Invalid resource id.');
        }

        $this->parentId = $orderId;

        $parameters['operations'] = $operations;

        $result = $this->client->performHttpCall(
            self::REST_UPDATE,
            "{$this->getResourcePath()}",
            $this->parseRequestBody($parameters)
        );

        /** @var Order */
        return ResourceFactory::createFromApiResult($this->client, $result, Order::class);
    }

    /**
     * Cancel lines for the provided order.
     * The data array must contain a lines array.
     * You can pass an empty lines array if you want to cancel all eligible lines.
     *
     *
     * @throws ApiException
     */
    public function cancelFor(Order $order, array $data): void
    {
        $this->cancelForId($order->id, $data);
    }

    /**
     * Cancel lines for the provided order id.
     * The data array must contain a lines array.
     * You can pass an empty lines array if you want to cancel all eligible lines.
     *
     *
     * @throws ApiException
     */
    public function cancelForId(string $orderId, array $data): void
    {
        if (! isset($data['lines']) || ! is_array($data['lines'])) {
            throw new ApiException('A lines array is required.');
        }

        $this->parentId = $orderId;

        $this->client->performHttpCall(
            self::REST_DELETE,
            "{$this->getResourcePath()}",
            $this->parseRequestBody($data)
        );
    }
}
