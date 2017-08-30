<?php

namespace App\Http\Controllers;

use App\Payment;
use App\PayAction;

class PaidController extends Controller
{
    public function store()
    {
        request()->validate(['id' => 'required']);

        $nextPayment = PayAction::instance()
            ->pay(Payment::findOrFail(request('id')))
            ->getNextPayment();

        return ['next_payment' => $nextPayment];
    }
}
