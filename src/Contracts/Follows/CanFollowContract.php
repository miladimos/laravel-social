<?php

namespace Miladimos\Social\Contracts;

use Illuminate\Database\Eloquent\Collection;

// CanFollowContract
interface CanFollowContract
{
    /**
     * Returns the entities that this entity is following.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function followings();

    /**
     * Determines if the entity has followings associated.
     *
     * @return bool
     */
    public function hasFollowings();

    /**
     * Determines if the entity is following the given entity.
     *
     * @return bool
     */
    public function isFollowing(FollowableContract $entity);

    /**
     * Follows the given entity.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function follow(FollowableContract $entity);

    /**
     * Follows many entities.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function followMany(Collection $entities);

    /**
     * Unfollows the given entity.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function unfollow(FollowableContract $entity);

    /**
     * Unfollows many entities.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function unfollowMany(Collection $entities);

    /**
     * Returns the given following entity record if this entity is following it.
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function findFollowing(FollowableContract $entity);

    /**
     * Synchronize many entities to be followed by this entity.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function syncManyFollowings(Collection $entities);
}
