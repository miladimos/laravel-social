<?php

namespace Miladimos\Social\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Subscription extends Model
{
    protected $table = config('social.subscriptions.table', 'subscriptions');

    protected $guarded = [];

    // protected $dispatchesEvents = [
    //     'created' => Subscribed::class,
    //     'deleted' => Unsubscribed::class,
    // ];

    public function __construct(array $attributes = [])
    {
        $this->table = config('social.subscribtions.table');

        parent::__construct($attributes);
    }

    protected static function boot()
    {
        parent::boot();

        self::saving(function (Subscription $subscription) {
            $userForeignKey = \config('social.subscribtions.user_foreign_key');
            $subscription->{$userForeignKey} = $subscription->{$userForeignKey} ?: auth()->id();

            if (\config('social.subscribtions.uuids')) {
                $subscription->{$subscription->getKeyName()} = $subscription->{$subscription->getKeyName()} ?: (string) Str::orderedUuid();
            }
        });
    }

    public function subscribable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(config('auth.providers.users.model'), config('social.subscribtions.user_foreign_key'));
    }

    public function subscriber()
    {
        return $this->user();
    }

    public function scopeWithType(Builder $query, string $type)
    {
        return $query->where('subscribable_type', app($type)->getMorphClass());
    }
}
