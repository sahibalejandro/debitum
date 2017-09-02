| Description | Due Date | Amount |
| ----------- |:--------:|-------:|
@foreach ($payments as $payment)
| {{ $payment->name }}     | {{ $payment->dueDateProximity }}      | {{ $payment->amountAsCurrency }} |
@endforeach
