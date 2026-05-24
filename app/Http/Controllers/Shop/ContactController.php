<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        ContactMessage::create($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'تم إرسال رسالتك بنجاح! سنتواصل معك قريباً.']);
        }

        return back()->with('success', 'تم إرسال رسالتك بنجاح! سنتواصل معك قريباً.');
    }
}
