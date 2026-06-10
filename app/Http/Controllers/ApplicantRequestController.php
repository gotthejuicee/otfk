<?php

namespace App\Http\Controllers;

use App\Mail\ApplicantRequestReceived;
use App\Models\ApplicantRequest;
use App\Models\Setting;
use App\Models\Specialty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ApplicantRequestController extends Controller
{
    public function create()
    {
        $specialties = Specialty::published()->ordered()->get(['id', 'title']);

        return view('applicants.create', compact('specialties'));
    }

    public function store(Request $request)
    {
        // Антиспам: приховане поле-пастка
        if (filled($request->input('website'))) {
            return back()->with('status', 'Дякуємо! Вашу заявку прийнято — ми звʼяжемося з вами.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'specialty_id' => ['nullable', 'integer', 'exists:specialties,id'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        $data['ip'] = $request->ip();
        $applicant = ApplicantRequest::create($data);

        // Лист приймальній комісії (форму не ламаємо, якщо пошта не налаштована)
        $to = Setting::get('feedback_email') ?: Setting::get('contact_email');
        if ($to) {
            try {
                Mail::to($to)->send(new ApplicantRequestReceived($applicant));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return back()->with('status', 'Дякуємо! Вашу заявку прийнято — ми звʼяжемося з вами найближчим часом.');
    }
}
