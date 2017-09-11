| Description | Due Date | Amount |
| ----------- |:--------:|-------:|
@foreach ($payments as $payment)
| <span class="circle circle--{{ $payment->level }}"></span> {{ $payment->name }}     | {{ $payment->dueDateProximity }}      | {{ $payment->amountAsCurrency }} |
@endforeach
