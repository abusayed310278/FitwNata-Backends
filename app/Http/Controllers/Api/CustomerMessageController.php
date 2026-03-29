<?php

namespace App\Http\Controllers\Api;

use App\Models\Chat;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CustomerMessageController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate([
            'coach_id' => 'required|exists:users,id',
            'message' => 'required',
            'chat_id' => 'required|exists:chats,id',
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
            // $request->validate([
            //     'chat_id' => 'exists:chats,id',
            // ]);

            $chat_id = $request->chat_id;
        }
        else
        {
            $chat = Chat::create([
                'customer_id' => $request->customer_id ?? Auth::Id(),
                'coach_id' => $request->coach_id,
                'sender_id' => $request->customer_id ?? Auth::Id(),
            ]);

            $chat_id = $chat->id;
        }

        $message = ChatMessage::create([
            'chat_id' => $chat_id,
            'sender_id' => $request->customer_id ?? Auth::Id(),
            'receiver_id' => $request->coach_id,
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
            ->where('customer_id', Auth::Id())
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Customer Message List',
            'data' => $data,
        ]);
    }
}
