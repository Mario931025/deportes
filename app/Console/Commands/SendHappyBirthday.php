<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class SendHappyBirthday extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'birthday:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send birthday';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        foreach ($this->getBirthdays() as $birthday) {
            $birthday->notify(new \App\Notifications\Birthday);
        }
    }
    
    protected function getBirthdays()
    {
        $today = now();
        
        $users = User::whereMonth('birthday', $today->month)
                     ->whereDay('birthday', $today->day)
                     ->get();

        return $users;
    }
}
