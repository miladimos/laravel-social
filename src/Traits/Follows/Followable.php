<?php

namespace Miladimos\Social\Traits\Follows;

use Illuminate\Database\Eloquent\Model;

trait Followable
{

    public function needsToApproveFollowRequests()
    {
        return false;
    }

    public function follow($user)
    {
        $isPending = $user->needsToApproveFollowRequests() ?: false;

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

    public function isFollowing($user)
    {
        if ($user instanceof Model) {
            $user = $user->getKey();
        }

        if ($this->relationLoaded('followings')) {
            return $this->followings
                ->where('pivot.accepted_at', '!==', null)
                ->contains($user);
        }

        return $this->followings()
            ->wherePivot('accepted_at', '!=', null)
            ->where($this->getQualifiedKeyName(), $user)
            ->exists();
    }

    public function isFollowedBy($user)
    {
        if ($user instanceof Model) {
            $user = $user->getKey();
        }

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

    public function areFollowingEachOther($user)
    {
        return $this->isFollowing($user) && $this->isFollowedBy($user);
    }

    public function followers()
    {
        return $this->belongsToMany(
            __CLASS__,
            \config('social.follows.relation_table', 'user_follower'),
            'following_id',
            'follower_id'
        )->withPivot('accepted_at')->withTimestamps()->using(UserFollower::class);
    }

    public function followings()
    {
        return $this->belongsToMany(
            __CLASS__,
            \config('social.follows.relation_table', 'user_follower'),
            'follower_id',
            'following_id'
        )->withPivot('accepted_at')->withTimestamps()->using(UserFollower::class);
    }

     /**
     * Check if the authenticated user is following this user.
     *
     * @return bool
     */
    public function getFollowingAttribute()
    {
        if (! auth()->check()) {
            return false;
        }

        if (! $this->relationLoaded('followers')) {
            $this->load(['followers' => function ($query) {
                $query->where('follower_id', auth()->id());
            }]);
        }

        $followers = $this->getRelation('followers');

        if (! empty($followers) && $followers->contains('id', auth()->id())) {
            return true;
        }

        return false;
    }

    /**
     * Follow the given user.
     *
     * @param User $user
     * @return mixed
     */
    public function follow(User $user)
    {
        if (! $this->isFollowing($user) && $this->id != $user->id)
        {
            return $this->following()->attach($user);
        }
    }

    /**
     * Unfollow the given user.
     *
     * @param User $user
     * @return mixed
     */
    public function unFollow(User $user)
    {
        return $this->following()->detach($user);
    }

    /**
     * Get all the users that this user is following.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function following()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'followed_id')->withTimestamps();
    }

    /**
     * Get all the users that are following this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'followed_id', 'follower_id')->withTimestamps();
    }

    /**
     * Check if a given user is following this user.
     *
     * @param User $user
     * @return bool
     */
    public function isFollowing(User $user)
    {
        return !! $this->following()->where('followed_id', $user->id)->count();
    }

    /**
     * Check if a given user is being followed by this user.
     *
     * @param User $user
     * @return bool
     */
    public function isFollowedBy(User $user)
    {
        return !! $this->followers()->where('follower_id', $user->id)->count();
    }
}
