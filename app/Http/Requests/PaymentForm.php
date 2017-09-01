<?php

namespace App\Http\Requests;

use App\Payment;
use Illuminate\Foundation\Http\FormRequest;

class PaymentForm extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->method() == 'POST') {
            return true;
        }

        return $this->user()->owns($this->route('payment'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'amount' => 'required|numeric',
            'due_date' => 'required|date',
            'repeat_period' => 'required_with:repeat_designator|numeric|nullable',
            'repeat_designator' => 'required_with:repeat_period|in:weeks,months,years|nullable',
        ];
    }

    /**
     * Create a new payment using the request data and store it into database.
     *
     * @return \App\Payment
     */
    public function store()
    {
        $payment = new Payment($this->all());

        $this->user()->payments()->save($payment);

        return $payment->fresh();
    }

    /**
     * Update a payment using the request data.
     *
     * @param  \App\Payment $payment
     * @return \App\Payment
     */
    public function update(Payment $payment)
    {
        $payment->update($this->all());

        return $payment;
    }
}
