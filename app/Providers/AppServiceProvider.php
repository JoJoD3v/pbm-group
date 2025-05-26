<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use App\Helpers\DateHelper;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        
        // Registra direttive Blade personalizzate per le date
        Blade::directive('formatDate', function ($expression) {
            return "<?php echo App\Helpers\DateHelper::formatDate($expression); ?>";
        });
        
        Blade::directive('formatDateTime', function ($expression) {
            return "<?php echo App\Helpers\DateHelper::formatDateTime($expression); ?>";
        });
        
        // Direttiva per generare un campo input date con data odierna come default
        Blade::directive('dateInput', function ($expression) {
            return "<?php 
                \$params = [{$expression}];
                \$name = \$params[0] ?? '';
                \$id = \$params[1] ?? \$name;
                \$required = isset(\$params[2]) && \$params[2] ? 'required' : '';
                \$class = \$params[3] ?? 'form-control';
                \$value = \$params[4] ?? '';
                if (empty(\$value)) {
                    \$value = date('Y-m-d');
                }
                echo '<div class=\"italian-date-input\">
                    <input type=\"date\" name=\"'.\$name.'\" id=\"'.\$id.'\" class=\"'.\$class.'\" value=\"'.\$value.'\" '.\$required.' data-default-today=\"true\">
                </div>';
            ?>";
        });
    }
}
