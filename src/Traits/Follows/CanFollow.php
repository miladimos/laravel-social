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

    public function findFollower(CanFollowContract $entity)
    {
        return $this->followers()->whereFollowingableEntity($entity)->first();
    }

    // FollowableContract $entity
    public function isFollowing(Model $model): bool
    {
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

    // FollowableContract $entity
    public function unfollow($user)
    {
        $this->followings()->detach($user);

        $relation = $object->subscriptions()
            ->where('subscribable_id', $object->getKey())
            ->where('subscribable_type', $object->getMorphClass())
            ->where(config('social.subscribtions.user_foreign_key'), $this->getKey())
            ->first();

        if ($relation) {
            $relation->delete();
        }
    }

  public function toggleFollow($user)
  {
      $this->isFollowing($user) ? $this->unfollow($user) : $this->follow($user);
  }

  public function areFollowingEachOther($user): bool
  {
      return $this->isFollowing($user) && $this->isFollowedBy($user);
  }

  public function followMany(Collection $entities)
  {
      $entities->each(function (FollowableContract $entity) {
          $this->follow($entity);
      });

      return $this->fresh();
  }

  public function unfollowMany(Collection $entities)
  {
      $entities->each(function (FollowableContract $entity) {
          $this->unfollow($entity);
      });

      return $this->fresh();
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
}
