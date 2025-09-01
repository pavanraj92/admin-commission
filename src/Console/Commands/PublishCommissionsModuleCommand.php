<?php
namespace admin\commissions\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PublishCommissionsModuleCommand extends Command
{
    protected $signature = 'commissions:publish {--force : Force overwrite existing files}';
    protected $description = 'Publish the commissions module files to the main app';

     public function handle()
    {
        $this->info('Publishing Commissions module files...');

        // Check if module directory exists
        $moduleDir = base_path('Modules/Commissions');
        if (!File::exists($moduleDir)) {
            File::makeDirectory($moduleDir, 0755, true);
        }

        // Publish with namespace transformation
        $this->publishWithNamespaceTransformation();
        
        // Publish other files
        $this->call('vendor:publish', [
            '--tag' => 'commission',
            '--force' => $this->option('force')
        ]);

        // Update composer autoload
        $this->updateComposerAutoload();

        $this->info('Commissions module published successfully!');
        $this->info('Please run: composer dump-autoload');
    }

    protected function publishWithNamespaceTransformation()
    {
        $basePath = dirname(dirname(__DIR__)); // Go up to packages/admin/commissions/src
        
        $filesWithNamespaces = [
            // Controllers
            $basePath . '/Controllers/CommissionManagerController.php' => base_path('Modules/Commissions/app/Http/Controllers/Admin/CommissionManagerController.php'),
            
            // Models
            $basePath . '/Models/Commission.php' => base_path('Modules/Commissions/app/Models/Commission.php'),
            
            // Requests
            $basePath . '/Requests/CommissionCreateRequest.php' => base_path('Modules/Commissions/app/Http/Requests/CommissionCreateRequest.php'),
            $basePath . '/Requests/CommissionUpdateRequest.php' => base_path('Modules/Commissions/app/Http/Requests/CommissionUpdateRequest.php'),
            
            // Routes
            $basePath . '/routes/web.php' => base_path('Modules/Commissions/routes/web.php'),
        ];

        foreach ($filesWithNamespaces as $source => $destination) {
            if (File::exists($source)) {
                File::ensureDirectoryExists(dirname($destination));
                
                $content = File::get($source);
                $content = $this->transformNamespaces($content, $source);
                
                File::put($destination, $content);
                $this->info("Published: " . basename($destination));
            } else {
                $this->warn("Source file not found: " . $source);
            }
        }
    }

    protected function transformNamespaces($content, $sourceFile)
    {
        // Define namespace mappings
        $namespaceTransforms = [
            // Main namespace transformations
            'namespace admin\\commissions\\Controllers;' => 'namespace Modules\\Commissions\\app\\Http\\Controllers\\Admin;',
            'namespace admin\\commissions\\Models;' => 'namespace Modules\\Commissions\\app\\Models;',
            'namespace admin\\commissions\\Requests;' => 'namespace Modules\\Commissions\\app\\Http\\Requests;',
            
            // Use statements transformations
            'use admin\\commissions\\Controllers\\' => 'use Modules\\Commissions\\app\\Http\\Controllers\\Admin\\',
            'use admin\\commissions\\Models\\' => 'use Modules\\Commissions\\app\\Models\\',
            'use admin\\commissions\\Requests\\' => 'use Modules\\Commissions\\app\\Http\\Requests\\',
            
            // Class references in routes
            'admin\\commissions\\Controllers\\CommissionManagerController' => 'Modules\\Commissions\\app\\Http\\Controllers\\Admin\\CommissionManagerController',
        ];

        // Apply transformations
        foreach ($namespaceTransforms as $search => $replace) {
            $content = str_replace($search, $replace, $content);
        }

        // Handle specific file types
        if (str_contains($sourceFile, 'Controllers')) {
            $content = str_replace('use admin\\commissions\\Models\\Commission;', 'use Modules\\Commissions\\app\\Models\\Commission;', $content);
            $content = str_replace('use admin\\commissions\\Requests\\CommissionCreateRequest;', 'use Modules\\Commissions\\app\\Http\\Requests\\CommissionCreateRequest;', $content);
            $content = str_replace('use admin\\commissions\\Requests\\CommissionUpdateRequest;', 'use Modules\\Commissions\\app\\Http\\Requests\\CommissionUpdateRequest;', $content);
        }

        return $content;
    }

    protected function updateComposerAutoload()
    {
        $composerFile = base_path('composer.json');
        $composer = json_decode(File::get($composerFile), true);

        // Add module namespace to autoload
        if (!isset($composer['autoload']['psr-4']['Modules\\Commissions\\'])) {
            $composer['autoload']['psr-4']['Modules\\Commissions\\'] = 'Modules/Commissions/app/';
            
            File::put($composerFile, json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            $this->info('Updated composer.json autoload');
        }
    }
}
