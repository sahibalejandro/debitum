<?php

namespace Tests\Unit;

use App\Payment;
use App\User;
use Tests\TestCase;

class UserTest extends TestCase
{
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
}
