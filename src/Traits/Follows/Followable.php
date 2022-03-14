<?php

namespace Miladimos\Social\Traits\Follows;

use Illuminate\Database\Eloquent\Model;

trait Followable
{

    public function needsToApproveFollowRequests()
    {
        return config('social.follows.need_follows_to_approved') ?? false;
    }

    /**
     * Follow the given user.
     *
     * @param User $user
     * @return mixed
     */
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

    /**
     * Unfollow the given user.
     *
     * @param User $user
     * @return mixed
     */
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

    /**
     * Check if a given user is following this user.
     *
     * @param Model $user
     * @return bool
     */
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

    /**
     * Check if a given user is being followed by this user.
     *
     * @param User $user
     * @return bool
     */
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

    public function areFollowingEachOther($user)
    {
        return $this->isFollowing($user) && $this->isFollowedBy($user);
    }

    /**
     * Get all the users that are following this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
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
    }

    /**
     * Check if the authenticated user is following this user.
     *
     * @return bool
     */
    public function getFollowingAttribute()
    {
        if (!auth()->check()) {
            return false;
        }

        if (!$this->relationLoaded('followers')) {
            $this->load(['followers' => function ($query) {
                $query->where('follower_id', auth()->id());
            }]);
        }

        $followers = $this->getRelation('followers');

        if (!empty($followers) && $followers->contains('id', auth()->id())) {
            return true;
        }

        return false;
    }
}
