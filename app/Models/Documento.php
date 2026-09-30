<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\CambioDocumento;
use App\Models\Nivel;
use App\Models\SubNivel;
use App\Models\Area;
use App\Models\Localidad;
use App\Models\LugarRetencion;
use App\Models\PeriodoRetencion;
use App\Models\DisposicionFinal;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Documento extends Model
{
    protected $table = 'documentos';

    protected $fillable = [
        'codigo_documento',
        'nombre_documento',
        'version',
        'url_documento',
        'nivel_id',
        'subnivel_id',
        'localidad_id',
        'area_id',
        'lugar_retencion_id',
        'periodo_retencion_id',
        'disposicion_final_id',
        'usuario_id',
        'aprobar_id',
        'cambio_documento_id',
        'vigente',
        'fecha_publicacion',
    ];

    protected $casts = [
        'vigente' => 'boolean',
        'fecha_publicacion' => 'datetime',
    ];

    // Obtener todas las versiones del mismo documento por su código
    public function historialVersiones(): HasMany
    {
        return $this->hasMany(Documento::class, 'codigo_documento', 'codigo_documento')
            ->orderBy('version', 'desc');
    }

    public function autor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function aprobador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aprobar_id');
    }

    public function cambioOrigen(): BelongsTo
    {
        return $this->belongsTo(CambioDocumento::class, 'cambio_documento_id');
    }

    public function nivel(): BelongsTo
    {
        return $this->belongsTo(Nivel::class, 'nivel_id');
    }

    public function subnivel(): BelongsTo
    {
        return $this->belongsTo(SubNivel::class, 'subnivel_id');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function localidad(): BelongsTo
    {
        return $this->belongsTo(Localidad::class, 'localidad_id');
    }

    public function lugarRetencion(): BelongsTo
    {
        return $this->belongsTo(LugarRetencion::class, 'lugar_retencion_id');
    }

    public function periodoRetencion(): BelongsTo
    {
        return $this->belongsTo(PeriodoRetencion::class, 'periodo_retencion_id');
    }

    public function disposicionFinal(): BelongsTo
    {
        return $this->belongsTo(DisposicionFinal::class, 'disposicion_final_id');
    }

    public function cambios(): HasMany
    {
        return $this->hasMany(CambioDocumento::class, 'documento_id');
    }
}
