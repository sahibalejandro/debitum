<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentForm;
use App\Payment;

class PaymentsController extends Controller
{
    public function index()
    {
        $payments = Payment::whereNull('paid_at')
            ->orderBy('due_date')
            ->get();

        return view('layout')->with([
            'component' => 'payments',
            'props' => [
                ':data-payments' => $payments,
            ],
        ]);
    }

    public function create()
    {
        return view('layout')->with([
            'component' => 'payment-form',
        ]);
    }

    public function store(PaymentForm $request)
    {
        $payment = $request->store();

        return compact('payment');
    }

    public function edit(Payment $payment)
    {
        return view('layout')->with([
            'component' => 'payment-form',
            'props' => [
                ':data-payment' => $payment,
            ]
        ]);
    }

    public function update(PaymentForm $request, Payment $payment)
    {
        $payment = $request->update($payment);

        return compact('payment');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();

        return response(['success' => true]);
    }
}
