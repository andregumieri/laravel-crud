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

### locale (Brazilian Portuguese)
When set locale to pt_BR all action names will be translated. For example, CreateService becomes CriarService

```php
$ php artisan make:crud Usuario --locale=pt_BR
$ php artisan make:crud Acao Acoes --locale=pt_BR
```

This can also be set globally on config/crud.php locale key (check publishing config file)

### Repository Base Class
Whenever the package andregumieri/laravel-repository is present and the config.repository_base_class is set to true, it will automatically use it as a base repository.

In other cases your custom base repository can be set informing the full path of the base class:
```bash
$ php artisan make:crud User --repository-base-class="App\Repositories\Base" 
```

It can also be set via config/crud.php on repository_base_class (check publishing config file)

## Publishing Config file
```bash
$ php artisan vendor:publish --tag=laravel-crud
```

this will create a config/crud.php file