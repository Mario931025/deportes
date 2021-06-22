<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Assistance;

class SendAbsenceMotivation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'motivation:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send motivation';

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
        foreach ($this->getAssistances() as $assistance) {
            $assistance->studentUser->notify(new \App\Notifications\Absence);            
        }
    }
    
    protected function getAssistances()
    {
        $assistances = Assistance::select('student_user_id')
            ->whereHas('studentUser.deviceTokens', null, '>', 1)
            ->whereDate('created_at', '<', now()->subDays(15))
            ->groupBy('student_user_id')->first();

        return $assistances;
    }
}
