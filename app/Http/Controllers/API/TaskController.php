<?php

namespace App\Http\Controllers\API;

use App\Events\TaskCreated;
use App\Http\Controllers\Controller;
use App\Task;
use App\Http\Resources\TaskResource;
use App\Listeners\SendNotificationTaskCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(auth()->user()->isManager)
            $tasks = Task::all();
        else 
            $tasks = Task::where('user_id', auth()->user()->id)->get();
     
        return response(['tasks'=> TaskResource::collection($tasks)]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $validation = $request->validate([
            'summary' => 'required|max:2500',
            'date' => 'required|date'
        ]);

        $task = new Task();
        $task->summary = $request->get('summary');
        $task->completed_at = $request->get('date');

         if(auth()->user()->tasks()->save($task))
         {

           TaskCreated::dispatch($task);

            return response([
                'success' => true,
                'task' => $task
            ], 201);

        } else {

            return response([
                'success' => false,
                'message' => 'Task could not be created'
            ], 500);
        
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        if(auth()->user()->isManager)
            return  Task::firstOrFail($task->id);
        else 
           return auth()->user()->tasks()->findOrfail($task->id);     
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        //
        $validation = $request->validate([
            'summary'=> 'required|max:2500',
            'date' => 'required|date'
        ]);
    
        $selectedTask = auth()->user()->tasks()->findOrfail($task->id);
        
        $updated =  $selectedTask->update([
                'summary' => $request->get('summary'),
                'completed_at' => $request->get('date')
        ]);
        
        if ($updated)
            return response([
                'success' => true,
                'task' => $selectedTask
            ]);
        else
            return response([
                'success' => false,
                'message' => 'Task could not be updated'
            ], 500);
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        if(auth()->user()->isManager)
        {
            if($task->delete())
                return response([
                    'success' => true,
                    'message' => "Task has been deleted"
                ]); 

        } else {
            return response([
                'success' => false,
                'message' => "You can't perform this action"
            ], 403);
        }   
    }
}
