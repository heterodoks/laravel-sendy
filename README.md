# Laravel Sendy

A Laravel package for integrating with the Sendy API.

## Requirements

- PHP 8.1 or higher
- Laravel 10.x|11.x
- Sendy 6.1

## Installation

You can install the package via composer:

  ```bash
  composer require heterodoks/laravel-sendy
  ```

The package will automatically register its service provider.

## Configuration

Publish the configuration file:

  ```bash
  php artisan vendor:publish --tag="sendy-config"
  ```

Add these variables to your .env file:

  ```env
  SENDY_URL=your-sendy-installation-url
  SENDY_API_KEY=your-api-key
  SENDY_BRAND_ID=your-brand-id
  SENDY_TIMEOUT=10
  ```

## Usage

### Subscribe a User

  ```php
  use Heterodoks\LaravelSendy\Facades\Sendy;

  // Basic subscription
  $result = Sendy::subscribe('list_id', 'user@example.com');

  // With additional details
  $result = Sendy::subscribe(
      'list_id',
      'user@example.com',
      'John Doe',
      ['custom_field' => 'value'],
      true // GDPR consent
  );
  ```

### Unsubscribe a User

  ```php
  $result = Sendy::unsubscribe('list_id', 'user@example.com');
  ```

### Check Subscription Status

  ```php
  $status = Sendy::getSubscriptionStatus('list_id', 'user@example.com');
  // Returns: "Subscribed", "Unsubscribed", "Unconfirmed", "Bounced", "Soft bounced", or "Complained"
  ```

### Get Active Subscriber Count

  ```php
  $count = Sendy::getActiveSubscriberCount('list_id');
  ```

### Campaign Management

#### Create and Send Campaign

  ```php
  $result = Sendy::createCampaign(
      'John Doe',                  // From Name
      'john@example.com',         // From Email
      'reply@example.com',        // Reply To
      'Campaign Title',           // Title
      'Email Subject',            // Subject
      'Plain text version',       // Plain Text
      '<p>HTML version</p>',      // HTML Text
      'list-id',                  // List ID or array of List IDs
      'brand-id',                 // Optional: Brand ID
      'utm_source=newsletter'     // Optional: Query String
  );  ```

#### Create Draft Campaign

  ```php
  $result = Sendy::createDraftCampaign(
      'John Doe',
      'john@example.com',
      'reply@example.com',
      'Campaign Title',
      'Email Subject',
      'Plain text version',
      '<p>HTML version</p>',
      ['list-id-1', 'list-id-2'], // Multiple lists
      'brand-id',
      'utm_source=newsletter'
  );  ```

#### Schedule Campaign

  ```php
  $result = Sendy::scheduleCampaign(
      'John Doe',
      'john@example.com',
      'reply@example.com',
      'Campaign Title',
      'Email Subject',
      'Plain text version',
      '<p>HTML version</p>',
      'list-id',
      '2024-12-31 23:59:59',     // Schedule datetime
      'brand-id',
      'utm_source=newsletter'
  );  ```

### Subscriber Management

#### Delete Subscriber

  ```php
  $result = Sendy::deleteSubscriber('list_id', 'user@example.com');
  ```

#### Get Subscriber Count by Status

  ```php
  // Available statuses: active, unconfirmed, unsubscribed, bounced, complained
  $count = Sendy::getSubscriberCountByStatus('list_id', 'active');
  ```

#### Get Total Active Subscribers

  ```php
  // Get total active subscribers for default brand
  $total = Sendy::getTotalActiveSubscribers();

  // Get total active subscribers for specific brand
  $total = Sendy::getTotalActiveSubscribers('brand-id');
  ```

#### Update Subscriber

  ```php
  // Basic update
  $result = Sendy::updateSubscriber('list_id', 'user@example.com', 'New Name');

  // Update with custom fields
  $result = Sendy::updateSubscriber(
      'list_id',
      'user@example.com',
      'New Name',
      ['custom_field' => 'new_value']
  );
  ```

## Testing

  ```bash
  composer test
  ```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.