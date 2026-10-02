<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/**
 * Deja una empresa lista para usarse con los datos de config/aprovisionamiento.php y
 * config/documentos.php. Se ejecuta dentro del contexto del tenant y solo AGREGA lo que
 * falta, así que es seguro correrlo varias veces y sobre empresas que ya tienen datos.
 */
class PreparacionEmpresaService
{
    private array $agregado = [];

    /** @return array<string, int> lo agregado por tabla */
    public function preparar(): array
    {
        $this->agregado = [];

        DB::transaction(function () {
            $this->estadosYTipos();
            $this->catalogos();
            $this->planta();
            $subMenus = $this->menus();
            $this->roles($subMenus);
        });

        return array_filter($this->agregado);
    }

    /** URLs de sub menú configuradas que no tienen una ruta GET registrada. */
    public static function urlsSinRuta(): array
    {
        $rutas = collect(Route::getRoutes()->getRoutes())
            ->filter(fn ($r) => in_array('GET', $r->methods(), true))
            ->map(fn ($r) => trim($r->uri(), '/'))
            ->unique();

        return collect(config('aprovisionamiento.menus'))
            ->flatMap(fn ($menu) => array_column($menu[2], 1))
            ->reject(fn ($url) => $rutas->contains(trim($url, '/')))
            ->values()
            ->all();
    }

    private function estadosYTipos(): void
    {
        foreach (config('documentos.estados') as $estado) {
            $this->siFalta('estado_solicituds', ['estado_solicitud' => $estado], ['activo' => true]);
        }

        // Los tipos se comparan sin acentos ni mayúsculas, igual que CambioDocumento::tiposIds()
        $existentes = DB::table('tipo_solicituds')->pluck('tipo_solicitud')->map(fn ($t) => $this->normalizar($t));
        foreach (config('documentos.tipos') as $tipo) {
            if (!$existentes->contains($this->normalizar($tipo))) {
                $this->insertar('tipo_solicituds', ['tipo_solicitud' => $tipo, 'activo' => true]);
            }
        }

        foreach (config('aprovisionamiento.catalogos.periodos_retencion') as [$unidad, $tiempo]) {
            $this->siFalta('periodo_retencions', ['periodo_retencion' => $unidad, 'tiempo' => $tiempo], ['activo' => true]);
        }
    }

    /** Catálogos propios de cada empresa: solo si la tabla está vacía. */
    private function catalogos(): void
    {
        $c = config('aprovisionamiento.catalogos');

        if (!DB::table('nivels')->exists()) {
            foreach ($c['niveles'] as $numero => [$nombre, $subniveles]) {
                $nivelId = $this->insertar('nivels', ['nivel' => $numero, 'nombre' => $nombre, 'activo' => true]);
                foreach ($subniveles as $sub) {
                    $this->insertar('sub_nivels', ['nombre' => $sub, 'nivel_id' => $nivelId, 'activo' => true]);
                }
            }
        }

        foreach ([
            'areas' => ['area', $c['areas']],
            'localidads' => ['localidad', $c['localidades']],
            'lugar_retencions' => ['lugar_retencion', $c['lugares_retencion']],
            'disposicion_finals' => ['disposicion_final', $c['disposiciones_finales']],
        ] as $tabla => [$columna, $valores]) {
            if (!DB::table($tabla)->exists()) {
                foreach ($valores as $valor) {
                    $this->insertar($tabla, [$columna => $valor, 'activo' => true]);
                }
            }
        }
    }

    private function planta(): void
    {
        if (!DB::table('plantas')->whereNull('deleted_at')->exists()) {
            [$planta, $ubicacion] = config('aprovisionamiento.planta_inicial');
            $this->insertar('plantas', ['planta' => $planta, 'ubicacion' => $ubicacion]);
        }
    }

    /** @return array<string, int> url => id de cada sub menú configurado */
    private function menus(): array
    {
        $ids = [];

        foreach (config('aprovisionamiento.menus') as $i => [$menu, $icono, $subMenus]) {
            $menuId = null; // el menú solo se crea si hace falta para un sub menú nuevo

            foreach ($subMenus as $j => [$subMenu, $url, $iconoSub]) {
                // Se identifica por URL: si la empresa lo renombró o movió de menú, se respeta.
                if ($existente = DB::table('sub_menus')->where('url', $url)->value('id')) {
                    $ids[$url] = $existente;
                    continue;
                }

                $menuId ??= DB::table('menus')->where('menu', $menu)->value('id')
                    ?? $this->insertar('menus', ['menu' => $menu, 'icono' => $icono, 'orden' => $i + 1, 'activo' => true]);

                $ids[$url] = $this->insertar('sub_menus', [
                    'menu_id' => $menuId, 'sub_menu' => $subMenu, 'url' => $url,
                    'icono' => $iconoSub, 'orden' => $j + 1, 'activo' => true,
                ]);
            }
        }

        return $ids;
    }

    private function roles(array $subMenus): void
    {
        foreach (config('aprovisionamiento.roles') as $rol => $urls) {
            $rolId = DB::table('rols')->where('rol', $rol)->value('id');
            $esNuevo = !$rolId;
            $rolId ??= $this->insertar('rols', ['rol' => $rol, 'activo' => true]);

            // Los permisos de un rol que la empresa ya tenía no se tocan; solo el
            // administrador recibe siempre las pantallas nuevas.
            if (!$esNuevo && $rol !== self::rolAdministrador()) {
                continue;
            }

            $permitidos = $urls === '*' ? $subMenus : array_intersect_key($subMenus, array_flip($urls));

            foreach ($permitidos as $subMenuId) {
                $this->siFalta('acceso_asignados', ['rol_id' => $rolId, 'sub_menu_id' => $subMenuId]);
            }
        }
    }

    public static function rolAdministrador(): string
    {
        return array_key_first(config('aprovisionamiento.roles'));
    }

    private function siFalta(string $tabla, array $clave, array $extra = []): void
    {
        if (!DB::table($tabla)->where($clave)->exists()) {
            $this->insertar($tabla, $clave + $extra);
        }
    }

    private function insertar(string $tabla, array $valores): int
    {
        $this->agregado[$tabla] = ($this->agregado[$tabla] ?? 0) + 1;

        return DB::table($tabla)->insertGetId($valores + ['created_at' => now(), 'updated_at' => now()]);
    }

    private function normalizar(?string $texto): string
    {
        return Str::lower(Str::ascii(trim((string) $texto)));
    }
}
