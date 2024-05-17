# Laravel CRUD
Easily creates CRUD for laravel

It will create:
- Model
- Collection for the model
- Repository
- Services
- Controllers
- Request
- Policies
- Resources

## Usage
### Basic
```php
$ php artisan make:crud User
```

### Different Plural
```php
$ php artisan make:crud Policy Policies
```

### Brazilian Portuguese
When set locale to pt_BR all action names will be translated. For example, CreateService becomes CriarService

```php
$ php artisan make:crud Usuario --locale=pt_BR
$ php artisan make:crud Acao Acoes --locale=pt_BR
```