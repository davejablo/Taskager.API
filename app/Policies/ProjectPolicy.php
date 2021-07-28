<?php

namespace App\Policies;

use App\Project;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any projects.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->hasRole('ADMIN');
    }

    /**
     * Determine whether the user can view the project.
     *
     * @param  \App\User  $user
     * @param  \App\Project  $project
     * @return mixed
     */
    public function view(User $user, Project $project)
    {
        switch ($user){
            case $user->hasRole('ADMIN'):
                return true;
                break;

            case $user->hasRole(['LEADER']):
                return $user->project_id == $project->id;
                break;

            case $user->hasRole('WORKER'):
                return $user->project_id == $project->id;
                break;

            case $user->hasRole('CLIENT'):
                return $user->project_id == $project->id;
                break;

            default:
                return false;
        }
    }

    /**
     * Determine whether the user can create projects.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasRole('ADMIN');
    }

//    /**
//     * Determine whether the user can update the project.
//     *
//     * @param  \App\User  $user
//     * @param  \App\Project  $project
//     * @return mixed
//     */
    /**
     * @param User $user
     * @return bool
     */
    public function update(User $user, Project $project)
    {
        switch ($user){
            case $user->hasRole('ADMIN'):
                return true;
                break;

            case $user->hasRole(['LEADER']):
                return $user->project_id == $project->id;
                break;

            case $user->hasRole('CLIENT'):
                return $user->project_id == $project->id;
                break;

            default:
                return false;
        }
    }

//    /**
//     * Determine whether the user can delete the project.
//     *
//     * @param  \App\User  $user
//     * @param  \App\Project  $project
//     * @return mixed
//     */
    /**
     * @param User $userp
     * @return bool
     */
    public function delete(User $user)
    {
        return $user->hasRole('ADMIN');
    }

    /**
     * Determine whether the user can restore the project.
     *
     * @param  \App\User  $user
     * @param  \App\Project  $project
     * @return mixed
     */
    public function restore(User $user, Project $project)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the project.
     *
     * @param  \App\User  $user
     * @param  \App\Project  $project
     * @return mixed
     */
    public function forceDelete(User $user, Project $project)
    {
        //
    }
}
