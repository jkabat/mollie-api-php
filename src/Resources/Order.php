<?php

namespace Mollie\Api\Resources;

use Mollie\Api\Contracts\EmbeddedResourcesContract;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Types\OrderStatus;

class Order extends BaseResource implements EmbeddedResourcesContract
{
    use HasPresetOptions;

    /**
     * Id of the order.
     *
     * @example ord_8wmqcHMN4U
     *
     * @var string
     */
    public $id;

    /**
     * The profile ID this order belongs to.
     *
     * @example pfl_xH2kP6Nc6X
     *
     * @var string
     */
    public $profileId;

    /**
     * Either "live" or "test". Indicates this being a test or a live (verified) order.
     *
     * @var string
     */
    public $mode;

    /**
     * Amount object containing the value and currency
     *
     * @var \stdClass
     */
    public $amount;

    /**
     * The total amount captured, thus far.
     *
     * @var \stdClass
     */
    public $amountCaptured;

    /**
     * The total amount refunded, thus far.
     *
     * @var \stdClass
     */
    public $amountRefunded;

    /**
     * The status of the order.
     *
     * @var string
     */
    public $status;

    /**
     * The person and the address the order is billed to.
     *
     * @var \stdClass
     */
    public $billingAddress;

    /**
     * The date of birth of your customer, if available.
     *
     * @example 1976-08-21
     *
     * @var string|null
     */
    public $consumerDateOfBirth;

    /**
     * The order number that was used when creating the order.
     *
     * @var string
     */
    public $orderNumber;

    /**
     * The person and the address the order is shipped to.
     *
     * @var \stdClass
     */
    public $shippingAddress;

    /**
     * The payment method last used when paying for the order.
     *
     * @see Method
     *
     * @var string
     */
    public $method;

    /**
     * The locale used for this order.
     *
     * @var string
     */
    public $locale;

    /**
     * During creation of the order you can set custom metadata that is stored with
     * the order, and given back whenever you retrieve that order.
     *
     * @var \stdClass|mixed|null
     */
    public $metadata;

    /**
     * Can this order be canceled?
     *
     * @var bool
     */
    public $isCancelable;

    /**
     * Webhook URL set on this payment
     *
     * @var string|null
     */
    public $webhookUrl;

    /**
     * Redirect URL set on this payment
     *
     * @var string
     */
    public $redirectUrl;

    /**
     * Cancel URL set on this payment
     *
     * @var string
     */
    public $cancelUrl;

    /**
     * UTC datetime the order was created in ISO-8601 format.
     *
     * @example "2013-12-25T10:30:54+00:00"
     *
     * @var string|null
     */
    public $createdAt;

    /**
     * UTC datetime the order the order will expire in ISO-8601 format.
     *
     * @example "2013-12-25T10:30:54+00:00"
     *
     * @var string|null
     */
    public $expiresAt;

    /**
     * UTC datetime if the order is expired, the time of expiration will be present in ISO-8601 format.
     *
     * @example "2013-12-25T10:30:54+00:00"
     *
     * @var string|null
     */
    public $expiredAt;

    /**
     * UTC datetime if the order has been paid, the time of payment will be present in ISO-8601 format.
     *
     * @example "2013-12-25T10:30:54+00:00"
     *
     * @var string|null
     */
    public $paidAt;

    /**
     * UTC datetime if the order has been authorized, the time of authorization will be present in ISO-8601 format.
     *
     * @example "2013-12-25T10:30:54+00:00"
     *
     * @var string|null
     */
    public $authorizedAt;

    /**
     * UTC datetime if the order has been canceled, the time of cancellation will be present in ISO 8601 format.
     *
     * @example "2013-12-25T10:30:54+00:00"
     *
     * @var string|null
     */
    public $canceledAt;

    /**
     * UTC datetime if the order is completed, the time of completion will be present in ISO 8601 format.
     *
     * @example "2013-12-25T10:30:54+00:00"
     *
     * @var string|null
     */
    public $completedAt;

    /**
     * The order lines contain the actual things the customer bought.
     *
     * @var array|object[]
     */
    public $lines;

    /**
     * For digital goods, you must make sure to apply the VAT rate from your customer’s country in most jurisdictions.
     * Use this parameter to restrict the payment methods available to your customer to methods from the billing country
     * only.
     *
     * @var bool
     */
    public $shopperCountryMustMatchBillingCountry;

    /**
     * An object with several URL objects relevant to the customer. Every URL object will contain an href and a type field.
     *
     * @var \stdClass
     */
    public $_links;

    /**
     * @var \stdClass|null
     */
    public $_embedded;

    public function getEmbeddedResourcesMap(): array
    {
        return [
            'payments' => PaymentCollection::class,
            'refunds' => RefundCollection::class,
            'shipments' => ShipmentCollection::class,
        ];
    }

    /**
     * Is this order created?
     */
    public function isCreated(): bool
    {
        return $this->status === OrderStatus::CREATED;
    }

    /**
     * Is this order paid for?
     */
    public function isPaid(): bool
    {
        return $this->status === OrderStatus::PAID;
    }

    /**
     * Is this order authorized?
     */
    public function isAuthorized(): bool
    {
        return $this->status === OrderStatus::AUTHORIZED;
    }

    /**
     * Is this order canceled?
     */
    public function isCanceled(): bool
    {
        return $this->status === OrderStatus::CANCELED;
    }

    /**
     * Is this order shipping?
     */
    public function isShipping(): bool
    {
        return $this->status === OrderStatus::SHIPPING;
    }

    /**
     * Is this order completed?
     */
    public function isCompleted(): bool
    {
        return $this->status === OrderStatus::COMPLETED;
    }

    /**
     * Is this order expired?
     */
    public function isExpired(): bool
    {
        return $this->status === OrderStatus::EXPIRED;
    }

    /**
     * Is this order completed?
     */
    public function isPending(): bool
    {
        return $this->status === OrderStatus::PENDING;
    }

    /**
     * Cancels this order.
     * If the order was partially shipped, the status will be "completed" instead of
     * "canceled".
     * Will throw a ApiException if the order id is invalid or the resource cannot
     * be found.
     *
     * @return Order
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function cancel()
    {
        return $this->connector->orders->cancel($this->id, $this->getPresetOptions());
    }

    /**
     * Cancel a line for this order.
     * The data array must contain a lines array.
     * You can pass an empty lines array if you want to cancel all eligible lines.
     * Returns null if successful.
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function cancelLines(array $data): void
    {
        $this->connector->orderLines->cancelFor($this, $data);
    }

    /**
     * Cancels all eligible lines for this order.
     * Returns null if successful.
     *
     * @param  array  $data
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function cancelAllLines($data = []): void
    {
        $data['lines'] = [];

        $this->connector->orderLines->cancelFor($this, $data);
    }

    /**
     * Get the line value objects
     */
    public function lines(): OrderLineCollection
    {
        /** @var OrderLineCollection */
        return ResourceFactory::createBaseResourceCollection(
            $this->connector,
            OrderLine::class,
            $this->lines
        );
    }

    /**
     * Create a shipment for some order lines. You can provide an empty array for the
     * "lines" option to include all unshipped lines for this order.
     *
     *
     * @throws ApiException
     */
    public function createShipment(array $options = []): Shipment
    {
        return $this->connector->shipments->createFor($this, $this->withPresetOptions($options));
    }

    /**
     * Create a shipment for all unshipped order lines.
     */
    public function shipAll(array $options = []): Shipment
    {
        $options['lines'] = [];

        return $this->createShipment($options);
    }

    /**
     * Retrieve a specific shipment for this order.
     *
     * @param  string  $shipmentId
     * @return Shipment
     *
     * @throws ApiException
     */
    public function getShipment($shipmentId, array $parameters = [])
    {
        return $this->connector->shipments->getFor($this, $shipmentId, $this->withPresetOptions($parameters));
    }

    /**
     * Get all shipments for this order.
     *
     *
     * @throws ApiException
     */
    public function shipments(array $parameters = []): ShipmentCollection
    {
        return $this->connector->shipments->listFor($this, $this->withPresetOptions($parameters));
    }

    /**
     * Get the checkout URL where the customer can complete the payment.
     */
    public function getCheckoutUrl(): ?string
    {
        if (empty($this->_links->checkout)) {
            return null;
        }

        return $this->_links->checkout->href;
    }

    /**
     * Refund specific order lines.
     *
     * @throws ApiException
     */
    public function refund(array $data): Refund
    {
        return $this->connector->orderRefunds->createFor($this, $this->withPresetOptions($data));
    }

    /**
     * Refund all eligible order lines.
     */
    public function refundAll(array $data = []): Refund
    {
        $data['lines'] = [];

        return $this->refund($data);
    }

    /**
     * Retrieves all refunds associated with this order
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function refunds(): RefundCollection
    {
        return $this->connector->orderRefunds->pageFor($this);
    }

    /**
     * Saves the order's updated billingAddress and/or shippingAddress.
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function update(): ?Order
    {
        $body = [
            'billingAddress' => $this->billingAddress,
            'shippingAddress' => $this->shippingAddress,
            'orderNumber' => $this->orderNumber,
            'redirectUrl' => $this->redirectUrl,
            'cancelUrl' => $this->cancelUrl,
            'webhookUrl' => $this->webhookUrl,
        ];

        /** @var null|Order */
        return $this->connector->orders->update($this->id, $body);
    }

    /**
     * Create a new payment for this Order.
     *
     * @param  array  $data
     * @param  array  $filters
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function createPayment($data, $filters = []): Payment
    {
        return $this->connector->orderPayments->createFor($this, $data, $filters);
    }

    /**
     * Retrieve the payments for this order.
     * Requires the order to be retrieved using the embed payments parameter.
     */
    public function payments(): ?PaymentCollection
    {
        if (! isset($this->_embedded, $this->_embedded->payments)) {
            return null;
        }

        /** @var PaymentCollection */
        return ResourceFactory::createBaseResourceCollection(
            $this->connector,
            Payment::class,
            $this->_embedded->payments,
        );
    }
}
