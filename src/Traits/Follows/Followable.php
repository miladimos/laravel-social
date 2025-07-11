<?php

namespace Miladimos\Social\Traits\Follows;

use DateTime;
use function in_array;
use function class_uses;
use InvalidArgumentException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Followable
{
    public static function bootFollowable()
    {
        static::deleted(function (Model $entity) {
            $entity->followers()->delete();
            $entity->followings()->delete();
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

    public function followers(): MorphMany
    {
        return $this->morphMany($this->followsModel(), 'followable');
    }

    public function followings(): MorphMany
    {
        return $this->morphMany($this->followsModel(), 'followingable');
    }

    public function follow(Model $followable): array
    {
        if ($followable->is($this)) {
            throw new InvalidArgumentException('Cannot follow yourself.');
        }

        if (! in_array(Followable::class, class_uses($followable))) {
            throw new InvalidArgumentException('The followable model must use the Followable trait.');
        }

        $isPending = $followable->needsToApproveFollowRequests() ?: false;

        $this->followings()->updateOrCreate([
            'followable_id' => $followable->getKey(),
            'followable_type' => $followable->getMorphClass(),
        ], [
            'accepted_at' => $isPending ? null : now(),
        ]);

        return ['pending' => $isPending];

        // if (!$this->isFollowing($entity) && $this->id != $entity->id) {
        //     return $this->followings()->attach($entity);
        // }

        // $this->followings()->attach($user, [
        //     'accepted_at' => $isPending ? null : now()
        // ]);
    }

    public function unfollow(Model $followable)
    {
        if ($followable->is($this)) {
            throw new InvalidArgumentException('Cannot unfollow yourself.');
        }

        if (! in_array(Followable::class, class_uses($followable))) {
            throw new InvalidArgumentException('The followable model must use the Followable trait.');
        }

        // $this->followings()->of($followable)->get()->each->delete();

        return $this->followings()->detach($followable);
    }

    public function toggleFollow(Model $followable): void
    {
        $this->isFollowing($followable) ? $this->unfollow($followable) : $this->follow($followable);
    }

    public function rejectFollowRequestFrom(Model $follower): void
    {
        if (! in_array(Followable::class, \class_uses($follower))) {
            throw new \InvalidArgumentException('The model must use the Follower trait.');
        }

        // $this->followers()->detach($user);

        $this->followables()->followedBy($follower)->get()->each->delete();
    }

    public function acceptFollowRequestFrom(Model $follower): void
    {
        if (! in_array(Followable::class, \class_uses($follower))) {
            throw new \InvalidArgumentException('The model must use the Follower trait.');
        }
        // $this->followers()->updateExistingPivot($user, ['accepted_at' => now()]);

        $this->followables()->followedBy($follower)->get()->each->update(['accepted_at' => \now()]);
    }

    public function isFollowedBy(Model $follower): bool
    {
        if (! in_array(Followable::class, \class_uses($follower))) {
            throw new \InvalidArgumentException('The model must use the Follower trait.');
        }

        if ($this->relationLoaded('followers')) {
            return $this->followers->whereNotNull('accepted_at')->contains($follower);
        }

        return $this->followers()->accepted()->followedBy($follower)->exists();

        // if ($this->relationLoaded('followers')) {
        //     return $this->followers()
        //         ->wherePivot('accepted_at', '!=', null)
        //         ->contains($user);
        // }
    }

    public function isFollowing(Model $followable): bool
    {
        if (! in_array(Followable::class, class_uses($followable))) {
            throw new InvalidArgumentException('The followable model must use the Followable trait.');
        }

        if ($this->relationLoaded('followings')) {
            return $this->followings
                ->whereNotNull('accepted_at')
                ->where('followable_id', $followable->getKey())
                ->where('followable_type', $followable->getMorphClass())
                ->isNotEmpty();
        }

        return $this->followings()->of($followable)->accepted()->exists();


        // if ($this->relationLoaded('followings')) {
        //     return $this->followings()
        //         ->wherePivot('accepted_at', '!=', null)
        //         ->contains($model);
        // }

        // // return tap($this->relationLoaded('subscriptions') ? $this->subscriptions : $this->subscriptions())
        // //         ->where('subscribable_id', $object->getKey())
        // //         ->where('subscribable_type', $object->getMorphClass())
        // //         ->count() > 0;
    }







    public function approvedFollowers()
    {
        return $this->followers()->whereNotNull('accepted_at');
    }

    public function notApprovedFollowers()
    {
        return $this->followers()->whereNull('accepted_at');
    }

    public function hasRequestedToFollow(Model $followable): bool
    {
        if (! in_array(Followable::class, \class_uses($followable))) {
            throw new InvalidArgumentException('The followable model must use the Followable trait.');
        }

        if ($this->relationLoaded('followings')) {
            return $this->followings->whereNull('accepted_at')
                ->where('followable_id', $followable->getKey())
                ->where('followable_type', $followable->getMorphClass())
                ->isNotEmpty();
        }

        return $this->followings()->of($followable)->notAccepted()->exists();





        // if ($user instanceof Model) {
        //     $user = $user->getKey();
        // }

        // if ($this->relationLoaded('followings')) {
        //     return $this->followings
        //         ->where('pivot.accepted_at', '===', null)
        //         ->contains($user);
        // }

        // return $this->followings()
        //     ->wherePivot('accepted_at', null)
        //     ->where($this->getQualifiedKeyName(), $user)
        //     ->exists();
    }

    public function approvedFollowings(): HasMany
    {
        return $this->followings()->accepted();
    }

    public function notApprovedFollowings(): HasMany
    {
        return $this->followings()->notAccepted();
    }

    public function hasAnyFollowings(): bool
    {
        return (bool) $this->followings()->count();
    }

    public function hasAnyFollowers(): bool
    {
        return (bool) $this->followers()->count();
    }

    public function areFollowingEachOther($user): bool
    {
        return $this->isFollowing($user) && $this->isFollowedBy($user);
    }

    public function followMany(Collection $entities)
    {
        $entities->each(function (Model $entity) {
            $this->follow($entity);
        });

        return $this->fresh();
    }

    public function unfollowMany(Collection $entities)
    {
        $entities->each(function (Model $entity) {
            $this->unfollow($entity);
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

    public function scopeOrderByFollowersCount($query, string $direction = 'desc')
    {
        return $query->withCount('followers')->orderBy('followers_count', $direction);
    }

    public function scopeOrderByFollowersCountDesc($query)
    {
        return $this->scopeOrderByFollowersCount($query, 'desc');
    }

    public function scopeOrderByFollowersCountAsc($query)
    {
        return $this->scopeOrderByFollowersCount($query, 'asc');
    }
}
