<?php
namespace admin\commissions;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CommissionServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Load routes, views, migrations from the package  
        $this->loadViewsFrom([
            base_path('Modules/Commissions/resources/views'), // Published module views first
            resource_path('views/admin/commission'), // Published views second
            __DIR__ . '/../resources/views'      // Package views as fallback
        ], 'commission');

        $this->mergeConfigFrom(__DIR__.'/../config/commission.php', 'commission.constants');
        
        // Also register module views with a specific namespace for explicit usage
        if (is_dir(base_path('Modules/Commissions/resources/views'))) {
            $this->loadViewsFrom(base_path('Modules/Commissions/resources/views'), 'commissions-module');
        }
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        // Also load migrations from published module if they exist
        if (is_dir(base_path('Modules/Commissions/database/migrations'))) {
            $this->loadMigrationsFrom(base_path('Modules/Commissions/database/migrations'));
        }

        // Only publish automatically during package installation, not on every request
        // Use 'php artisan commissions:publish' command for manual publishing
        // $this->publishWithNamespaceTransformation();
        
        // Standard publishing for non-PHP files
        $this->publishes([
            __DIR__ . '/../database/migrations' => base_path('Modules/Commissions/database/migrations'),
            __DIR__ . '/../resources/views' => base_path('Modules/Commissions/resources/views/'),
        ], 'commission');
       
        $this->registerAdminRoutes();

    }

    protected function registerAdminRoutes()
    {
        if (!Schema::hasTable('admins')) {
            return; // Avoid errors before migration
        }

        $admin = DB::table('admins')
            ->orderBy('created_at', 'asc')
            ->first();
            
        $slug = $admin->website_slug ?? 'admin';

        Route::middleware('web')
            ->prefix("{$slug}/admin") // dynamic prefix
            ->group(function () {
                $this->loadRoutesFrom(__DIR__.'/routes/web.php');
            });
    }

    public function register()
    {
        // Register the publish command
        if ($this->app->runningInConsole()) {
            $this->commands([
                \admin\commissions\Console\Commands\PublishCommissionsModuleCommand::class,
                \admin\commissions\Console\Commands\CheckModuleStatusCommand::class,
                \admin\commissions\Console\Commands\DebugCommissionsCommand::class,
            ]);
        }
    }

    /**
     * Publish files with namespace transformation
     */
    protected function publishWithNamespaceTransformation()
    {
        // Define the files that need namespace transformation
        $filesWithNamespaces = [
            // Controllers
            __DIR__ . '/../src/Controllers/CommissionManagerController.php' => base_path('Modules/Commissions/app/Http/Controllers/Admin/CommissionManagerController.php'),
            
            // Models
            __DIR__ . '/../src/Models/Commission.php' => base_path('Modules/Commissions/app/Models/Commission.php'),
            
            // Requests
            __DIR__ . '/../src/Requests/CommissionCreateRequest.php' => base_path('Modules/Commissions/app/Http/Requests/CommissionCreateRequest.php'),
            __DIR__ . '/../src/Requests/CommissionUpdateRequest.php' => base_path('Modules/Commissions/app/Http/Requests/CommissionUpdateRequest.php'),
            
            // Routes
            __DIR__ . '/routes/web.php' => base_path('Modules/Commissions/routes/web.php'),
        ];

        foreach ($filesWithNamespaces as $source => $destination) {
            if (File::exists($source)) {
                // Create destination directory if it doesn't exist
                File::ensureDirectoryExists(dirname($destination));
                
                // Read the source file
                $content = File::get($source);
                
                // Transform namespaces based on file type
                $content = $this->transformNamespaces($content, $source);
                
                // Write the transformed content to destination
                File::put($destination, $content);
            }
        }
    }

    /**
     * Transform namespaces in PHP files
     */
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
            $content = $this->transformControllerNamespaces($content);
        } elseif (str_contains($sourceFile, 'Models')) {
            $content = $this->transformModelNamespaces($content);
        } elseif (str_contains($sourceFile, 'Requests')) {
            $content = $this->transformRequestNamespaces($content);
        } elseif (str_contains($sourceFile, 'routes')) {
            $content = $this->transformRouteNamespaces($content);
        }

        return $content;
    }

    /**
     * Transform controller-specific namespaces
     */
    protected function transformControllerNamespaces($content)
    {
        // Update use statements for models and requests
        $content = str_replace(
            'use admin\\commissions\\Models\\Commission;',
            'use Modules\\Commissions\\app\\Models\\Commission;',
            $content
        );
        
        $content = str_replace(
            'use admin\\commissions\\Requests\\CommissionCreateRequest;',
            'use Modules\\Commissions\\app\\Http\\Requests\\CommissionCreateRequest;',
            $content
        );
        
        $content = str_replace(
            'use admin\\commissions\\Requests\\CommissionUpdateRequest;',
            'use Modules\\Commissions\\app\\Http\\Requests\\CommissionUpdateRequest;',
            $content
        );

        return $content;
    }

    /**
     * Transform model-specific namespaces
     */
    protected function transformModelNamespaces($content)
    {
        // Any model-specific transformations
        return $content;
    }

    /**
     * Transform request-specific namespaces
     */
    protected function transformRequestNamespaces($content)
    {
        // Any request-specific transformations
        return $content;
    }

    /**
     * Transform route-specific namespaces
     */
    protected function transformRouteNamespaces($content)
    {
        // Update controller references in routes
        $content = str_replace(
            'admin\\commissions\\Controllers\\CommissionManagerController',
            'Modules\\Commissions\\app\\Http\\Controllers\\Admin\\CommissionManagerController',
            $content
        );

        return $content;
    }
}
