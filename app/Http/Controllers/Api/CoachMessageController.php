<?php

namespace App\Http\Controllers\Api;

use App\Models\Chat;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CoachMessageController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:users,id',
            'message' => 'required',
        ]);

        // dd($request->all());

        if ($request->hasFile('attachment')) 
        {
            $request->validate([
                'attachment' => 'mimes:jpg,jpeg,png,pdf,docs,docx|max:20048',
            ]);

            $attachment = uploadImage($request->file('attachment'), 'images/message-attachments');
        }

        if($request->chat_id)
        {
            $request->validate([
                'chat_id' => 'exists:chats,id',
            ]);

            $chat_id = $request->chat_id;
        }
        else
        {
            $chat = Chat::create([
                'customer_id' => $request->customer_id,
                'coach_id' => $request->coach_id ?? Auth::Id(),
                'sender_id' => $request->coach_id ?? Auth::Id(),
            ]);

            $chat_id = $chat->id;
        }

        $message = ChatMessage::create([
            'chat_id' => $chat_id,
            'sender_id' => $request->coach_id ?? Auth::Id(),
            'receiver_id' => $request->customer_id,
            'message' => $request->message,
            'attachment' => $attachment ?? null,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Message sent successfully',
        ]);
    }

    public function list(Request $request)
    {
        $data = Chat::with('messages', 'customer', 'coach')
            ->where('sender_id', Auth::Id())
            ->where('coach_id', Auth::Id())
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Coach Message List',
            'data' => $data,
        ]);
    }
}
