<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Switch application language
     */
    public function switch(Request $request, $locale)
    {
        // Validate locale
        if (!in_array($locale, ['en', 'ar'])) {
            return redirect()->back();
        }

        // Set locale in session
        Session::put('locale', $locale);
        App::setLocale($locale);

        // Redirect back with success message
        return redirect()->back()->with('success', __('Language changed successfully.'));
    }
}

