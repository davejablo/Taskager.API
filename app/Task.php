<?php

namespace App;

use App\Mail\NewTaskNotificationMail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class Task extends Model
{
    protected $fillable = [
        'project_id',
        'user_id',
        'name',
        'description',
        'expire_date',
        'hours_spent',
        'task_cost',
        'status',
        'is_done',
        'priority',
    ];

    const TASK_STATUS = ['not_assigned', 'in_progress', 'done'];
    const TASK_PRIORITY = ['low', 'medium', 'high', 'urgent'];

    protected static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub
        static::created(function ($task){
            if ($task->user){
                $emailTo = $task->user->email;
                $name = $task->user->name;
                $taskName = $task->name;
                $projectName = $task->project->name;
                $expr = $task->expire_date;
                Mail::to($emailTo)->send(new NewTaskNotificationMail($name, $taskName, $projectName, $expr));
            }
        });

    }

    public function project(){
        return $this->belongsTo(Project::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function hasProject(){
        return $this->project()->first() ? true : false;
    }

    public function hasUser(){
        return $this->user()->first() ? true : false;
    }

    public function isDone(){
        return $this->is_done ? true : false;
    }
}
