<?php


namespace App\Http\Repositories;
use App\Http\Resources\UserResource;
use App\Project;
use App\Task;
use App\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    public function getAuthenticatedUser(){
        return $user = auth()->user();
    }

    public function createAndReturnUser($request){
        $newUser = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'user_id' => $request->get('user_id')
        ]);

        if ($newUser->save()){
            return $newUser;
        }
    }

    public  function getUsers(){
        $authUser = $this->getAuthenticatedUser();
        return $authUser->hasRole('LEADER')
            ? $users = User::where('project_id', $authUser->project_id)->with('profile', 'roles', 'project')->paginate(5)
            : $users = User::with('profile', 'roles', 'project')->paginate(5);
    }

    public function getUser($user){
        return $userToReturn = User::findOrFail($user->id);
    }

    public function getUserProfile(User $user){
        return $userProfile = $user->profile()->firstOrFail();
    }

    public function getAuthenticatedProject(){
        return $userProject = $this->getAuthenticatedUser()->project()->firstOrFail();
    }

    public function getUserProject(User $user){
        return $userProject = $user->project()->firstOrFail();
    }

    public function getUserTasks(User $user){
        return $userTasks = $user->tasks()->paginate(5);
    }

    public function getSingleUserTask(User $user, Task $task){
        return $singleUserTask = $user->tasks()->where('id', $task->id)->firstOrFail();
    }

    public function getAuthenticatedProfile(){
        return $userProfile = $this->getAuthenticatedUser()->profile()->firstOrFail();
    }

    public function getAuthenticatedTasks(){
        $authUser = $this->getAuthenticatedUser();
        $project = Project::findOrFail($authUser->project_id);
        return $authUser->hasRole('LEADER')
            ? $tasks = $project->tasks
            : $userTasks = $this->getAuthenticatedUser()->tasks()->paginate(5);
    }

    public function getSingleAuthenticatedTask(Task $task){
        $user = $this->getAuthenticatedUser();
        return $userSingleTask = $user->tasks()->where('id', $task->id)->firstOrFail();
    }

    public function updateAndReturnUser($request, $id){
        $userFromDb = User::findOrFail($id);
        $userFromDb->update($request->validated());
        $updatedUserFromDb = User::findOrFail($id);

        return new UserResource($updatedUserFromDb);
    }

}