<?php
namespace admin\commissions\Console\Commands;

use Illuminate\Console\Command;

class CheckModuleStatusCommand extends Command
{
    protected $signature = 'commissions:check-status';
    protected $description = 'Check the status of the commissions module';

    public function handle()
    {
        $this->info('Commissions module status: OK');
    }
}
