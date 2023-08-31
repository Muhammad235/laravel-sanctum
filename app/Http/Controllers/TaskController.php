<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreTaskRequest;

class TaskController extends Controller
{
    use HttpResponses;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return TaskResource::collection(
            Task::where('user_id', Auth::user()->id)->get()
        );

        // Task::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $request->validated($request->all());

        $task = Task::create([
            'user_id' => Auth::user()->id,
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return new TaskResource($task);
    }

    /**
     * Display the specified resource.
     */
    // public function show($id)
    // {
    //     $task = Task::where('id', $id)->get();

    //     return new TaskResource($task);
    // }

    public function show(Task $task)
    {
        return $this->isNotAuthorize($task) ? $this->isNotAuthorize($task) :  new TaskResource($task);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $this->isNotAuthorize($task);

        $task->update($request->all());

        return new TaskResource($task);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {

        return $this->isNotAuthorize($task) ? $this->isNotAuthorize($task) :  $task->delete();;

    }

    private function isNotAuthorize($task){

        if (Auth::user()->id !== $task->user_id) {
            return $this->error('', 'You are not authorized to make this request', 403);
        }
    }
}
