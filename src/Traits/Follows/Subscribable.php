<?php

namespace Miladimos\Social\Traits\Subscription;

use Illuminate\Database\Eloquent\Model;

trait Subscribable
{
    public function isSubscribedBy(Model $user)
    {
        if (\is_a($user, \config('auth.providers.users.model'))) {
            if ($this->relationLoaded('subscribers')) {
                return $this->subscribers->contains($user);
            }

            return tap($this->relationLoaded('subscriptions') ? $this->subscriptions : $this->subscriptions())
                ->where(\config('social.subscribtions.user_foreign_key'), $user->getKey())->count() > 0;
        }

        return false;
    }

    public function subscriptions()
    {
        return $this->morphMany(\config('social.subscribtions.model'), 'subscribable');
    }

    public function subscribers()
    {
        return $this->belongsToMany(
            config('auth.providers.users.model'),
            config('social.subscribtions.table'),
            'subscribable_id',
            config('social.subscribtions.user_foreign_key')
        )
            ->where('subscribable_type', $this->getMorphClass());
    }
}
