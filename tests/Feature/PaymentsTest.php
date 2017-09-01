<?php

namespace Tests\Feature;

use App\User;
use App\Payment;
use Tests\TestCase;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentsTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function payment_name_is_required()
    {
        $this->call('POST', '/payments');
        $this->assertTrue(session('errors')->has('name'));
    }

    /** @test */
    public function payment_amount_is_required_and_is_numeric()
    {
        $this->call('POST', '/payments');
        $this->assertTrue(session('errors')->has('amount'));

        $this->call('POST', '/payments', ['amount' => 'abc']);
        $this->assertTrue(session('errors')->has('amount'));

        $this->call('POST', '/payments', ['amount' => 123]);
        $this->assertFalse(session('errors')->has('amount'));
    }

    /** @test */
    public function payment_due_date_is_required_and_is_a_date()
    {
        $this->call('POST', '/payments');
        $this->assertTrue(session('errors')->has('due_date'));

        $this->call('POST', '/payments', ['due_date' => 'invalid date']);
        $this->assertTrue(session('errors')->has('due_date'));

        $this->call('POST', '/payments', ['due_date' => '2000-01-01']);
        $this->assertFalse(session('errors')->has('due_date'));
    }

    /** @test */
    public function payment_repeat_period_is_required_and_numeric_when_repeat_designator_is_present()
    {
        $this->call('POST', '/payments');
        $this->assertFalse(session('errors')->has('repeat_period'));

        $this->call('POST', '/payments', ['repeat_designator' => 'weeks']);
        $this->assertTrue(session('errors')->has('repeat_period'));

        $this->call('POST', '/payments', ['repeat_period' => 'abc', 'repeat_designator' => 'weeks']);
        $this->assertTrue(session('errors')->has('repeat_period'));

        $this->call('POST', '/payments', ['repeat_period' => 123, 'repeat_designator' => 'weeks']);
        $this->assertFalse(session('errors')->has('repeat_period'));
    }

    /** @test */
    public function payment_repeat_designator_is_required_if_repeat_period_is_present()
    {
        $this->call('POST', '/payments');
        $this->assertFalse(session('errors')->has('repeat_designator'));

        $this->call('POST', '/payments', ['repeat_period' => 1]);
        $this->assertTrue(session('errors')->has('repeat_designator'));

        $this->call('POST', '/payments', ['repeat_period' => 1, 'repeat_designator' => 'weeks']);
        $this->assertFalse(session('errors')->has('repeat_designator'));
    }

    /** @test */
    public function payment_repeat_designator_must_have_an_allowed_value()
    {
        $this->call('POST', '/payments', ['repeat_designator' => 'invalid']);
        $this->assertTrue(session('errors')->has('repeat_designator'));

        collect(['weeks', 'months', 'years'])->each(function ($validDesignator) {
            $this->call('POST', '/payments', ['repeat_designator' => $validDesignator]);
            $this->assertFalse(session('errors')->has('repeat_designator'));
        });
    }

    /** @test */
    public function payment_repeat_period_can_be_nullable()
    {
        $this->call('POST', '/payments', ['repeat_period' => null]);
        $this->assertFalse(session('errors')->has('repeat_period'));
    }

    /** @test */
    public function payment_repeat_designator_can_be_nullable()
    {
        $this->call('POST', '/payments', ['repeat_designator' => null]);
        $this->assertFalse(session('errors')->has('repeat_designator'));
    }

    /** @test */
    public function create_a_new_payment()
    {
        $response = $this->json('POST', '/payments', [
            'name' => 'Payment Test',
            'amount' => 25000,
            'due_date' => '2000-01-01',
            'repeat_period' => 1,
            'repeat_designator' => 'months',
        ]);

        $this->assertDatabaseHas('payments', [
            'user_id' => $this->user->id,
            'name' => 'Payment Test',
            'amount' => 25000,
            'due_date' => '2000-01-01',
            'repeat_period' => 1,
            'repeat_designator' => 'months',
            'paid_at' => null,
        ]);

        $response->assertExactJson([
            'payment' => [
                'id' => $this->user->payments->first()->id,
                'name' => 'Payment Test',
                'amount' => 25000,
                'due_date' => '2000-01-01',
                'repeat_period' => 1,
                'repeat_designator' => 'months',
                'paid_at' => null,
            ]
        ]);
    }

    /** @test */
    public function update_a_payment()
    {
        $payment = factory(Payment::class)->create([
            'user_id' => $this->user->id,
            'repeat_period' => 1,
            'repeat_designator' => 'months',
            'paid_at' => null,
        ]);

        $response = $this->json('PATCH', '/payments/' . $payment->id, [
            'name' => 'Edited Payment Test',
            'amount' => 35000,
            'due_date' => '2001-01-01',
            'repeat_period' => 2,
            'repeat_designator' => 'weeks',
        ]);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'user_id' => $this->user->id,
            'name' => 'Edited Payment Test',
            'amount' => 35000,
            'due_date' => '2001-01-01',
            'repeat_period' => 2,
            'repeat_designator' => 'weeks',
        ]);

        $response->assertExactJson([
            'payment' => [
                'id' => $payment->id,
                'name' => 'Edited Payment Test',
                'amount' => 35000,
                'due_date' => '2001-01-01',
                'repeat_period' => 2,
                'repeat_designator' => 'weeks',
                'paid_at' => null,
            ],
        ]);
    }

    /** @test */
    public function a_user_cannot_update_a_payment_of_another_user()
    {
        $this->withoutExceptionHandling();
        $this->expectException(AuthorizationException::class);
        $payment = factory(Payment::class)->create();

        $this->json('PATCH', '/payments/' . $payment->id, [
            'name' => 'Foo',
            'amount' => 123,
            'due_date' => '2000-01-01',
            'repeat_period' => null,
            'repeat_designator' => null,
        ]);

        $this->assertDatabaseHas('paymets', [
            'id' => $payment->id,
            'name' => $payment->name,
            'amount' => $payment->amount,
            'due_date' => $payment->due_date,
            'repeat_period' => $payment->repeat_period,
            'repeat_designator' => $payment->repeat_designator,
            'paid_at' => $payment->paid_at,
        ]);
    }

    /** @test */
    public function delete_a_payment()
    {
        $payment = factory(Payment::class)->create(['user_id' => $this->user->id]);

        $response = $this->json('DELETE', '/payments/' . $payment->id);

        $response->assertExactJson(['success' => true]);
        $this->assertDatabaseMissing('payments', ['id' => $payment->id]);
    }

    /** @test */
    public function a_user_cannot_delete_payments_of_another_user()
    {
        $this->withoutExceptionHandling();
        $this->expectException(AuthorizationException::class);
        $payment = factory(Payment::class)->create();

        $this->json('DELETE', '/payments/' . $payment->id);

        $this->assertDatabaseHas('payments', ['id' => $payment->id]);
    }

    /** @test */
    public function pay_a_payment()
    {
        $payment = factory(Payment::class)->create([
            'user_id' => $this->user->id,
            'repeat_period' => null,
            'paid_at' => null,
        ]);

        $response = $this->json('POST', '/paid', ['id' => $payment->id]);

        $this->assertDatabaseMissing('payments', ['id' => $payment->id, 'paid_at' => null]);
        $response->assertJson(['next_payment' => null]);
    }

    /** @test */
    public function a_user_cannot_pay_a_payment_of_another_user()
    {
        $this->withoutExceptionHandling();
        $this->expectException(AuthorizationException::class);
        $payment = factory(Payment::class)->create([
            'repeat_period' => null,
            'paid_at' => null,
        ]);

        $this->json('POST', '/paid', ['id' => $payment->id]);

        $this->assertDatabaseHas('payments', ['id' => $payment->id, 'paid_at' => null]);
    }

    /** @test */
    public function pay_a_repeatable_payment_and_get_the_next_payment_within_the_response()
    {
        $payment = factory(Payment::class)->create([
            'user_id' => $this->user->id,
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
