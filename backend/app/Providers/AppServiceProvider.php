<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Bootstrap any application services.
   *
   * @return void
   */
  public function boot()
  {
      Schema::defaultStringLength(191);
      Paginator::useBootstrap();
  }

  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
  }
}
