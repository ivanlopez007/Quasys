@php
    // Clases completas (Tailwind no detecta clases armadas dinámicamente)
    $alertas = [
        'success' => ['border-emerald-200 bg-emerald-50 text-emerald-800', 'fa-circle-check'],
        'error'   => ['border-rose-200 bg-rose-50 text-rose-800', 'fa-circle-exclamation'],
        'warning' => ['border-amber-200 bg-amber-50 text-amber-800', 'fa-triangle-exclamation'],
    ];
@endphp

@foreach ($alertas as $clave => [$clases, $icono])
    @if (session($clave))
        <div class="flex items-start gap-3 rounded-xl border px-4 py-3 text-sm shadow-sm {{ $clases }}">
            <i class="fa-solid {{ $icono }} mt-0.5"></i>
            <p class="font-semibold">{{ session($clave) }}</p>
        </div>
    @endif
@endforeach

@if ($errors->any())
    <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800 shadow-sm">
        <p class="font-bold mb-1"><i class="fa-solid fa-circle-exclamation mr-1"></i> Revisa los siguientes campos:</p>
        <ul class="list-disc list-inside text-xs space-y-0.5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
