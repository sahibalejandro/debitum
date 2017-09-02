@component('mail::message')

# Payments Report
Hello,<br>
This is a report of your incoming or overdue payments.

{{-- Incoming Payments --}}
@if($user->incomingPayments->count() > 0)
## Incoming
@include('email.payments-list', ['payments' => $user->incomingPayments])
@endif

{{-- Overdue Payments --}}
@if ($user->overduePayments->count() > 0)
## Overdue

@include('email.payments-list', ['payments' => $user->overduePayments])
@endif

{{-- Call to action --}}
@component('mail::button', ['url' => url('/')])
Go to website
@endcomponent

@endcomponent
