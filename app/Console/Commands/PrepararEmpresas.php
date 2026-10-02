<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Services\PreparacionEmpresaService;
use Illuminate\Console\Command;

class PrepararEmpresas extends Command
{
    protected $signature = 'empresas:preparar {empresa?* : ID de la empresa (por defecto, todas)}';

    protected $description = 'Completa en cada empresa los menús, roles, accesos y catálogos de config/aprovisionamiento.php (solo agrega lo que falta)';

    public function handle(PreparacionEmpresaService $preparacion): int
    {
        if ($sinRuta = PreparacionEmpresaService::urlsSinRuta()) {
            $this->warn('Sub menús configurados sin ruta registrada: ' . implode(', ', $sinRuta));
        }

        $ids = $this->argument('empresa');
        $empresas = $ids ? Tenant::whereIn('id', $ids)->get() : Tenant::all();

        if ($ids && $empresas->count() !== count($ids)) {
            $this->error('No existe: ' . implode(', ', array_diff($ids, $empresas->pluck('id')->all())));
            return self::FAILURE;
        }

        $fallas = 0;
        foreach ($empresas as $empresa) {
            try {
                $agregado = $empresa->run(fn () => $preparacion->preparar());
                $detalle = $agregado
                    ? collect($agregado)->map(fn ($n, $tabla) => "{$tabla}: {$n}")->implode(', ')
                    : 'ya estaba completa';
                $this->line("<info>✔</info> {$empresa->id} — {$detalle}");
            } catch (\Throwable $e) {
                if (tenancy()->initialized) {
                    tenancy()->end();
                }
                $fallas++;
                $this->line("<error>✘</error> {$empresa->id} — {$e->getMessage()}");
            }
        }

        return $fallas ? self::FAILURE : self::SUCCESS;
    }
}
