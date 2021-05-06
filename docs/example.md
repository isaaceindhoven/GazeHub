# Example

It can be a bit daunting to get a grasp of how all of Gaze comes together. On this page we will walk you through a generic PHP example.

## System requirements
| Name | Check Version
| --- | --- |
| PHP | `php -v` |
| NodeJS | `npm -v` |
| Composer | `composer -V` |

## Installation
```bash
composer require isaac/gaze-hub isaac/gaze-publisher
```

## Public/Private keypair

!> Make sure the **private key** never leaves the machine it will be used on and the keys are added to the `.gitignore` file.

Run in your project root:
```bash
# Generate private key
openssl genrsa -out private.key 4096

# Extract public key from private key
openssl rsa -in private.key -outform PEM -pubout -out public.key
```

## Configuring

### GazeHub configuration
Create a `gazehub.config.json` file in your project root with the following contents.
```json
{
    "port": 3333,
    "host": "0.0.0.0"
}
```

?> Take a look at the [GazeHub](gazehub) page for more configuration options.

### GazePublisher configuration

Most frameworks contain a `.env` file in the root.
[GazePublisher](gazepublisher) needs 2 important settings to function:

```env
# the host and port where the hub is hosted (same as gazehub.config.json)
GAZEHUB_URL='0.0.0.0:3333'

# paste here the contents of the 'private.key' file that you generated
GAZEHUB_PRIVATE_KEY="PRIVATE KEY CONTENTS"
```

We highly recommend that you register the `GazePublisher` instance into your framework's DI container. Below is a generic example.

```php
public function register(Container $container){
    $hubUrl = getenv('GAZEHUB_URL');
    $privateKey = getenv('GAZEHUB_PRIVATE_KEY');
    $gaze = new GazePublisher($hubUrl, $privateKey);
    $container->register(GazePublisher::class, $gaze);
}
```

## Create `/token` URL

GazeHub has no clue about your backend authorization. The user (browser) needs to connect with GazeHub using a JWT that has been provided by the backend. You'll need to make a route in your backend that provides the JWT to the user. In the example we will use a `/token` endpoint. The GazePublisher instance `$this->gaze` was provided using dependency injection.

```php
// @route('/token')
public function token(Request $request){
    return ['token' => $this->gaze->generateClientToken($request->user()->getRoles())];
}
```

## Installing GazeClient

You can use GazeClient via a CDN or running `npm install @isaac.frontend/gaze-client`. We will take the CDN approach.

```html
<head>
    <script src="https://unpkg.com/@isaac.frontend/gaze-client/dist/GazeClient.js" defer></script>
</head>
```

```js
// get the JWT from the backend
const tokenRequest = await fetch('/token');
const tokenRequestJson = await tokenRequest.json();

// connect to gaze
const gaze = new GazeClient('http://localhost:3333/');
await gaze.connect();
await gaze.setToken(tokenRequestJson.token);
```

## Run GazeHub

Great! We can now start GazeHub by running the command `GAZEHUB_JWT_PUBLIC_KEY=$(cat public.key) ./vendor/bin/gazehub`.

## Subscribing to a topic

We will finish by subscribing to a topic and receiving its emit payload from the backend.

### Javascript code (receiving)

```js
gaze.on('ProductCreated', product => {
    alert(`New product ${product.name} added`);
});
```

### PHP code (emitting)
```php
// @route('/product', 'post')
public function product(Request $request){
    $product = (new Product())->fill($request->data())->save();
    $this->gaze->emit('ProductCreated', $product);
    return $product;
}
```
