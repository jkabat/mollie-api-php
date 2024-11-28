<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Http\Requests\GetPaginatedPaymentLinkPaymentsRequest;
use Mollie\Api\Resources\PaymentLink;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentCollection;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;

class PaymentLinkPaymentEndpointCollectionTest extends TestCase
{
    /** @test */
    public function page_for()
    {
        $client = new MockClient([
            GetPaginatedPaymentLinkPaymentsRequest::class => new MockResponse(200, 'payment-list'),
        ]);

        $paymentLink = new PaymentLink($client);
        $paymentLink->id = 'pl_4Y0eZitmBnQ6IDoMqZQKh';

        /** @var PaymentCollection $payments */
        $payments = $client->paymentLinkPayments->pageFor($paymentLink);

        $this->assertInstanceOf(PaymentCollection::class, $payments);
        $this->assertGreaterThan(0, $payments->count());

        foreach ($payments as $payment) {
            $this->assertPayment($payment);
        }
    }

    /** @test */
    public function iterator_for()
    {
        $client = new MockClient([
            GetPaginatedPaymentLinkPaymentsRequest::class => new MockResponse(200, 'payment-list'),
        ]);

        $paymentLink = new PaymentLink($client);
        $paymentLink->id = 'pl_4Y0eZitmBnQ6IDoMqZQKh';

        foreach ($client->paymentLinkPayments->iteratorFor($paymentLink) as $payment) {
            $this->assertPayment($payment);
        }
    }

    protected function assertPayment(Payment $payment)
    {
        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertEquals('payment', $payment->resource);
        $this->assertNotEmpty($payment->id);
        $this->assertNotEmpty($payment->mode);
        $this->assertNotEmpty($payment->createdAt);
        $this->assertNotEmpty($payment->status);
        $this->assertNotEmpty($payment->amount);
        $this->assertNotEmpty($payment->description);
        $this->assertNotEmpty($payment->method);
        $this->assertNotEmpty($payment->metadata);
        $this->assertNotEmpty($payment->profileId);
        $this->assertNotEmpty($payment->_links);
    }
}
