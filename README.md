# Aphpi Template

Starter Template for PHP APIs

## Installation

```
git clone https://github.com/aphpi/aphpi.git example-api
cd example-api
composer install
```

Replace "Aphpi\Template" with your namespace in all files

## Usage

1. Change Api->getClient

src/Api.php
```php
protected function getClient() : Client
{
    return new Client([        
        'base_uri' => 'http://httpbin.org',
        'timeout'  => 2.0,
    ]);
}
```

2. Create Endpoints in src/Api
3. Set Endpoint

src/Api.php
```php 
protected function setEndpoints(Client $client) : void
{
    $this->example = new Example($client);
}
```

4. Make Call

```php
$api = new Api();
$response = $api->example->post();
```
