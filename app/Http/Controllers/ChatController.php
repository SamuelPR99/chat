<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index(string $room = 'general')
    {
        $messages = Message::with('user')
            ->where('room', $room)
            ->latest()
            ->take(50)
            ->get()
            ->reverse()
            ->values();

        return view('chat.index', compact('messages', 'room'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'room' => 'required|string|max:50',
        ]);

        $message = Message::create([
            'user_id' => Auth::id(),
            'content' => $request->content,
            'room' => $request->room,
        ]);        $message->load('user');

        \Log::info('Disparando evento MessageSent', [
            'message_id' => $message->id,
            'user_id' => Auth::id(),
            'room' => $message->room,
            'content' => $message->content
        ]);

        broadcast(new MessageSent($message, Auth::user()));

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    public function getMessages(string $room)
    {
        $messages = Message::with('user')
            ->where('room', $room)
            ->latest()
            ->take(50)
            ->get()
            ->reverse()
            ->values();

        return response()->json($messages);
    }
}
