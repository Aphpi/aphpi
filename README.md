# Aphpi Template

Starter Template for PHP APIs

## Installation

### Installer

First, download the installer using Composer:

```
composer global require aphpi/installer
```

Make sure to place Composer's system-wide vendor bin directory in your $PATH so the aphpi executable can be located by your system. This directory exists in different locations based on your operating system; however, some common locations include:

- macOS: $HOME/.composer/vendor/bin
- Windows: %USERPROFILE%\AppData\Roaming\Composer\vendor\bin
- GNU / Linux Distributions: $HOME/.config/composer/vendor/bin or $HOME/.composer/vendor/bin

You could also find the composer's global installation path by running composer global about and looking up from the first line.

Once installed, the aphpi new command will create a fresh aphpi installation in the directory you specify.

```
aphpi new github-api Your\\Namespace\\Github
```

### Manual

```
git clone https://github.com/aphpi/aphpi.git example-api
cd example-api
composer install
```

Replace "Aphpi\Template" with your namespace in all files:

- src/*/.php
- tests/*/.php
- composer.json

## Usage

1. Update Api->getClient

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

2. Create Endpoints in src/Endpoints
3. Set Endpoint in Api.php

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
