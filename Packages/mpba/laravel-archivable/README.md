A simple package for making Laravel Eloquent models 'archivable'. This package allows for the easy archiving of models by creating various macros to be used within method chaining.

## Installation

You can install the package via composer:

```bash
composer require mpba/laravel-archivable
```

## Usage

#### Migrations

The `Archivable` trait works similarly to Laravel's `SoftDeletes` trait. This package also ships with a helpful macro for Laravel's `\Illuminate\Database\Schema\Blueprint`. To get started, simply add the `archivedAt` macro to your migration, like so:

```php
Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('user_id');
    $table->string('title');
    $table->timestamps();
    $table->archivedAt(); // Macro
});
```

#### Eloquent
You can now, safely, include the `Archivable` trait in your Eloquent model:

``` php
namespace App\Models;

use \Illuminate\Database\Eloquent\Model;
use \LaravelArchivable\Archivable;

class Post extends Model {

    use Archivable;
    ...
}
```

#### Extensions

The extensions shipped with this trait include; `archive`, `unArchive`, `withArchived`, `withoutArchived`, `onlyArchived` and can be used accordingly:

```php
$user = User::first();
$user->archive();
$user->unArchive();

$usersWithArchived = User::query()->withArchived();
$onlyArchivedUsers = User::query()->onlyArchived();
```

By default, the global scope of this trait uses the `withoutArchived` extension when the trait is added to a model.



### Security

If you discover any security related issues, please email joel@joelbutcher.co.uk instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
