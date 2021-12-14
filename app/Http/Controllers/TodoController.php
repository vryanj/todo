<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Todo;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //list user todos
        $todos = Todo::where('user_id', auth()->id())
            ->orderBy('position', 'asc')
            ->get();

        return response()->json([
            'todos' => $todos
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'task' => 'required',
        ]);

        $next_position = $this->getNextPosition();

        $todo = new Todo;
        $todo->task = $request->task;
        $todo->user_id = auth()->user()->id;
        $todo->position = $next_position;
        $todo->save();

        return response()->json(compact('todo'), 201);
    }

    public function getNextPosition()
    {
        $last = Todo::select('position')
            ->orderBy('position', 'desc')
            ->where('user_id', auth()->user()->id)
            ->first();
        return $last ? $last->position + 1 : 1;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $todo = Todo::where('user_id', auth()->id())
            ->where('id', $id)
            ->firstOrFail();

        return response()->json([
            'todo' => $todo
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'task' => 'required',
        ]);

        $todo = Todo::where('user_id', auth()->id())
            ->where('id', $id)
            ->firstOrFail();

        $todo->task = $request->task;
        $todo->save();

        return response()->json(compact('todo'), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $todo = Todo::where('user_id', auth()->id())
            ->where('id', $id)
            ->first();

        $todo->delete();

        return response()->json(null, 204);
    }
}
