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

    public function followings(): MorphMany
    {
        return $this->morphMany(config('social.follows.model'), $this->followingableMorphs());
    }

    public function hasAnyFollowings(): bool
    {
        return (bool) $this->followings()->count();
    }

    public function hasAnyFollowers(): bool
    {
        return (bool) $this->followers()->count();
    }

    // FollowableContract
    public function follow($entity)
    {
      // $following = $this->findFollowing($entity);

        $isPending = $entity->needsToApproveFollowRequests() ?: false;

        if (!$this->isFollowing($entity) && $this->id != $entity->id) {
            return $this->followings()->attach($entity);
        }

        $this->followings()->attach($user, [
            'accepted_at' => $isPending ? null : now()
        ]);

        return ['pending' => $isPending];
    }



    public function findFollowing(FollowableContract $entity)
    {
        return $this->followings()->whereFollowableEntity($entity)->first();
    }

    public function findFollower(FollowableContract $entity)
    {
        return $this->followers()->whereFollowingableEntity($entity)->first();
    }

    // FollowableContract $entity
    public function isFollowing(Model $model): bool
    {
        // return !!$this->followings()->where('followed_id', $model->id)->count();
        if ($this->relationLoaded('followings')) {
            return $this->followings()
                ->wherePivot('accepted_at', '!=', null)
                ->contains($model);
        }

        // return tap($this->relationLoaded('subscriptions') ? $this->subscriptions : $this->subscriptions())
        //         ->where('subscribable_id', $object->getKey())
        //         ->where('subscribable_type', $object->getMorphClass())
        //         ->count() > 0;
    }

    public function isFollowedBy(Model $user): bool
    {
        if ($this->relationLoaded('followers')) {
            return $this->followers()
                ->wherePivot('accepted_at', '!=', null)
                ->contains($user);
        }
    }






























    public function followMany(Collection $entities)
    {
        $entities->each(function (FollowableContract $entity) {
            $this->follow($entity);
        });

        return $this->fresh();
    }

    /**
     * Unfollows the given entity.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function unfollow(FollowableContract $entity)
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
        $entities->each(function (FollowableContract $entity) {
            $this->unfollow($entity);
        });

        return $this->fresh();
    }

    /**
     * Synchronize many entities to be followed by this entity.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function syncManyFollowings(Collection $entities)
    {
        $this->followings()->delete();

        $entities->each(function (FollowableContract $entity) {
            $this->follow($entity);
        });

        return $this->fresh();
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



        public function areFollowingEachOther($user): bool
        {
            return $this->isFollowing($user) && $this->isFollowedBy($user);
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
