<?php

namespace Tests\Feature;

use App\Payment;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function create_a_new_payment()
    {
        $this->json('POST', '/payments', [
            'name' => 'Payment Test',
            'amount' => 25000,
            'due_date' => '2000-01-01',
            'repeat_period' => 1,
            'repeat_designator' => 'months',
        ]);

        $this->assertDatabaseHas('payments', [
            'name' => 'Payment Test',
            'amount' => 25000,
            'due_date' => '2000-01-01',
            'repeat_period' => 1,
            'repeat_designator' => 'months',
            'paid_at' => null,
        ]);
    }

    /** @test */
    public function update_a_payment()
    {
        $payment = factory(Payment::class)->create([
            'repeat_period' => 1,
            'repeat_designator' => 'months',
        ]);

        $this->json('PATCH', '/payments/' . $payment->id, [
            'name' => 'Edited Payment Test',
            'amount' => 35000,
            'due_date' => '2001-01-01',
            'repeat_period' => 2,
            'repeat_designator' => 'weeks',
        ]);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'name' => 'Edited Payment Test',
            'amount' => 35000,
            'due_date' => '2001-01-01',
            'repeat_period' => 2,
            'repeat_designator' => 'weeks',
        ]);
    }

    /** @test */
    public function delete_a_payment()
    {
        $payment = factory(Payment::class)->create();

        $this->json('DELETE', '/payments/' . $payment->id);

        $this->assertDatabaseMissing('payments', ['id' => $payment->id]);
    }

    /** @test */
    public function pay_a_payment()
    {
        $payment = factory(Payment::class)->create([
            'repeat_period' => null,
            'paid_at' => null,
        ]);

        $response = $this->json('POST', '/paid', ['id' => $payment->id]);

        $this->assertDatabaseMissing('payments', ['id' => $payment->id, 'paid_at' => null]);
        $response->assertJson(['next_payment' => null]);
    }

    /** @test */
    public function pay_a_repeatable_payment_and_get_the_next_payment_within_the_response()
    {
        $payment = factory(Payment::class)->create([
            'due_date' => '2017-01-01',
            'repeat_period' => 1,
            'repeat_designator' => 'weeks',
            'paid_at' => null,
        ]);

        $response = $this->json('POST', '/paid', ['id' => $payment->id]);

        $response->assertJson([
            'next_payment' => [
                'name' => $payment->name,
                'amount' => $payment->amount,
                'due_date' => '2017-01-08',
                'repeat_period' => $payment->repeat_period,
                'repeat_designator' => $payment->repeat_designator,
                'paid_at' => null,
            ]
        ]);
    }
}
