<?php

namespace Tests\Unit;

use App\PayAction;
use App\Payment;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PayActionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function pay_a_payment()
    {
        $payAction = new PayAction;
        $payment = factory(Payment::class)->create([
            'repeat_period' => null,
            'paid_at' => null,
        ]);

        $payAction->pay($payment);

        $this->assertNotNull($payment->paid_at);
        $this->assertNull($payAction->getNextPayment());
    }

    /** @test */
    public function pay_a_repeatable_payment_and_create_next_payment()
    {
        $payAction = new PayAction;
        $payment = factory(Payment::class)->create([
            'due_date' => '2017-01-01',
            'repeat_period' => 1,
            'repeat_designator' => 'weeks',
            'paid_at' => null,
        ]);

        $payAction->pay($payment);
        $nextPayment = $payAction->getNextPayment();

        $this->assertNotNull($nextPayment);
        $this->assertDatabaseHas('payments', [
            'id' => $nextPayment->id,
            'name' => $payment->name,
            'amount' => $payment->amount,
            'due_date' => '2017-01-08',
            'repeat_period' => $payment->repeat_period,
            'repeat_designator' => $payment->repeat_designator,
            'paid_at' => null,
        ]);
    }
}
