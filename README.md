
# Laravel social package

A toolkit package for social networks

- [![Starts](https://img.shields.io/github/stars/miladimos/laravel-social?style=flat&logo=github)](https://github.com/miladimos/laravel-social/forks)
- [![Forks](https://img.shields.io/github/forks/miladimos/laravel-social?style=flat&logo=github)](https://github.com/miladimos/laravel-social/stargazers)

## Installation

1. Run the command below to add this package:

```shell
composer require miladimos/laravel-social
```

2. Open your config/socials.php and add the following to the providers array:

```php
Miladimos\Social\Providers\SocialServiceProvider::class,
```

3. Run the command below to install package:

```shell
php artisan social:install
```

4. Run the command below to migrate database:

```
php artisan migrate
```

## Uses

 Depending on the Capabilities You want implemented, as the required traits to the right Models.

### Likeable Trait

```php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Miladimos\Social\Traits\Like\Likeable;

class Post extends Model
{
    use HasFactory,
        Likeable;
}

```

In the User Model:

```php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Miladimos\Social\Traits\Like\CanLike;

class User extends Authenticatable
{
    use HasFactory,
        CanLike;
}

```

### Commentable Trait

```php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Miladimos\Social\Traits\Comment\Commentable;

class Post extends Model
{
    use HasFactory,
        Commentable;
}

```

In the User Model:

```php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Miladimos\Social\Traits\Comment\Commentor;

class User extends Authenticatable
{
    use HasFactory,
        Commentor;
}

```

### Bookmarkable Trait

```php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Miladimos\Social\Traits\Bookmark\Bookmarkable;

class Post extends Model
{
    use HasFactory,
        Bookmarkable;
}

```

In the User Model:

```php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Miladimos\Social\Traits\Bookmark\CanBookmark;

class User extends Authenticatable
{
    use HasFactory,
        CanBookmark;
}

```

### Followable Trait

In the User Model:

```php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Miladimos\Social\Traits\Follow\Followable;

class User extends Authenticatable
{
    use HasFactory,
        Followable;
}

```

### Methods

In controllers you have these methods:

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

# TODO

- Document Every Available Methods and their Usage.
- Create a test framework to test each unit feature.

## Capabilities [* Proposed, ** In Active Developement]

Like

Favorite *

Tag **

Category **

Bookmark

Follow \ Unfollow

Subscribe **

Comment

Vote / Rate System *