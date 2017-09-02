<?php

namespace App\Console\Commands;

use App\User;
use App\Mail\PaymentsReport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendEmailNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notifies users about their incoming payments';

    /**
     * Payments with due date before the next days will be treated
     * as incoming payments.
     *
     * @var int
     */
    protected $days = 3;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        User::has('incomingPayments')->orHas('overduePayments')
            ->get()
            ->each(function ($user) {
                Mail::to($user->email)->send(new PaymentsReport($user));
            });
    }
}
