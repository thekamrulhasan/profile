<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ContactFormMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        // Rate limiting
        $key = 'contact-form:' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            
            throw ValidationException::withMessages([
                'email' => "Too many contact form submissions. Please try again in {$seconds} seconds.",
            ]);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        RateLimiter::hit($key, 300); // 5 minutes

        try {
            Mail::to(config('portfolio.owner.email'))
                ->send(new ContactFormMail(
                    $request->name,
                    $request->email,
                    $request->subject,
                    $request->message
                ));

            return response()->json([
                'message' => 'Thank you for your message! I will get back to you soon.',
                'success' => true
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Sorry, there was an error sending your message. Please try again later.',
                'success' => false
            ], 500);
        }
    }
}
