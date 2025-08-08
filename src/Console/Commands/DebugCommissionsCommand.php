<?php
namespace admin\commissions\Console\Commands;

use Illuminate\Console\Command;

class DebugCommissionsCommand extends Command
{
    protected $signature = 'commissions:debug';
    protected $description = 'Debug the commissions module';

    public function handle()
    {
        $this->info('Debugging commissions module...');
        // Add debug logic here
        $this->info('Debug complete.');
    }
}
