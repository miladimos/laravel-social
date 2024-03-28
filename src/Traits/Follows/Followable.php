<?php

namespace Miladimos\Social\Traits\Follows;

use Illuminate\Database\Eloquent\Model;

trait Followable
{
    public static function bootFollowable()
    {
        static::deleted(function (Model $entity) {
            $entity->followers()->delete();
        });
    }

    public function followableMorphs(): string
    {
        return config('social.follows.followable_morphs');
    }

    public function followers():MorphMany
    {
        return $this->morphMany(config('social.follows.model'), $this->followableMorphs())->withPivot('accepted_at')->withTimestamps();
    }




    public function needsToApproveFollowRequests(): bool
{
    return false;
}

public function rejectFollowRequestFrom(Model $follower): void
{
    if (! in_array(Follower::class, \class_uses($follower))) {
        throw new \InvalidArgumentException('The model must use the Follower trait.');
    }

    $this->followables()->followedBy($follower)->get()->each->delete();
}

public function acceptFollowRequestFrom(Model $follower): void
{
    if (! in_array(Follower::class, \class_uses($follower))) {
        throw new \InvalidArgumentException('The model must use the Follower trait.');
    }

    $this->followables()->followedBy($follower)->get()->each->update(['accepted_at' => \now()]);
}

public function isFollowedBy(Model $follower): bool
{
    if (! in_array(Follower::class, \class_uses($follower))) {
        throw new \InvalidArgumentException('The model must use the Follower trait.');
    }

    if ($this->relationLoaded('followables')) {
        return $this->followables->whereNotNull('accepted_at')->contains($follower);
    }

    return $this->followables()->accepted()->followedBy($follower)->exists();
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


    public function approvedFollowers(): BelongsToMany
    {
        return $this->followers()->whereNotNull('accepted_at');
    }

    public function notApprovedFollowers(): BelongsToMany
    {
        return $this->followers()->whereNull('accepted_at');
    }
}
