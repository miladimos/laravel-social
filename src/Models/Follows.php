<?php

namespace Miladimos\Social\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Miladimos\Social\Traits\HasUUID;

class Follows extends Model
{
    use HasUUID;

    protected $table = 'follows';

    protected $guarded = [];

    public $timestamps = false;

    public function followable()
    {
        return $this->morphTo();
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = config('social.subscriptions.table');
    }

    public function subscribable()
    {
        return $this->morphTo();
    }

    public function subscriber()
    {
        return $this->user();
    }

    public function user()
    {
        return $this->belongsTo(config('auth.providers.users.model'), config('social.subscriptions.user_foreign_key'));
    }

    public function scopeWithType(Builder $query, string $type)
    {
        return $query->where('subscribable_type', app($type)->getMorphClass());
    }
}
