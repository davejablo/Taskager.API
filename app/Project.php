<?php

namespace App;

use App\Http\Resources\UserResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use test\Mockery\ReturnTypeObjectTypeHint;
use Illuminate\Database\Eloquent\Builder;

class Project extends Model
{
    protected $fillable = ['name', 'budget'];

    public function users(){
        return $this->hasMany(User::class);
    }

    public function tasks(){
        return $this->hasMany(Task::class);
    }

    public function documents(){
        return $this->hasMany(Document::class);
    }

    public function hasAnyUsers(){
        return $this->users()->first() ? true : false;
    }

    public function hasAnyTasks(){
        return $this->tasks()->first() ? true : false;
    }

    public function hasAnyDocuments(){
        return $this->documents()->first() ? true : false;
    }

    public function hasAnyRelations(){
        return ($this->hasAnyTasks() || $this->hasAnyUsers() || $this->hasAnyDocuments()) ? true : false;
    }

    public function doneTasksBetweenDate($request)
    {
            return $this->tasks()
                ->whereBetween('done_at', [$request->date_start, $request->date_end])
                ->where('status', 'done')
                ->where('is_done', true)
                ->get();
    }

    public function getTasksCost($request){
        if ($request->get('date_start') && $request->get('date_end')){
            $cost = $this->tasks()
                ->whereBetween('done_at', [$request->date_start, $request->date_end])
                ->where('status', 'done')
                ->where('is_done', true)
                ->pluck('task_cost')
                ->sum();
        }
        else
        $cost = $this->tasks()
            ->where('status', 'done')
            ->where('is_done', true)
            ->pluck('task_cost')
            ->sum();
        return number_format($cost, 2);
    }

    public function getAmountOfAllTasks($request){
        if ($request->get('date_start') && $request->get('date_end')){
            return $this->tasks()
                ->whereBetween('created_at', [$request->date_start, $request->date_end])
                ->count();
        }
        else
        return $this->tasks()->count();
    }

    public function getAmountOfAssignedUsers($request){
        if ($request->get('date_start') && $request->get('date_end')){
            return $this->tasks()
                ->whereBetween('created_at', [$request->date_start, $request->date_end])
                ->distinct('user_id')
                ->count();
        }
        else
        return $this->users()->count();
    }

    public function getAmountOfDoneTasks($request){
        if ($request->get('date_start') && $request->get('date_end')){
            return $this->tasks()
                ->whereBetween('done_at', [$request->date_start, $request->date_end])
                ->where('status', 'done')
                ->where('is_done', true)
                ->count();
        }
        else
        return $this->tasks()
            ->where('status', 'done')
            ->where('is_done', true)
            ->count();
    }

    public function getAmountOfInProgressTasks($request){
        if ($request->get('date_start') && $request->get('date_end')){
            return $this->tasks()
                ->whereBetween('created_at', [$request->date_start, $request->date_end])
                ->where('status', 'in_progress')
                ->where('is_done', false)
                ->count();
        }
        else
        return $this->tasks()
            ->where('status', 'in_progress')
            ->where('is_done', false)
            ->count();
    }

    public function getAmountOfNaTasks($request){
        if ($request->get('date_start') && $request->get('date_end')){
            return $this->tasks()
                ->whereBetween('created_at', [$request->date_start, $request->date_end])
                ->where('status', 'not_assigned')
                ->where('is_done', false)
                ->where('user_id', null)
                ->count();
        }
        return $this->tasks()
            ->where('status', 'not_assigned')
            ->where('is_done', false)
            ->where('user_id', null)
            ->count();
    }

    public function getClient(){
        $user = $this->users()
            ->select('id', 'name', 'email')
            ->join('role_user', function ($join) {
                $join->on('users.id', '=', 'role_user.user_id')
                    ->where('role_user.role_id', '=', 4);
            })
            ->first();
        return $user;
    }
}
