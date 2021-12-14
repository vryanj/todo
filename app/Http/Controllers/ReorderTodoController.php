<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Todo;

class ReorderTodoController extends Controller
{
    public function update(Request $request)
    {
        //validate
        $validated = $request->validate([
            'position' => 'required|integer|min:1',
            'id' => 'required|integer|exists:todos,id',
        ]);

        $id = $request->input("id");
        $position = $request->input('position');

        $todo = Todo::where('id', $id)
        ->where('user_id', auth()->id())
        ->first();

        if (!$todo) {
            //return json error
            return response()->json([
                'message' => 'Task not found'
            ], 404);
        }

        //all users todos
        $todos = Todo::select('id','position')->where('user_id', auth()->id())
            ->orderBy('position', 'asc')
            ->get();

        $array = $todos->pluck('id')->toArray();

        $id_to_move = $id;
        $move_to_pos = $position-1;

        //remove id_to_move in $array
        array_splice($array, array_search($id_to_move, $array ), 1);

        //insert id into position
        $rearranged = array_merge(array_slice($array, 0, $move_to_pos), array($id_to_move), array_slice($array, $move_to_pos));
    
        //update database
        foreach ($rearranged as $key => $value) {
            Todo::find($value)->update(['position'=>$key+1]);
        }

        $todo->refresh();

        return response()->json(compact('todo'), 200);
    }
}
