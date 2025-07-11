[![Starts](https://img.shields.io/github/stars/miladimos/laravel-social?style=flat&logo=github)](https://github.com/miladimos/laravel-social/forks)
[![Forks](https://img.shields.io/github/forks/miladimos/laravel-social?style=flat&logo=github)](https://github.com/miladimos/laravel-social/stargazers)


# Laravel social package

A toolkit package for social networks

## Installation

1. Run the command below to add this package:

```
composer require miladimos/laravel-social
```

2. Open your config/socials.php and add the following to the providers array:

```php
Miladimos\Social\Providers\SocialServiceProvider::class,
```

3. Run the command below to install package:

```
php artisan social:install
```

4. Run the command below to migrate database:

```
php artisan migrate
```

# Features

## Follow/UnFollow

First add `Followable` trait to user model

```php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Miladimos\Social\Traits\Follows\Followable;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory,
        Followable;
}

```

and enable to you follow/unfollow feature:

```php
namespace App\Http\Controller;

use App\Models\User;

class YourController extends Controller
{
    public function index()
    {   
        $firstUser = User::first();
        $secondUser = User::find(2);

        $firstUser->follow($secondUser);
        $firstUser->unfollow($secondUser);
        $firstUser->toggleFollow($secondUser);

        $firstUser->followers;
        $firstUser->followings;
    }
}

```

## Like

