<?php

namespace Heterodoks\LaravelSendy\Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Heterodoks\LaravelSendy\Exceptions\SendyException;
use Heterodoks\LaravelSendy\Facades\Sendy;
use Heterodoks\LaravelSendy\Tests\TestCase;
use Heterodoks\LaravelSendy\Tests\TestHelper;

class SendyTest extends TestCase
{
    use TestHelper;

    protected function mockClientResponse(string $body, int $status = 200): void
    {
        $mock = new MockHandler([
            new Response($status, [], $body)
        ]);
        
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);
        
        $sendy = new \Heterodoks\LaravelSendy\Sendy($this->app['config']['sendy']);
        $this->setProperty($sendy, 'client', $client);
        
        $this->app->instance('sendy', $sendy);
    }

    public function test_can_subscribe_user(): void
    {
        $this->mockClientResponse('1');

        $result = Sendy::subscribe(
            'test-list-id',
            'test@example.com',
            'Test User',
            ['custom' => 'value'],
            true
        );

        $this->assertTrue($result);
    }

    public function test_can_unsubscribe_user(): void
    {
        $this->mockClientResponse('1');

        $result = Sendy::unsubscribe('test-list-id', 'test@example.com');

        $this->assertTrue($result);
    }

    public function test_can_get_subscription_status(): void
    {
        $this->mockClientResponse('Subscribed');

        $result = Sendy::getSubscriptionStatus('test-list-id', 'test@example.com');

        $this->assertEquals('Subscribed', $result);
    }

    public function test_can_get_active_subscriber_count(): void
    {
        $this->mockClientResponse('100');

        $result = Sendy::getActiveSubscriberCount('test-list-id');

        $this->assertEquals(100, $result);
    }

    public function test_throws_exception_on_error(): void
    {
        $this->mockClientResponse('Error: API key not passed');

        $this->expectException(SendyException::class);
        $this->expectExceptionMessage('Error: API key not passed');

        Sendy::subscribe('test-list-id', 'test@example.com');
    }

    public function test_can_create_campaign(): void
    {
        $this->mockClientResponse('Campaign created and now sending');

        $result = Sendy::createCampaign(
            'John Doe',
            'john@example.com',
            'reply@example.com',
            'Test Campaign',
            'Test Subject',
            'Plain text content',
            '<p>HTML content</p>',
            'list-id',
            'brand-id',
            'utm_source=newsletter'
        );

        $this->assertTrue($result);
    }

    public function test_can_create_draft_campaign(): void
    {
        $this->mockClientResponse('Draft campaign created');

        $result = Sendy::createDraftCampaign(
            'John Doe',
            'john@example.com',
            'reply@example.com',
            'Test Campaign',
            'Test Subject',
            'Plain text content',
            '<p>HTML content</p>',
            ['list-id-1', 'list-id-2'],
            'brand-id',
            'utm_source=newsletter'
        );

        $this->assertTrue($result);
    }

    public function test_can_schedule_campaign(): void
    {
        $this->mockClientResponse('Campaign scheduled');

        $result = Sendy::scheduleCampaign(
            'John Doe',
            'john@example.com',
            'reply@example.com',
            'Test Campaign',
            'Test Subject',
            'Plain text content',
            '<p>HTML content</p>',
            'list-id',
            '2024-12-31 23:59:59',
            'brand-id',
            'utm_source=newsletter'
        );

        $this->assertTrue($result);
    }

    public function test_can_delete_subscriber(): void
    {
        $this->mockClientResponse('Subscriber deleted');

        $result = Sendy::deleteSubscriber('test-list-id', 'test@example.com');

        $this->assertTrue($result);
    }

    public function test_can_get_subscriber_count_by_status(): void
    {
        $this->mockClientResponse('150');

        $result = Sendy::getSubscriberCountByStatus('test-list-id', 'active');

        $this->assertEquals(150, $result);
    }

    public function test_can_get_total_active_subscribers(): void
    {
        $this->mockClientResponse('500');

        $result = Sendy::getTotalActiveSubscribers('brand-id');

        $this->assertEquals(500, $result);
    }

    public function test_can_update_subscriber(): void
    {
        $this->mockClientResponse('Subscriber updated');

        $result = Sendy::updateSubscriber(
            'test-list-id',
            'test@example.com',
            'Updated Name',
            ['custom_field' => 'new_value']
        );

        $this->assertTrue($result);
    }
} 