# GazePublisher
[GazePublisher](https://github.com/isaaceindhoven/GazePublisher) is a PHP library that is used in your project's backend to communicate to GazeHub.<br/>See the [Complete install](complete-install.md) page for a detailed guide of how to install Gaze.

### GazePublisher instance
?>It is recommended to register the `GazePublish` in your framework's DI container and load the constructor variables from a config file. Take a look at the [Complete install](complete-install) page.

```php
$privateKeyContent = file_get_contents('./private.key');
$gaze = new GazePublisher('http://localhost:3333', $privateKeyContent);
```

### Emitting

<!-- tabs:start -->

#### **To everyone**

```php
$gaze->emit('ProductCreated', $product);
```

#### **To a specific role**

```php
$gaze->emit('ProductCreated', $product, 'admin'); 
```

<!-- tabs:end -->

?> The payload parameter (in the example `$product`) must be JSON encodable.

### Error handling

It may occur that the emit will fail for numerous reasons such as the wrong huburl, no emit permission because of a faulty token, hub is offline, etc. To combat this you can provide a custom errorhandler in the constructor or wrap the `emit` in a `try-catch` block. GazePublisher comes with 2 built-in error handlers `IgnoringErrorHandler` and the default `RethrowingErrorHandler`. The `IgnoringErrorHandler` will ignore the exception and `RethrowingErrorHandler` will throw the exception.

<!-- tabs:start -->

#### **try-catch block**

```php
try {
    $gaze->emit('ProductCreated', $newProduct);
} catch(GazeException $e) {
    // your code ...
}
```

#### **Custom ErrorHandler**

Providing a custom errorhandler (this example will POST to Sentry). Note that you won't have to wrap the emit code in a try catch block anymore.

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

<!-- tabs:end -->
