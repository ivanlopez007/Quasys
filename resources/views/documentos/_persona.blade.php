{{-- Nombre completo de un usuario (informacion_usuarios) o su correo como respaldo. Uso: @include('documentos._persona', ['u' => $usuario]) --}}
@if ($u)
    {{ $u->informacion ? trim($u->informacion->nombre . ' ' . $u->informacion->apellidos) : $u->email }}
@else
    <span class="text-slate-300">—</span>
@endif
