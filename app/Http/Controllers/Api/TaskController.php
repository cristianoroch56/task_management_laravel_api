<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class TaskController extends Controller
{
    public function getTasks(Request $request)
    {
        try {
            $query = Task::with('user')->orderBy('id', 'desc');

            if (!empty($request->status)) {
                $query->whereStatus($request->status);
            }

            if (!empty($request->assignee)) {
                $query->whereHas('user', function ($query) use ($request) {
                    $query->whereId($request->assignee);
                });
            }

            $tasks = $query->get();

            return $this->sendResponse($tasks, 'Task list.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return $this->sendError('Something went wrong!', [], HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getTaskDetails(Request $request)
    {
        $tasks = Task::whereId($request->id)->first();

        if (empty($tasks)) {
            return $this->sendError('Task does not exists.', [], HttpResponse::HTTP_NOT_FOUND);
        }

        return $this->sendResponse($tasks, 'Task details.');
    }

    public function addTask(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'assignee' => 'required|exists:users,id',
                'name' => 'required',
                'description' => 'required',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->messages() as $key => $value) {
                    return $this->sendError($value[0], [], HttpResponse::HTTP_BAD_REQUEST);
                }
            }

            if (!in_array($request->status,array_flip(Task::STATUS))) {
                return $this->sendError('Invalid task status request', [], HttpResponse::HTTP_BAD_REQUEST);
            }

            $task = new Task();
            $task->assignee = $request->assignee;
            $task->name = $request->name;
            $task->description = $request->description;
            $task->status = !empty($request->status) ? $request->status : Task::STATUS['New'];

            if (!$task->save()) {
                return $this->sendError('Something went wrong while creating the task.', [], HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
            }

            return $this->sendResponse([], 'Task saved successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return $this->sendError('Something went wrong!', [], HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateTask(Request $request, $taskId = null)
    {
        try {
            $validator = Validator::make($request->all(), [
                'assignee' => 'required|exists:users,id',
                'name' => 'required',
                'description' => 'required',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->messages() as $key => $value) {
                    return $this->sendError($value[0], [], HttpResponse::HTTP_BAD_REQUEST);
                }
            }

            if (!in_array($request->status,array_flip(Task::STATUS))) {
                return $this->sendError('Invalid task status request', [], HttpResponse::HTTP_BAD_REQUEST);
            }

            $task = Task::whereId($request->taskId)->first();

            if (empty($task)) {
                return $this->sendError('Task does not exists.', [], HttpResponse::HTTP_NOT_FOUND);
            }

            $task->assignee = $request->assignee;
            $task->name = $request->name;
            $task->description = $request->description;
            $task->status = !empty($request->status) ? $request->status : Task::STATUS['New'];

            if (!$task->save()) {
                return $this->sendError('Something went wrong while creating the task.', [], HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
            }

            return $this->sendResponse([], 'Task update successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return $this->sendError('Something went wrong!', [], HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteTask(Request $request, $taskId = null)
    {
        try {
            $task = Task::whereId($request->taskId)->first();

            if (empty($task)) {
                return $this->sendError('Task does not exists.', [], HttpResponse::HTTP_NOT_FOUND);
            }

            $task->delete();

            return $this->sendResponse([], 'Task delete successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return $this->sendError('Something went wrong!', [], HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
