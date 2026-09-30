<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Documento;
use App\Models\User;
use App\Models\Nivel;
use App\Models\SubNivel;
use App\Models\Area;
use App\Models\Localidad;
use App\Models\LugarRetencion;
use App\Models\PeriodoRetencion;
use App\Models\DisposicionFinal;
use App\Models\EstadoSolicitud;
use App\Models\TipoSolicitud;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CambioDocumento extends Model
{
    protected $table = 'cambio_documentos';

    protected $fillable = [
        'documento_id',
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
        'tipo_solicitud_id',
        'solicitante_id',
        'aprobar_id',
        'estado_id',
        'motivo_cambio',
        'descripcion_cambios',
        'comentario_aprobador',
        'fecha_solicitud',
        'fecha_aprobacion',
    ];

    protected $casts = [
        'fecha_solicitud' => 'datetime',
        'fecha_aprobacion' => 'datetime',
    ];

    public function documento(): BelongsTo
    {
        return $this->belongsTo(Documento::class, 'documento_id');
    }

    public function solicitante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'solicitante_id');
    }

    public function aprobador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aprobar_id');
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(EstadoSolicitud::class, 'estado_id');
    }

    public function tipoSolicitud(): BelongsTo
    {
        return $this->belongsTo(TipoSolicitud::class, 'tipo_solicitud_id');
    }

    public function nivel(): BelongsTo
    {
        return $this->belongsTo(Nivel::class, 'nivel_id');
    }

    public function subnivel(): BelongsTo
    {
        return $this->belongsTo(SubNivel::class, 'subnivel_id');
    }

    public function localidad(): BelongsTo
    {
        return $this->belongsTo(Localidad::class, 'localidad_id');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_id');
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

    public function documentoResultante(): HasOne
    {
        return $this->hasOne(Documento::class, 'cambio_documento_id');
    }
}
