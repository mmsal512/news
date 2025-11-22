<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NewsletterController extends Controller
{
    /**
     * Subscribe to newsletter
     */
    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $subscriber = NewsletterSubscriber::firstOrCreate(
            ['email' => $request->email],
            [
                'name' => $request->name,
                'is_active' => true,
                'subscribed_at' => now(),
            ]
        );

        if (!$subscriber->wasRecentlyCreated && $subscriber->is_active) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'You are already subscribed.'], 200);
            }
            return back()->with('info', 'You are already subscribed to our newsletter.');
        }

        if (!$subscriber->is_active) {
            $subscriber->update([
                'is_active' => true,
                'subscribed_at' => now(),
                'unsubscribed_at' => null,
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Successfully subscribed to newsletter!'], 201);
        }

        return back()->with('success', 'Thank you for subscribing to our newsletter!');
    }

    /**
     * Unsubscribe from newsletter
     */
    public function unsubscribe($token)
    {
        $subscriber = NewsletterSubscriber::where('token', $token)->first();

        if (!$subscriber) {
            return redirect()->route('home')->with('error', 'Invalid unsubscribe link.');
        }

        $subscriber->unsubscribe();

        return redirect()->route('home')->with('success', 'You have been unsubscribed from our newsletter.');
    }
}
