<?php

namespace App\Providers;

use App\Models\Menu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View; 

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
        // Comparte $menusLayout únicamente con las vistas de layout (o usas '*' para todas)
        View::composer(['layout.layout', 'layout.*'], function ($view) {
            if (Auth::check()) {
                $user = Auth::user();
                $rolId = $user->rol_id ?? $user->rol?->id;

                // Cargar menús que tengan al menos un submenú asignado al rol del usuario
                $menusLayout = Menu::where('activo', true)
                    ->whereHas('subMenus', function ($query) use ($rolId) {
                        $query->where('activo', true)
                            ->whereHas('accesosAsignados', function ($q) use ($rolId) {
                                $q->where('rol_id', $rolId);
                            });
                    })
                    ->with(['subMenus' => function ($query) use ($rolId) {
                        $query->where('activo', true)
                            ->whereHas('accesosAsignados', function ($q) use ($rolId) {
                                $q->where('rol_id', $rolId);
                            })
                            ->orderBy('orden', 'asc');
                    }])
                    ->orderBy('orden', 'asc')
                    ->get();

                $view->with('menusLayout', $menusLayout);
            }
        });
    }
}
