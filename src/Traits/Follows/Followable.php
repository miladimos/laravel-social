<?php

namespace Miladimos\Social\Traits\Follows;

use Illuminate\Database\Eloquent\Model;

trait Followable
{
    public function needsToApproveFollowRequests()
    {
        return config('social.follows.need_follows_to_approved') ?? false;
    }

    public function followers()
    {
        return $this->belongsToMany(
            __CLASS__,
            config('social.follows.relation_table', 'user_follower'),
            'following_id',
            'follower_id'
        )->withPivot('accepted_at')->withTimestamps();
    }

    public function followings()
    {
        return $this->belongsToMany(
            __CLASS__,
            config('social.follows.relation_table', 'user_follower'),
            'follower_id',
            'following_id'
        )->withPivot('accepted_at')->withTimestamps();

        return $this->morphMany(\config('social.subscribtions.model'), 'subscribable');

    }

    public function follow($user)
    {
        $isPending = $user->needsToApproveFollowRequests() ?: false;

        if (!$this->isFollowing($user) && $this->id != $user->id) {
            return $this->followings()->attach($user);
        }

        $this->followings()->attach($user, [
            'accepted_at' => $isPending ? null : now()
        ]);

        return ['pending' => $isPending];
    }

    public function unfollow($user)
    {
        $this->followings()->detach($user);
    }

    public function toggleFollow($user)
    {
        $this->isFollowing($user) ? $this->unfollow($user) : $this->follow($user);
    }

    public function rejectFollowRequestFrom($user)
    {
        $this->followers()->detach($user);
    }

    public function acceptFollowRequestFrom($user)
    {
        $this->followers()->updateExistingPivot($user, ['accepted_at' => now()]);
    }

    public function hasRequestedToFollow($user): bool
    {
        if ($user instanceof Model) {
            $user = $user->getKey();
        }

        if ($this->relationLoaded('followings')) {
            return $this->followings
                ->where('pivot.accepted_at', '===', null)
                ->contains($user);
        }

        return $this->followings()
            ->wherePivot('accepted_at', null)
            ->where($this->getQualifiedKeyName(), $user)
            ->exists();
    }

    public function isFollowing(Model $user): bool
    {
        // return !!$this->followings()->where('followed_id', $user->id)->count();

        if ($this->relationLoaded('followings')) {
            return $this->followings()
                ->where('pivot.accepted_at', '!==', null)
                ->contains($user);
        }

        return $this->followings()
            ->wherePivot('accepted_at', '!=', null)
            ->where($this->getQualifiedKeyName(), $user)
            ->exists();
    }

    public function isFollowedBy(Model $user): bool
    {
        // return !!$this->followers()->where('follower_id', $user->id)->count();

        if ($this->relationLoaded('followers')) {
            return $this->followers
                ->where('pivot.accepted_at', '!==', null)
                ->contains($user);
        }

        return $this->followers()
            ->wherePivot('accepted_at', '!=', null)
            ->where($this->getQualifiedKeyName(), $user)
            ->exists();
    }

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

    public function areFollowingEachOther($user): bool
    {
        return $this->isFollowing($user) && $this->isFollowedBy($user);
    }

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
