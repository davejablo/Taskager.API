<?php


namespace App\Http\Repositories;

use App\Http\RepositoryInterfaces\TaskRepositoryInterface;
use App\Mail\NewTaskNotificationMail;
use App\Task;
use App\Http\Resources\TaskResource;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class TaskRepository implements TaskRepositoryInterface
{
    public function createAndReturnTask($request){
        if ($user_id = $request->user_id){
            $hours = $request->hours_spent;
            $foundUser = User::find($user_id);
            $newTask = new Task($request->validated());
            if ($hours){
                $newTask->task_cost = $hours * $foundUser->hr_wage;
                $newTask->status = 'done';
                $newTask->done_at = now();
                $newTask->is_done = true;
                $newTask->save();
            }
            else
            $newTask->status = 'in_progress';
            $newTask->save();
        }
        else
            $newTask = Task::create($request->validated());
        if ($newTask->save()){
            return new TaskResource($newTask);
        }
    }

    public function getTasks(){
        return $tasks = Task::paginate(5);
    }

    public function getTask($task){
        return $taskToReturn = Task::findOrFail($task->id);
    }

    public function destroyTask($taskToDestroy){
        $taskToDestroy->delete();
    }

    public function updateAndReturnTask($request, $id){
        $taskFromDb = Task::findOrFail($id);
        if ($hours = $request->hours_spent){
            //TODO: What if hours are updated by leader or admin or client ?
            $taskFromDb->task_cost = $hours * Auth::user()->hr_wage;
            $taskFromDb->is_done = true;
            $taskFromDb->status = 'done';
            $taskFromDb->done_at = Carbon::now()->toDateTimeString();
            $taskFromDb->hours_spent = $hours;
            $taskFromDb->update();
            return $taskFromDb = Task::findOrFail($id);
        }
        else if($user = $request->user_id){
            $taskFromDb->status = Task::TASK_STATUS[1];
            $taskFromDb->update($request->validated());
            $updatedTaskFromDb = Task::findOrFail($id);
            $emailTo = $updatedTaskFromDb->user->email;
            $name = $updatedTaskFromDb->user->name;
            $taskName = $updatedTaskFromDb->name;
            $projectName = $updatedTaskFromDb->project->name;
            $expr = $updatedTaskFromDb->expire_date;
            Mail::to($emailTo)->send(new NewTaskNotificationMail($name, $taskName, $projectName, $expr));
            return new TaskResource($updatedTaskFromDb);
        }
    }

    public function getTaskProject(Task $task){
        return $taskProject = $task->project()->firstOrFail();
    }

    public function getTaskUser(Task $task){
        return $taskProject = $task->user()->firstOrFail();
    }
}