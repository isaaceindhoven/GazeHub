# Complete install

It can be a bit daunting to get a grasp of how all of Gaze comes together. On this page we will walk you through a generic PHP example. Before following the tutorial make sure you have the following tools installed: `PHP 7.3`, `NPM` and `Composer`.

### Installing GazeHub and GazePublisher
```bash
composer require isaac/gaze-hub isaac/gaze-publisher
```

### Public/Private keypair

Run in your project root:
```bash
# Generate private key
openssl genrsa -out private.key 4096

# Extract public key from private key
openssl rsa -in private.key -outform PEM -pubout -out public.key
```

!> Make sure the **private key** never leaves the machine it will be used on and the keys are added to the `.gitignore` file.

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

<!-- tabs:start -->

### **Generic PHP Project**

Most frameworks have a `.env` file in the root.
[GazePublisher](gazepublisher) needs 2 important settings to function:

```env
# the host and port where the hub is hosted (same as gazehub.config.json)
GAZEHUB_URL='http://0.0.0.0:3333'

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

### **Symfony**

TODO

### **Laravel**


#### Adding settings to `.env`

Add the `GAZEHUB_URL` and `GAZEHUB_PRIVATE_KEY` setting to your `.env` file in the root.

```env
# the host and port where the hub is hosted (same as gazehub.config.json)
GAZEHUB_URL='http://0.0.0.0:3333'

# paste here the contents of the 'private.key' file that you generated
GAZEHUB_PRIVATE_KEY="PRIVATE KEY CONTENTS"
```

Create a `config/gaze.php` file with the following contents:

```php
return [
    'hub_url' => env('GAZEHUB_URL'),
    'private_key' => env('GAZEHUB_PRIVATE_KEY')
];
```

#### Adding `GazePublisher` to your container

Create a `GazeProvider` with the following command and `register` method:

```bash
php artisan make:provider GazeProvider
```

```php
// file: app/Providers/GazeProvider.php
public function register()
{
    $this->app->singleton(GazePublisher::class, function () {
        return new GazePublisher(config('gaze.hub_url'), config('gaze.private_key'));
    });
}
```

Register the provider in `config/app.php`:
```php
"providers" => [
    ...
    App\Providers\GazeProvider::class,
]
```

<!-- tabs:end -->


### Create `/token` URL

<!-- tabs:start -->

#### **Generic PHP Project**

GazeHub has no clue about your backend authorization. The user (browser) needs to connect with GazeHub using a JWT that has been provided by the backend. You'll need to make a route in your backend that provides the JWT to the user. In the example we will use a `/token` endpoint. The GazePublisher instance `$this->gaze` was provided using dependency injection.

```php
// @route('/token')
public function token(Request $request){
    $roles = $request->user()->getRoles(); // ['admin', 'sales']
    return ['token' => $this->gaze->generateClientToken($roles)];
}
```

#### **Symfony**

TODO

#### **Laravel**

Create a `GazeTokenController` with the following command and code:

```bash
php artisan make:controller GazeTokenController
```

```php
class GazeTokenController extends Controller
{
    public function __invoke(GazePublisher $gazePublisher)
    {
        return ['token' => $gazePublisher->generateClientToken()];
    }
}
```

Add the `/token` endpoint with the controller to your `web.php` file:
```php
Route::get('/token', GazeTokenController::class);
```

<!-- tabs:end -->



### Installing GazeClient

You can use GazeClient via a CDN or running `npm install @isaac.frontend/gaze-client`. We will take the CDN approach.

```html
<script src="https://unpkg.com/@isaac.frontend/gaze-client/dist/GazeClient.js"></script>
```

```js
// get the JWT from the backend
const tokenRequest = await fetch('/token');
const tokenRequestJson = await tokenRequest.json();

// connect to gaze
const gaze = new GazeClient('http://localhost:3333/');
await gaze.connect();
await gaze.authenticate(tokenRequestJson.token);
```

### Run GazeHub

Great! We can now start GazeHub by running the command `GAZEHUB_JWT_PUBLIC_KEY=$(cat public.key) ./vendor/bin/gazehub`.

?> Take a look at the [GazeHub](gazehub) page if you want to start GazeHub using Docker, Supervisor or systemd.

### Subscribing to a topic

We will finish by subscribing to a topic and receiving its emit payload from the backend.

```js
gaze.on('ProductCreated', product => {
    alert(`New product ${product.name} added`);
});
```

```php
// @route('/product', 'post')
public function product(Request $request){
    $product = $this->productRepo()->save($request->data());
    %$this->gaze->emit('ProductCreated', $product);
    return $product;
}
```

### The full picture

Below is a diagram that visualizes the whole data flow between the libraries, your browser and your backend.

![Diagram](diagram.svg)