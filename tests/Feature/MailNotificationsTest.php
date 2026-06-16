<?php

namespace Tests\Feature;

use App\Mail\ApplicantRequestReceived;
use App\Mail\FeedbackReceived;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MailNotificationsTest extends TestCase
{
    use RefreshDatabase;

    private function setRecipient(string $email = 'pk@otfk.test'): void
    {
        Setting::updateOrCreate(['key' => 'feedback_email'], ['value' => $email]);
        cache()->forget('settings.map');
    }

    public function test_applicant_request_emails_committee(): void
    {
        Mail::fake();
        $this->setRecipient();

        $this->post('/zayavka', [
            'name' => 'Тарас Шевченко',
            'phone' => '+380501234567',
            'email' => 'taras@example.com',
        ])->assertRedirect();

        // Лист летить у afterResponse — у тесті це спрацьовує на kernel terminate().
        Mail::assertSent(ApplicantRequestReceived::class, fn ($mail) => $mail->hasTo('pk@otfk.test'));
    }

    public function test_contact_form_emails_college(): void
    {
        Mail::fake();
        $this->setRecipient();

        $this->post('/kontakty', [
            'name' => 'Відвідувач',
            'message' => 'Питання щодо вступу',
        ])->assertRedirect();

        Mail::assertSent(FeedbackReceived::class);
    }

    public function test_no_email_when_recipient_not_configured(): void
    {
        Mail::fake();
        Setting::where('key', 'feedback_email')->update(['value' => '']);
        Setting::where('key', 'contact_email')->update(['value' => '']);
        cache()->forget('settings.map');

        $this->post('/zayavka', [
            'name' => 'Без пошти',
            'phone' => '+380500000000',
        ])->assertRedirect();

        Mail::assertNothingSent();
    }
}
