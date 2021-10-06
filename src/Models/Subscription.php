<?php

namespace Miladimos\Social\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Miladimos\Social\Traits\HasUUID;

class Subscription extends Model
{
    use HasUUID;

    protected $table = 'subscriptions';

    protected $guarded = [];

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
