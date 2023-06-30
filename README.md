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

## Tag:

First add `Taggable` trait to models that you want have tags

```php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Miladimos\Social\Traits\Taggable;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory,
        Taggable;
}

```

Second you can work with tags:

```php
namespace App\Http\Controller;

use App\Models\Post;
use Miladimos\Social\Models\Tag;

class YourController extends Controller
{
    public function index()
    {   
        // first you can create custom tags
        $tag = Tag::create(['name' => 'tag']);   
        
        $post = Post::first();
        
        $post->tags; // return attached tags

        $post->attach($tag); // attach one tag

        $post->detach($tag); // detach one tag

        $post->syncTags($tags); // sync tags

        $tag->taggables; // return morph relation to tagged model
    }
}

```
tag model have soft deletes trait.


## Like

## Bookmark

## Follow

## Category

First add `Taggable` trait to models that you want have attachments

```php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Miladimos\Social\Traits\Taggable;

class Post extends Model
{
    use HasFactory,
        Taggable;
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

        $post->likes // return all likes

        
    }
}

```

####  Features

Like

Favorite

Bookmark

Follow \ Unfollow

Comment

Vote / Rate System
