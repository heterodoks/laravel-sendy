<?php

namespace Heterodoks\LaravelSendy;

use GuzzleHttp\Client;
use Heterodoks\LaravelSendy\Exceptions\SendyException;
use Illuminate\Support\Facades\Log;

class Sendy
{
    protected Client $client;
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->client = new Client([
            'base_uri' => rtrim($config['url'], '/') . '/',
            'timeout' => $config['timeout'],
        ]);
    }

    public function subscribe(string $listId, string $email, string $name = '', array $customFields = [], bool $gdprConsent = true): bool
    {
        $data = [
            'list' => $listId,
            'email' => $email,
            'name' => $name,
            'boolean' => $gdprConsent ? 'true' : 'false'
        ];

        // Merge custom fields directly into the request parameters
        $data = array_merge($data, $customFields);

        $response = $this->post('subscribers/subscribe', $data);

        return $response === '1';
    }

    public function unsubscribe(string $listId, string $email): bool
    {
        $response = $this->post('subscribers/unsubscribe', [
            'list' => $listId,
            'email' => $email,
        ]);

        return $response === '1';
    }

    public function getSubscriptionStatus(string $listId, string $email): string
    {
        return $this->post('subscribers/subscription-status', [
            'list' => $listId,
            'email' => $email,
        ]);
    }

    public function getActiveSubscriberCount(string $listId): int
    {
        $response = $this->post('subscribers/active-subscriber-count', [
            'list' => $listId,
        ]);

        return (int) $response;
    }

    /**
     * Create and send a campaign immediately
     */
    public function createCampaign(
        string $fromName,
        string $fromEmail,
        string $replyTo,
        string $title,
        string $subject,
        string $plainText,
        string $htmlText,
        string|array $listIds,
        ?string $brandId = null,
        ?string $queryString = null
    ): bool {
        $response = $this->post('campaigns/create', [
            'from_name' => $fromName,
            'from_email' => $fromEmail,
            'reply_to' => $replyTo,
            'title' => $title,
            'subject' => $subject,
            'plain_text' => $plainText,
            'html_text' => $htmlText,
            'list_ids' => is_array($listIds) ? implode(',', $listIds) : $listIds,
            'brand_id' => $brandId ?? $this->config['brand_id'],
            'query_string' => $queryString,
        ]);

        return str_contains($response, 'Campaign created and now sending');
    }

    /**
     * Create a draft campaign
     */
    public function createDraftCampaign(
        string $fromName,
        string $fromEmail,
        string $replyTo,
        string $title,
        string $subject,
        string $plainText,
        string $htmlText,
        string|array $listIds,
        ?string $brandId = null,
        ?string $queryString = null
    ): bool {
        $response = $this->post('campaigns/create-draft', [
            'from_name' => $fromName,
            'from_email' => $fromEmail,
            'reply_to' => $replyTo,
            'title' => $title,
            'subject' => $subject,
            'plain_text' => $plainText,
            'html_text' => $htmlText,
            'list_ids' => is_array($listIds) ? implode(',', $listIds) : $listIds,
            'brand_id' => $brandId ?? $this->config['brand_id'],
            'query_string' => $queryString,
        ]);

        return str_contains($response, 'Draft campaign created');
    }

    /**
     * Schedule a campaign for future sending
     */
    public function scheduleCampaign(
        string $fromName,
        string $fromEmail,
        string $replyTo,
        string $title,
        string $subject,
        string $plainText,
        string $htmlText,
        string|array $listIds,
        string $datetime,
        ?string $brandId = null,
        ?string $queryString = null
    ): bool {
        $response = $this->post('campaigns/schedule', [
            'from_name' => $fromName,
            'from_email' => $fromEmail,
            'reply_to' => $replyTo,
            'title' => $title,
            'subject' => $subject,
            'plain_text' => $plainText,
            'html_text' => $htmlText,
            'list_ids' => is_array($listIds) ? implode(',', $listIds) : $listIds,
            'brand_id' => $brandId ?? $this->config['brand_id'],
            'query_string' => $queryString,
            'send_campaign' => $datetime, // Format: YYYY-MM-DD HH:MM:SS
        ]);

        return str_contains($response, 'Campaign scheduled');
    }

    /**
     * Delete a subscriber
     */
    public function deleteSubscriber(string $listId, string $email): bool
    {
        $response = $this->post('subscribers/delete', [
            'list' => $listId,
            'email' => $email,
        ]);

        return str_contains($response, 'Subscriber deleted');
    }

    /**
     * Get subscriber count by status
     */
    public function getSubscriberCountByStatus(string $listId, string $status): int
    {
        $response = $this->post('subscribers/count', [
            'list' => $listId,
            'status' => $status, // Options: active, unconfirmed, unsubscribed, bounced, complained
        ]);

        return (int) $response;
    }

    /**
     * Get total active subscriber count across all lists
     */
    public function getTotalActiveSubscribers(?string $brandId = null): int
    {
        $response = $this->post('subscribers/total-active', [
            'brand_id' => $brandId ?? $this->config['brand_id'],
        ]);

        return (int) $response;
    }

    /**
     * Update subscriber
     */
    public function updateSubscriber(
        string $listId,
        string $email,
        ?string $name = null,
        array $customFields = []
    ): bool {
        $data = [
            'list' => $listId,
            'email' => $email,
        ];

        if ($name !== null) {
            $data['name'] = $name;
        }

        if (!empty($customFields)) {
            $data['custom_fields'] = $customFields;
        }

        $response = $this->post('subscribers/edit', $data);

        return str_contains($response, 'Subscriber updated');
    }

    protected function post(string $endpoint, array $data): string
    {
        $data['api_key'] = $this->config['api_key'];

        $endpoints = [
            'subscribers/subscribe' => 'subscribe',
            'subscribers/unsubscribe' => 'unsubscribe',
            'subscribers/subscription-status' => 'api/subscribers/subscription-status',
            'subscribers/active-subscriber-count' => 'api/subscribers/active-subscriber-count',
            'subscribers/delete' => 'api/subscribers/delete',
            'subscribers/edit' => 'api/subscribers/edit',
            'subscribers/total-active' => 'api/subscribers/total-active',
            'campaigns/create' => 'api/campaigns/create',
            'campaigns/create-draft' => 'api/campaigns/create-draft',
            'campaigns/schedule' => 'api/campaigns/schedule'
        ];

        $path = $endpoints[$endpoint] ?? $endpoint;

        // Debug the actual request data
        Log::channel('daily')->info('Sendy API Request', [
            'endpoint' => $path,
            'data' => $data
        ]);

        try {
            $response = $this->client->post($path, [
                'form_params' => $data,
                'debug' => true  // This will log the actual HTTP request
            ]);

            $body = (string) $response->getBody();

            // Debug the response
            Log::channel('daily')->info('Sendy API Response', [
                'body' => $body
            ]);

            if (str_contains(strtolower($body), 'error')) {
                throw new SendyException($body);
            }

            return $body;
        } catch (\Exception $e) {
            throw new SendyException($e->getMessage(), 0, $e);
        }
    }
} 