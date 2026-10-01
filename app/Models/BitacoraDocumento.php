<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
 
class BitacoraDocumento extends Model
{
    protected $table = 'bitacora_documentos';
 
    protected $fillable = [
        'documento_id',
        'cambio_documento_id',
        'usuario_id',
        'accion',
        'detalle',
        'ip',
    ];
 
    protected $casts = [
        'detalle' => 'array',
    ];
 
    public static function registrar(
        string $accion,
        ?int $usuarioId,
        ?int $documentoId = null,
        ?int $cambioId = null,
        array $detalle = []
    ): self {
        return self::create([
            'accion' => $accion,
            'usuario_id' => $usuarioId,
            'documento_id' => $documentoId,
            'cambio_documento_id' => $cambioId,
            'detalle' => $detalle ?: null,
            'ip' => request()->ip(),
        ]);
    }
 
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id')->withTrashed();
    }
 
    public function documento(): BelongsTo
    {
        return $this->belongsTo(Documento::class, 'documento_id');
    }
}