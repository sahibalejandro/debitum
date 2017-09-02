<?php

namespace Tests\Unit;

use App\Payment;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function pay_a_payment()
    {
        $payment = factory(Payment::class)->create(['paid_at' => null]);

        $payment->pay();

        $this->assertNotNull($payment->paid_at);
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'paid_at' => $payment->paid_at,
        ]);
    }

    /** @test */
    public function repeat_designator_is_null_if_no_repeat_period_is_provided()
    {
        $payment = factory(Payment::class)->make([
            'repeat_period' => null,
            'repeat_designator' => 'weeks',
        ]);

        $payment->save();

        $this->assertNull($payment->repeat_designator);
    }

    /** @test */
    public function repeat_designator_is_null_repeat_period_is_zero()
    {
        $payment = factory(Payment::class)->make([
            'repeat_period' => 0,
            'repeat_designator' => 'weeks',
        ]);

        $payment->save();

        $this->assertNull($payment->repeat_designator);
    }

    /** @test */
    public function generates_due_date_proximity_string()
    {
        $payment = new Payment(['due_date' => date('Y-m-d')]);
        $this->assertEquals('Today', $payment->dueDateProximity);

        $payment->due_date = date('Y-m-d', strtotime('+1 days'));
        $this->assertEquals('Tomorrow', $payment->dueDateProximity);

        $payment->due_date = date('Y-m-d', strtotime('2000-01-01'));
        $this->assertEquals('January 01, 2000', $payment->dueDateProximity);
    }

    /** @test */
    public function format_amount_into_currency()
    {
        $payment = new Payment(['amount' => 123456]);

        $this->assertEquals('$1,234.56', $payment->amountAsCurrency);
    }
}
