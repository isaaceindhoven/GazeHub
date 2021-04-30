# GazePublisher
GazePublisher is a PHP library that is used in your project's backend to communicate to GazeHub.

## Installation
GazeHub can be installed using [Composer](https://getcomposer.org/) with the following command:

```bash
composer require isaac/gaze-publisher
```

## Configuration
!>**Before continuing you should read [Authentication](authentication.md).**

### GazePublisher instance
?>It is recommended to register the `GazePublish` in your framework's DI container and load the config variables from a config file. Take a look at the [example](example) page.

```php
$privateKeyContent = file_get_contents('./private.key');
$gaze = new GazePublisher('http://localhost:3333', $privateKeyContent);
```

## Usage
### Emitting

```php
// send event to all
$gaze->emit('ProductCreated', $product);

// send to clients with 'admin' or 'sales' role
$gaze->emit('ProductCreated', $product, ['admin', 'sales']); 
```

### Error handling

It may occur that the emit will fail for numerous reasons such as the wrong huburl, no emit permission because of a faulty token, hub is offline, etc. To combat this you can provide a custom errorhandler in the constructor or wrap the `emit` in a `try-catch` block. GazePublisher comes with 2 built-in error handlers `IgnoringErrorHandler` and the default `RethrowingErrorHandler`. The `IgnoringErrorHandler` will ignore the exception and `RethrowingErrorHandler` will throw the exception.


**Example 1: Wrapping the code in a try-catch block**
```php
try {
    $gaze->emit('ProductCreated', $newProduct);
} catch(GazeException $e) {
    // your code ...
}
```

**Example 2: Providing a custom errorhandler (this example will POST to Sentry). Note that you won't have to wrap the emit code in a try catch block anymore**
```php
// ExternalLogErrorHandler.php
use ISAAC\GazePublisher\ErrorHandlers\ErrorHandler;

class ExternalLogErrorHandler implements ErrorHandler
{
    public function handleException(Exception $exception): void
    {
        \Sentry\captureException($exception);
    }
}

// YOUR-FILE.php
$gaze = new GazePublisher($hubUrl, $privateKeyContents, $maxRetries, new ExternalLogErrorHandler());
$gaze->emit('ProductCreated', $newProduct);
```

## Development
| Command | Description |
| ------- | ----------- |
| `./vendor/bin/phpunit` | Runs [PHPUnit](https://phpunit.de/) tests that are present in `/tests/`. |
| `./vendor/bin/phpunit --coverage-html coverage` | Runs the [PHPUnit](https://phpunit.de/) tests and outputs the code coverage to `coverage/index.html`. |
| `./vendor/bin/phpstan` | Runs [PHPStan](https://github.com/phpstan/phpstan) Analysis Tool. |
| `./vendor/bin/phpcs` | Runs [PHP Codesniffer](https://github.com/squizlabs/PHP_CodeSniffer). |
| `./vendor/bin/phpcbf` | Runs code beautifier. |
