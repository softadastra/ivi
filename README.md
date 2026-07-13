<table>
  <tr>
    <td valign="top" width="65%">

<h1>Ivi.php</h1>

<p>
  <img src="https://img.shields.io/badge/PHP-8.2+-blue">
  <img src="https://img.shields.io/badge/License-MIT-green">
</p>

<h3>Build backend systems with clarity.</h3>

<p>
  Ivi.php is a modern PHP framework designed for developers who want a clean structure, predictable behavior, and fast development without unnecessary complexity.
</p>

</td>

<td valign="middle" width="30%" align="right">

<img
  src="https://res.cloudinary.com/dwjbed2xb/image/upload/v1762524618/iviphp_jrpema.png"
  width="200"
  style="border-radius:10px; object-fit:cover;"
/>

</td>
  </tr>
</table>

## Overview

Ivi.php is a modern PHP framework designed for building APIs and web applications with clarity and control.

It provides a minimal core with a consistent architecture, allowing developers to build production-ready systems without unnecessary complexity.

The framework focuses on:

- predictable structure
- explicit behavior
- fast development cycles
- real-world features out of the box

## Installation

```bash
composer create-project softadastra/ivi my-app
cd my-app
```

Run the application:

```bash
ivi serve
```

## Quick Example

```php
use Ivi\Core\Bootstrap\App;
use Ivi\Http\Request;

$app = new App(__DIR__);

$app->router->get('/', fn() => ['hello' => 'ivi.php']);

$app->router->post('/echo', fn(Request $req) => [
    'you_sent' => $req->json()
]);

$app->run();
```

## Routing

```php
$app->router->get('/users', fn() => ['users' => []]);

$app->router->get('/user/{name}', function (array $params) {
    return ['name' => $params['name']];
});
```

## Request Handling

```php
$app->router->post('/data', function (Request $req) {
    return [
        'json' => $req->json(),
        'all'  => $req->all()
    ];
});
```

## Views

```php
use Ivi\Core\View\View;

$app->router->get('/', function () {
    return View::make('home', [
        'title'   => 'Welcome',
        'message' => 'Ivi.php running'
    ]);
});
```

## Validation

```php
use Ivi\Core\Validation\Validator;

$input = [
    'email'    => 'user@example.com',
    'password' => 'secret123'
];

$validated = (new Validator($input, [
    'email'    => 'required|email',
    'password' => 'required|min:6'
]))->validate();
```

Update scenario:

```php
$post = ['password' => ''];

if (trim($post['password']) === '') {
    unset($post['password']);
}

$validated = (new Validator($post, [
    'password' => 'sometimes|min:6'
]))->validate();
```

## ORM

### Model

```php
use Ivi\Core\ORM\Model;

final class User extends Model
{
    protected static array $fillable = ['name', 'email', 'password', 'active'];
}
```

### CRUD

```php
$user = User::create([
    'name'  => 'Alice',
    'email' => 'alice@example.com'
]);

$found = User::find(1);

$found->fill(['name' => 'Updated'])->save();

$found->delete();
```

### Query Builder

```php
$users = User::query()
    ->where('status = ?', 'active')
    ->orderBy('id DESC')
    ->limit(5)
    ->get();

$count = User::query()
    ->where('status = ?', 'active')
    ->count();
```

### Repository Pattern

```php
use Ivi\Core\ORM\Repository;

final class UserRepository extends Repository
{
    protected function modelClass(): string
    {
        return User::class;
    }

    public function findByEmail(string $email): ?User
    {
        $row = User::query()->where('email = ?', $email)->first();
        return $row ? new User($row) : null;
    }
}
```

## JWT Authentication

```php
use Ivi\Core\Jwt\JWT;

$jwt = new JWT();

$token = $jwt->generate([
    'sub' => 123
], [
    'key'      => 'secret',
    'alg'      => 'HS256',
    'validity' => 3600
]);

$jwt->check($token, ['key' => 'secret']);
```

## Logging

```php
log_info("Application started");

log_error("Database error", "Database");

log_debug([
    'user_id' => 1
], "Debugging");
```

Features:

- daily log rotation
- JSON support
- trace mode
- automatic log directory creation

## Collections

```php
$v = vector([1, 2, 3]);
$v->push(4);

$m = hashmap(['name' => 'Ivi']);
$m->put('version', '1.0');

$s = hashset(['apple']);
$s->add('banana');

$t = str(" hello ")->trim()->upper();
```

## CLI

Ivi.php provides a built-in CLI for development and deployment.

#### Project
```bash
ivi new my-app
```

#### Database
```bash
ivi migrate
ivi migrate:status
ivi migrate:reset
ivi seed
```

#### Modules
```bash
ivi make:module Blog
ivi modules:publish-assets
```

#### Development
```bash
ivi serve
ivi test
ivi coverage
```

#### Deployment
```bash
ivi deploy
```

## Project Structure

```
.
├── bootstrap/
├── config/
├── core/
├── public/
├── src/
├── views/
├── scripts/
├── docs/
└── vendor/
```

## Configuration

Example `.env`:

```env
APP_ENV=local
APP_DEBUG=true

DB_DRIVER=mysql
DB_HOST=127.0.0.1
DB_NAME=ivi
DB_USER=root
DB_PASS=secret
```

## Philosophy

Ivi.php is built around a simple idea:

- keep the core minimal
- expose real capabilities
- avoid hidden magic
- favor explicit code over abstraction

## Documentation

[https://ivi.softadastra.com/docs](https://ivi.softadastra.com/docs)

## Download

[https://github.com/softadastra/ivi](https://github.com/softadastra/ivi)

```bash
git clone https://github.com/softadastra/ivi.git
cd ivi
composer install
```

## License

## 10) Validation

```php
use Ivi\Http\Request;
use Ivi\Validation\Validator;

$validator = Validator::make($request->all(), [
  'name' => 'required|min:2|max:120',
  'email' => 'required|email',
]);

if ($validator->fails()) {
  return response()->json(['errors' => $validator->errors()], 422);
}
```

## 11) Responses

```php
use Ivi\Http\JsonResponse;
use Ivi\Http\HtmlResponse;

return new JsonResponse(['ok' => true]);
return new HtmlResponse('<h1>Hello</h1>');
```

## 12) Production Tips

- Set `APP_ENV=production`
- Use `APP_DEBUG=false`
- Configure opcache
- Serve from `public/`
- Minify assets

---

Happy building with **ivi.php** 🚀

## ⚖️ License

MIT License © 2026 Gaspard Kirira
