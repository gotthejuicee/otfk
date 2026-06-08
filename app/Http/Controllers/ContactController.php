<?php

namespace App\Http\Controllers;

use App\Mail\FeedbackReceived;
use App\Models\FeedbackMessage;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index()
    {
        return view('contacts');
    }

    public function store(Request $request)
    {
        // Антиспам: приховане поле-пастка (honeypot). Боти його заповнюють.
        if (filled($request->input('website'))) {
            return back()->with('status', 'Дякуємо! Ваше звернення успішно надіслано.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $data['ip'] = $request->ip();
        $feedback = FeedbackMessage::create($data);

        // Сповіщення на пошту коледжу (не ламаємо форму, якщо пошта не налаштована).
        $to = Setting::get('feedback_email') ?: Setting::get('contact_email');
        if ($to) {
            try {
                Mail::to($to)->send(new FeedbackReceived($feedback));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return back()->with('status', 'Дякуємо! Ваше звернення успішно надіслано.');
    }
}
