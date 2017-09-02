<?php

namespace Tests\Feature;

use App\User;
use App\Payment;
use Tests\TestCase;
use App\Mail\PaymentsReport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SendEmailNotificationsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function send_notification_email_to_users_who_have_incoming_payments()
    {
        Mail::fake();
        $user = factory(User::class)->create();
        factory(Payment::class)->create([
            'user_id' => $user->id,
            'due_date' => date('Y-m-d'),
            'paid_at' => null,
        ]);

        Artisan::call('payments:notify');

        Mail::assertSent(PaymentsReport::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    /** @test */
    public function send_notification_email_to_users_who_have_overdue_payments()
    {
        Mail::fake();
        $user = factory(User::class)->create();
        factory(Payment::class)->create([
            'user_id' => $user->id,
            'due_date' => date('Y-m-d', strtotime('yesterday')),
            'paid_at' => null,
        ]);

        Artisan::call('payments:notify');

        Mail::assertSent(PaymentsReport::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    /** @test */
    public function dont_send_notification_email_to_users_who_does_not_have_incoming_or_overdue_payments()
    {
        Mail::fake();
        factory(User::class)->create();

        Artisan::call('payments:notify');

        Mail::assertNotSent(PaymentsReport::class);
    }
}
