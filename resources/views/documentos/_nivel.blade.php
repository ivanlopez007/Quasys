{{-- Nivel y subnivel de un documento o solicitud. Uso: @include('documentos._nivel', ['item' => $doc]) --}}
@if ($item->nivel)
    <p class="font-semibold text-slate-700 whitespace-nowrap">
        <span class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1 mr-1 rounded-md bg-slate-900 text-white text-[10px] font-black">{{ $item->nivel->nivel }}</span>{{ $item->nivel->nombre }}
    </p>
    @if ($item->subnivel)
        <p class="text-[11px] text-slate-400 mt-0.5"><i class="fas fa-turn-up fa-rotate-90 text-[9px] mr-1"></i>{{ $item->subnivel->nombre }}</p>
    @endif
@else
    <span class="text-slate-300">—</span>
@endif
