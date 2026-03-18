<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    public function index()
    {
        return Note::where('user_id', Auth::id())
            ->orderBy('id', 'asc')
            ->get();
    }

    public function store(Request $request)
    {
        return Note::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
        ]);
    }

    // public function update(Request $request, $id)
    // {
    //     $note = Note::where('id', $id)
    //         ->where('user_id', Auth::id())
    //         ->firstOrFail();

    //     $note->update($request->only(['title', 'content']));
    //     return $note;
    // }
    
     public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
        ]);
    
        $note = Note::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
    
        $note->update($request->only(['title', 'content']));
    
        return response()->json($note);
    }

    public function destroy($id)
    {
        Note::where('id', $id)
            ->where('user_id', Auth::id())
            ->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
