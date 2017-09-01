<?php

namespace App\Http\Controllers;

use App\Payment;
use App\Http\Requests\PaymentForm;
use Illuminate\Auth\Access\AuthorizationException;

class PaymentsController extends Controller
{
    /**
     * Display a list of payments.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $payments = request()->user()->payments()
            ->whereNull('paid_at')
            ->orderBy('due_date')
            ->get();

        return view('layout')->with([
            'component' => 'payments',
            'props' => [
                ':data-payments' => $payments,
            ],
        ]);
    }

    /**
     * Display a form to create a payment.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('layout')->with([
            'title' => 'Add',
            'component' => 'payment-form',
        ]);
    }

    /**
     * Store a new payment into database.
     *
     * @param  \App\Http\Requests\PaymentForm $request
     * @return array
     */
    public function store(PaymentForm $request)
    {
        $payment = $request->store();

        return compact('payment');
    }

    /**
     * Display a form to edit a payment.
     *
     * @param  \App\Payment $payment
     * @return \Illuminate\View\View
     */
    public function edit(Payment $payment)
    {
        return view('layout')->with([
            'title' => 'Edit',
            'component' => 'payment-form',
            'props' => [
                ':data-payment' => $payment,
            ]
        ]);
    }

    /**
     * Update a payment.
     *
     * @param  \App\Http\Requests\PaymentForm $request
     * @param  \App\Payment $payment
     * @return array
     */
    public function update(PaymentForm $request, Payment $payment)
    {
        $payment = $request->update($payment);

        return compact('payment');
    }

    /**
     * Delete a payment.
     *
     * @param  \App\Payment $payment
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Payment $payment)
    {
        if (! request()->user()->owns($payment)) {
            throw new AuthorizationException();
        }

        $payment->delete();
        return response(['success' => true]);
    }
}
