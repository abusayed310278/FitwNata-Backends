<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        // Validate form
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // Prepare email data for helper function
        $data = [
            'to'         => 'med.hayballa@gmail.com',
            'subject'    => $validated['subject'],
            'mail_from'  => $validated['email'],
            'project'    => 'FitwNata',
            'view'       => 'emails.contact',
            'data'       => [
                'name'    => $validated['name'],
                'email'   => $validated['email'],
                'subject' => $validated['subject'],
                'message' => $validated['message'],
            ],
        ];

        sendMail($data); // Call your helper function

        return back()->with('success', 'Your message has been sent successfully!');
    }
}
