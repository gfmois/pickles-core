# Pickles Framework

A simple, modern PHP framework inspired by Laravel. Pickles provides a clean structure for building web applications, including routing, controllers, models, migrations, validation, session management, and a view engine.

---

## Features

- **MVC Structure**: Organize your code with Models, Views, and Controllers.
- **Routing**: Define routes for your application with support for middleware.
- **Database ORM**: Simple model system with fillable/hidden attributes and query helpers.
- **Migrations**: Create, run, and rollback database migrations via CLI.
- **Validation**: Powerful validation rules and custom error messages.
- **Session Management**: Flash data, session storage abstraction.
- **View Engine**: Lightweight PHP-based templating.
- **Service Providers**: Register and configure services for your app.
- **Extensible**: Easily add your own providers, middleware, and helpers.

---

## Requirements

- PHP 8.1 or higher
- Composer
- PDO extension (for database access)
- Supported databases: MySQL, PostgreSQL

---

## Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/gfmois/pickles-framework-1.git
   cd pickles-framework-1
   ```

2. **Install dependencies:**
   ```bash
   composer install
   ```

3. **Copy and edit your environment file:**
   ```bash
   cp .env.example .env
   # Edit .env to match your database and app settings
   ```

4. **Set up git hooks (optional):**
   ```bash
   chmod +x setup-git.sh
   ./setup-git.sh
   ```

---

## Configuration

All configuration files are in the `config/` directory:

- `app.php`: App name, environment, URL, version.
- `database.php`: Database connection settings.
- `session.php`: Session storage driver.
- `view.php`: View engine and path.
- `providers.php`: Service providers for boot/runtime/CLI.

You can use environment variables in your `.env` file to override config values.

---

## Usage

### Running the Application

Start the built-in PHP server from the `public/` directory:

```bash
php -S localhost:8080 -t public
```

Visit [http://localhost:8080](http://localhost:8080) in your browser.

### Routing Example

Define routes in `routes/web.php` or directly in your entry file:

```php
use Pickles\Http\Request;
use Pickles\Routing\Route;

Route::GET('/hello', function(Request $request) {
    return 'Hello, Pickles!';
});
```

### Controllers & Models

Create controllers in `app/Controllers/` and models in `app/Models/`. Example model:

```php
class User extends Model {
    public array $fillable = ['name', 'email'];
}
```

---

## Database & Migrations

### Creating a Migration

```bash
php pickles.php make:migration create_users_table
```

Edit the generated file in `database/migrations/`.

### Running Migrations

```bash
php pickles.php migrate
```

### Rolling Back Migrations

```bash
php pickles.php migration:rollback [steps]
```
- Omit `[steps]` to rollback all, or provide a number to rollback N migrations.

---

## Validation

Use validation rules in your controllers or route handlers:

```php
$data = $request->validate([
    'email' => 'required|email',
    'password' => 'required|min:8',
]);
```

---

## Testing

Run the test suite with PHPUnit:

```bash
composer test
```

Tests are located in the `tests/` directory.

---

## Contributing

1. Fork the repository.
2. Create your feature branch (`git checkout -b feature/your-feature`).
3. Commit your changes.
4. Push to the branch (`git push origin feature/your-feature`).
5. Open a pull request.

---

## License

This project is licensed under the MIT License.

---

## Author

Moisés Guerola  
[Contact](mailto:daw.moisesguerola@gmail.com)

---

# Configure githooks
## Launch Scrip
```bash
chmod -x setup-git.sh
./setup-git.sh
```

## Or configure it manually

### Add permissions to pre-commit hook
```bash
chmod +x .githooks/pre-commit
```

### Configure git to use .githooks as hook directory
```bash
git config core.hooksPath .githooks
```


