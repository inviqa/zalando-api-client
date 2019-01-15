# Zalando API Client

The purpose of this client API library is to provide access to the Zalando eCommerce merchant platform. The Zalando API
is REST based, exchanges information in JSON format, and uses OAuth 2 for authentication.

- [Zalando Merchant API Documentation](https://developers.merchants.zalando.com/docs/)

## Supported API Requests and Planned Features
- [x] OAuth 2 authentication.
- [ ] Article price update.

## Installation
Install with Composer
```bash
composer install
```

For inclusion in your project add the repository to the array of repositories in `composer.json`
```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "git@github.com:inviqa/zalando-api-client.git"
    }
  ]
}
```
Then require the Zalando API client library
```bash
composer require inviqa/zalando-api-client
```

## Running the Automated Test Suite
For the Behat integration test suite to pass working sandbox connectivity details should be provided in the YAML
configuration file located at `tests/config/integration.yml`.
```bash
bin/phpspec r
bin/behat

# Behat domain test suite only
bin/behat -s domain

# Behat integration test suite only
bin/behat -s integration
```

## Usage

#### Create a class that implements the `\Inviqa\Zalando\Api\ZalandoConfiguration` interface. See example below:
```php
<?php
use Inviqa\Zalando\Api\ZalandoConfiguration;

class MyConfiguration implements ZalandoConfiguration
{
    public function isTestMode(): bool { return false; }
    public function getMerchantId(): string { return 'merchant_id'; }
    public function getAuthenticationParametersFilePath(): string { return 'path/to/zalando/parameters/authentication.yml'; }
    public function getAuthenticationEndpointUrl(): string { return 'https://api-sandbox.merchants.zalando.com/auth/token'; }
    public function getArticlePriceUpdateEndpointUrl(): string { return 'https://api-sandbox.merchants.zalando.com/merchants/%s/article-price'; }
    public function getUsername(): string { return 'client_username'; }
    public function getSecret(): string { return 'client_secret'; }
}
```

#### Instantiate the library application class and inject an instance of the above configuration
An optional logger instance implementing `\Psr\Log\LoggerInterface` may be provided as the second argument to the
Application.
```php
<?php
use Inviqa\Zalando\Application;

$app = new Application(new MyConfiguration());
```

#### Authentication
```php
$authenticationParameters = $app->authenticate();
```
