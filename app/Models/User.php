<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;


class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'email',
        'password',
        'rol_id',
        'jefe_inmediato_id',
        'localidad_id',
        'planta_id',
        'area_id',
    ];

    protected $hidden = [
        'password',
    ];

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    public function jefeInmediato(): BelongsTo
    {
        return $this->belongsTo(User::class, 'jefe_inmediato_id');
    }

    public function subordinados(): HasMany
    {
        return $this->hasMany(User::class, 'jefe_inmediato_id');
    }

    public function localidad(): BelongsTo
    {
        return $this->belongsTo(Localidad::class, 'localidad_id');
    }

    public function planta(): BelongsTo
    {
        return $this->belongsTo(Planta::class, 'planta_id');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function informacion(): HasOne
    {
        return $this->hasOne(InformacionUsuario::class, 'usuario_id');
    }

    public function preferencia(): HasOne
    {
        return $this->hasOne(PreferenciaUsuario::class, 'usuario_id');
    }

    public function cursos(): BelongsToMany
    {
        return $this->belongsToMany(Curso::class, 'curso_asignados', 'usuario_id', 'curso_id')
                    ->withPivot('completado', 'calificacion', 'fecha_completado')
                    ->withTimestamps();
    }

    public function cursosAsignados(): HasMany
    {
        return $this->hasMany(CursoAsignado::class, 'usuario_id');
    }

    public function solicitudesDeCambio(): HasMany
    {
        return $this->hasMany(CambioDocumento::class, 'solicitante_id');
    }

    public function documentosElaborados(): HasMany
    {
        return $this->hasMany(Documento::class, 'usuario_id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
