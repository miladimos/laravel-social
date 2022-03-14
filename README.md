- [![Starts](https://img.shields.io/github/stars/miladimos/laravel-social?style=flat&logo=github)](https://github.com/miladimos/laravel-social/forks)
- [![Forks](https://img.shields.io/github/forks/miladimos/laravel-social?style=flat&logo=github)](https://github.com/miladimos/laravel-social/stargazers)


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

## Uses

First add `Attachmentable` trait to models that you want have attachments

```php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravelir\Attachmentable\Traits\Attachmentable;

class Post extends Model
{
    use HasFactory,
        Attachmentable;
}

```

### Methods

in controllers you have these methods:

```php

namespace App\Http\Controllers;

use App\Models\Post;

class PostController extends Controller
{
    public function index()
    {
        $post = Post::find(1);

        $post->attachments // return all attachments

        
    }
}

```

#### امکانات

Like

Favorite

Bookmark

Follow \ Unfollow

Subscribe

Comment

Vote / Rate System
