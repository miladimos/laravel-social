<?php

namespace Miladimos\Social\Traits\Subscription;

use Illuminate\Database\Eloquent\Model;

trait CanSubscribe
{

    public function subscribe(Model $object)
    {
        if (!$this->hasSubscribed($object)) {
            $subscribe = app(config('social.subscribtions.model'));
            $subscribe->{config('social.subscribtions.user_foreign_key')} = $this->getKey();

            $object->subscriptions()->save($subscribe);
        }
    }

    public function unsubscribe(Model $object)
    {
        $relation = $object->subscriptions()
            ->where('subscribable_id', $object->getKey())
            ->where('subscribable_type', $object->getMorphClass())
            ->where(config('social.subscribtions.user_foreign_key'), $this->getKey())
            ->first();

        if ($relation) {
            $relation->delete();
        }
    }

    public function toggleSubscribe(Model $object)
    {
        $this->hasSubscribed($object) ? $this->unsubscribe($object) : $this->subscribe($object);
    }

    public function hasSubscribed(Model $object)
    {
        return tap($this->relationLoaded('subscriptions') ? $this->subscriptions : $this->subscriptions())
            ->where('subscribable_id', $object->getKey())
            ->where('subscribable_type', $object->getMorphClass())
            ->count() > 0;
    }

    public function subscriptions()
    {
        return $this->hasMany(config('social.subscribtions.subscription_model'), config('social.subscribtions.user_foreign_key'), $this->getKeyName());
    }
}
