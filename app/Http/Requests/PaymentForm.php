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
        return true;
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

    public function store()
    {
        return Payment::create($this->all());
    }

    public function update(Payment $payment)
    {
        $payment->update($this->all());

        return $payment;
    }
}
