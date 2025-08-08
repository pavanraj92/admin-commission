<?php
namespace admin\commissions\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PublishCommissionsModuleCommand extends Command
{
    protected $signature = 'commissions:publish';
    protected $description = 'Publish the commissions module files to the main app';

    public function handle()
    {
        $this->info('Publishing commissions module files...');
        // Add logic to copy migrations/views/config to Modules/Commissions
        // Example:
        // File::copyDirectory(__DIR__.'/../../../resources/views', base_path('Modules/Commissions/resources/views'));
        $this->info('Commissions module published successfully.');
    }
}
