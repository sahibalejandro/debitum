<?php

namespace App\Http\Controllers;

use App\Payment;
use App\PayAction;
use Illuminate\Auth\Access\AuthorizationException;

class PaidController extends Controller
{
    /**
     * Display a list of paid payments.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $payments = request()->user()->payments()
            ->whereNotNull('paid_at')
            ->orderBy('due_date')
            ->get();

        return view('layout')->with([
            'component' => 'history',
            'props' => [
                ':payments' => $payments,
            ],
        ]);
    }

    /**
     * Create a new paid payment.
     *
     * @return array
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store()
    {
        request()->validate(['id' => 'required']);

        $payment = request()->user()->payments()->find(request('id'));

        if (! $payment) {
            throw new AuthorizationException();
        }

        $nextPayment = PayAction::instance()
            ->pay(Payment::findOrFail(request('id')))
            ->getNextPayment();

        return ['next_payment' => $nextPayment];
    }
}
