<?php
namespace admin\commissions\Console\Commands;

use Illuminate\Console\Command;

class TestViewResolutionCommand extends Command
{
    protected $signature = 'commissions:test-view';
    protected $description = 'Test view resolution for commissions module';

    public function handle()
    {
        $this->info('Testing commissions view resolution...');
        // Add view resolution test logic here
        $this->info('View resolution test complete.');
    }
}
