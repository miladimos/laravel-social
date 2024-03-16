<?php

namespace Miladimos\Social\Traits\Follows;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

trait CanFollow
{
    /**
     * Boots the trait.
     *
     * @return void
     */
    public static function bootCanFollow(): void
    {
        static::deleted(function (Model $entityModel) {
            $entityModel->followings()->delete();
        });
    }

    public function needsToApproveFollowRequests(): bool
    {
        return config('social.follows.need_follows_to_approved');
    }


    public function followsModel(): string
    {
        return config('social.follows.model');
    }

    public function followingableMorphs(): string
    {
        return config('social.follows.followingable_morphs');
    }
    /**
     * Returns the entities that this entity is following.
     */
    public function followings(): MorphMany
    {
        return $this->morphMany(config('social.follows.model'), $this->followingableMorphs());
    }






    /**
     * Determines if the entity has followings associated.
     */
    public function hasFollowings(): bool
    {
        return (bool) $this->followings()->withoutTrashed()->count();
    }

    /**
     * Determines if the entity is following the given entity.
     *
     * @return bool
     */
    public function isFollowing(CanBeFollowedContract $entity)
    {
        $following = $this->findFollowing($entity);

        return $following && ! $following->trashed();
    }

    /**
     * Follows the given entity.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function follow(CanBeFollowedContract $entity)
    {
        $following = $this->findFollowing($entity);

        // If the entity previously followed the entity but then unfollowed it,
        // we still have the relationship, it just needs to be restored.
        if ($following && $following->trashed()) {
            $following->restore();
        } elseif (! $following) {
            $follower = new Follower();
            $follower->followable_id = $entity->getKey();
            $follower->followable_type = $entity->getMorphClass();

            $this->followings()->save($follower);
        }

        return $this->fresh();
    }

    /**
     * Follows many entities.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function followMany(Collection $entities)
    {
        $entities->each(function (CanBeFollowedContract $entity) {
            $this->follow($entity);
        });

        return $this->fresh();
    }

    /**
     * Unfollows the given entity.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function unfollow(CanBeFollowedContract $entity)
    {
        $following = $this->findFollowing($entity);

        if ($following && ! $following->trashed()) {
            $this->followings()->whereFollowableEntity($entity)->delete();
        }

        return $this->fresh();
    }

    /**
     * Unfollows many entities.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function unfollowMany(Collection $entities)
    {
        $entities->each(function (CanBeFollowedContract $entity) {
            $this->unfollow($entity);
        });

        return $this->fresh();
    }

    /**
     * Returns the given following entity record if this entity is following it.
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function findFollowing(CanBeFollowedContract $entity)
    {
        return $this->followings()->withTrashed()->whereFollowableEntity($entity)->first();
    }

    /**
     * Synchronize many entities to be followed by this entity.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function syncManyFollowings(Collection $entities)
    {
        $this->followings()->delete();

        $entities->each(function (CanBeFollowedContract $entity) {
            $this->follow($entity);
        });

        return $this->fresh();
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

        /**
         * Determines if the entity has followers associated.
         *
         * @return bool
         */
        public function hasFollowers()
        {
            return (bool) $this->followers()->withoutTrashed()->count();
        }

        /**
         * Determines if the given entity is a follower of this entity.
         *
         * @return bool
         */
        public function hasFollower(CanFollowContract $entity)
        {
            $follower = $this->findFollower($entity);

            return (bool) $follower && ! $follower->trashed();
        }

        /**
         * Adds the given entity as a follower of this entity.
         *
         * @return \Illuminate\Database\Eloquent\Model
         */
        public function addFollower(CanFollowContract $entity)
        {
            $followed = $this->followers()->withTrashed()->whereFollowerEntity($entity)->first();

            // If the entity was previously a follower of this entity
            // but later decided to unfollow it, we still have that
            // entry, it just needs to be restored.
            if ($followed && $followed->trashed()) {
                $followed->restore();
            } elseif (! $followed) {
                $follower = new Follower();
                $follower->follower_id = $entity->getKey();
                $follower->follower_type = $entity->getMorphClass();

                $this->followers()->save($follower);
            }

            return $this->fresh();
        }

        /**
         * Adds many entities as followers of this entity.
         *
         * @return \Illuminate\Database\Eloquent\Model
         */
        public function addManyFollowers(Collection $entities)
        {
            $entities->each(function (CanFollowContract $entity) {
                $this->addFollower($entity);
            });

            return $this->fresh();
        }

        /**
         * Removes the given entity from being a follower of this entity.
         *
         * @return \Illuminate\Database\Eloquent\Model
         */
        public function removeFollower(CanFollowContract $entity)
        {
            $followed = $this->findFollower($entity);

            if ($followed && ! $followed->trashed()) {
                $followed->delete();
            }

            return $this->fresh();
        }

        /**
         * Removes many entities from being followers of this entity.
         *
         * @return \Illuminate\Database\Eloquent\Model
         */
        public function removeManyFollowers(Collection $entities)
        {
            $entities->each(function (CanFollowContract $entity) {
                $this->removeFollower($entity);
            });

            return $this->fresh();
        }

        /**
         * Finds the gained followers (created) over the given time period.
         *
         * @return int
         */
        public function scopeGainedFollowers(Builder $query, DateTime $startDate, DateTime $endDate)
        {
            return $this
                ->followers()
                ->withoutTrashed()
                ->whereBetween('created_at', [$startDate, $endDate])
            ;
        }

        /**
         * Finds the lost followers (deleted) over the given time period.
         *
         * @return int
         */
        public function scopeLostFollowers(Builder $query, DateTime $startDate, DateTime $endDate)
        {
            return $this
                ->followers()
                ->onlyTrashed()
                ->whereBetween('deleted_at', [$startDate, $endDate])
            ;
        }

        /**
         * Returns the given entity record if this entity is being followed by it.
         *
         * @return \Illuminate\Database\Eloquent\Model|null
         */
        public function findFollower(CanFollowContract $entity)
        {
            return $this->followers()->withTrashed()->whereFollowerEntity($entity)->first();
        }

        /**
         * Synchronize many entities that follows this entity.
         *
         * @return \Illuminate\Database\Eloquent\Model
         */
        public function syncManyFollowers(Collection $entities)
        {
            $this->followers()->delete();

            $entities->each(function (CanFollowContract $entity) {
                $this->addFollower($entity);
            });

            return $this->fresh();
        }
}
