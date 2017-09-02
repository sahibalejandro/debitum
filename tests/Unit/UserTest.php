<?php

namespace Tests\Unit;

use App\User;
use App\Payment;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function user_owns_an_entity()
    {
        $user = new User();
        $user->id = 123;

        $payment = new Payment();
        $payment->user_id = 123;

        $payment2 = new Payment();
        $payment2->user_id = 456;

        $this->assertTrue($user->owns($payment), 'A user should own an entity');
        $this->assertFalse($user->owns($payment2), 'A user should not own an entity');
    }

    /** @test */
    public function get_incoming_payments()
    {
        $user = factory(User::class)->create();

        $this->assertCount(0, $user->incomingPayments);

        // Not an incoming payment.
        factory(Payment::class)->create([
            'user_id' => $user->id,
            'paid_at' => date('Y-m-d'),
        ]);

        // Overdue payment
        factory(Payment::class)->create([
            'user_id' => $user->id,
            'due_date' => date('Y-m-d', strtotime('yesterday')),
            'paid_at' => null,
        ]);

        // Incoming payment.
        factory(Payment::class)->create([
            'user_id' => $user->id,
            'due_date' => date('Y-m-d', strtotime('tomorrow')),
            'paid_at' => null,
        ]);

        // Incoming payment out of range.
        factory(Payment::class)->create([
            'user_id' => $user->id,
            'due_date' => date('Y-m-d', strtotime('+3 days')),
            'paid_at' => null,
        ]);

        $this->assertCount(1, $user->load('incomingPayments')->incomingPayments);
    }

    /** @test */
    public function get_overdue_payments()
    {
        $user = factory(User::class)->create();

        $this->assertCount(0, $user->overduePayments);

        // Not an overdue payment
        factory(Payment::class)->create([
            'user_id' => $user->id,
            'due_date' => date('Y-m-d', strtotime('today')),
            'paid_at' => null,
        ]);

        // Overdue payment
        factory(Payment::class)->create([
            'user_id' => $user->id,
            'due_date' => date('Y-m-d', strtotime('yesterday')),
            'paid_at' => null,
        ]);

        // Paid payment
        factory(Payment::class)->create([
            'user_id' => $user->id,
            'due_date' => date('Y-m-d', strtotime('yesteday')),
            'paid_at' => date('Y-m-d'),
        ]);

        $this->assertCount(1, $user->load('overduePayments')->overduePayments);
    }
}
