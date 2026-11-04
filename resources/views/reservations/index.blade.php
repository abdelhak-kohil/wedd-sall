@extends('layouts.app')
@section('title', 'Réservations')
@section('page_title', app()->getLocale() === 'ar' ? 'سجل الحجوزات' : 'Toutes les Réservations')

@section('content')
<div class="space-y-5">
    <!-- Toolbar: Filters + Action -->
    <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-4 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <form method="GET" action="{{ route('reservations.index') }}" class="flex flex-wrap items-center gap-2 flex-1">
            <input type="text" name="q" value="{{ request('q') }}"
                placeholder="{{ app()->getLocale() === 'ar' ? 'بحث بالرقم، الزبون، العرسان...' : 'Rechercher N°, nom, tél, mariés...' }}"
                class="px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 flex-1 min-w-[200px]">

            <select name="status" onchange="this.form.submit()" class="px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Tous les statuts</option>
                <option value="TEMPORARY_HOLD" {{ request('status') === 'TEMPORARY_HOLD' ? 'selected' : '' }}>Bloqué temporaire</option>
                <option value="CONFIRMED" {{ request('status') === 'CONFIRMED' ? 'selected' : '' }}>Confirmé</option>
                <option value="COMPLETED" {{ request('status') === 'COMPLETED' ? 'selected' : '' }}>Terminé</option>
                <option value="CANCELLED" {{ request('status') === 'CANCELLED' ? 'selected' : '' }}>Annulé</option>
            </select>

            <button type="submit" class="px-3 py-2 bg-slate-700 hover:bg-slate-600 rounded-xl text-white text-xs font-medium transition">
                Filtrer
            </button>
        </form>

        <a href="{{ route('reservations.create') }}"
            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-semibold flex items-center justify-center gap-1.5 transition shadow-lg shrink-0">
            <span>⚡ {{ app()->getLocale() === 'ar' ? 'حجز سريع (<60 ث)' : 'Réservation Rapide (< 60s)' }}</span>
        </a>
    </div>

    <!-- Table -->
    <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl overflow-hidden shadow-xl">
        <table class="w-full text-xs">
            <thead class="text-[11px] text-slate-400 uppercase tracking-wider bg-slate-900 border-b border-slate-800">
                <tr>
                    <th class="py-3 px-4 text-left">N° Réservation</th>
                    <th class="py-3 px-4 text-left">Date Événement</th>
                    <th class="py-3 px-4 text-left">Client & Fête</th>
                    <th class="py-3 px-4 text-left hidden md:table-cell">Espace</th>
                    <th class="py-3 px-4 text-left hidden lg:table-cell">Créneau</th>
                    <th class="py-3 px-4 text-left">Statut</th>
                    <th class="py-3 px-4 text-right">Total</th>
                    <th class="py-3 px-4 text-right">Reste</th>
                    <th class="py-3 px-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60">
                @forelse($reservations as $res)
                    <tr class="hover:bg-slate-800/30 transition">
                        <td class="py-3 px-4 font-mono font-bold text-white">
                            {{ $res->reservation_number }}
                        </td>
                        <td class="py-3 px-4 text-slate-300">
                            {{ \Carbon\Carbon::parse($res->event_date)->format('d/m/Y') }}
                        </td>
                        <td class="py-3 px-4">
                            <div class="font-semibold text-white">{{ $res->customer_name }}</div>
                            <div class="text-[11px] text-slate-400">
                                @if($res->groom_name || $res->bride_name)
                                    💍 {{ $res->groom_name }} & {{ $res->bride_name }} •
                                @endif
                                <span class="font-mono">{{ $res->customer_phone }}</span>
                            </div>
                        </td>
                        <td class="py-3 px-4 hidden md:table-cell text-slate-300">
                            {{ $res->space?->name ?? '—' }}
                        </td>
                        <td class="py-3 px-4 hidden lg:table-cell text-slate-400 font-mono text-[11px]">
                            {{ substr($res->start_time, 0, 5) }} - {{ substr($res->end_time, 0, 5) }}
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $res->statusBadge()['class'] }}">
                                {{ $res->statusBadge()['label'] }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right font-mono font-bold text-white">
                            {{ number_format($res->total_price, 0, ',', ' ') }} DA
                        </td>
                        <td class="py-3 px-4 text-right font-mono {{ $res->remaining_balance > 0 ? 'text-amber-400 font-bold' : 'text-emerald-400' }}">
                            {{ number_format($res->remaining_balance, 0, ',', ' ') }} DA
                        </td>
                        <td class="py-3 px-4 text-right">
                            <a href="{{ route('reservations.show', $res) }}"
                                class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-white transition">
                                Dossier 360° →
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="py-12 text-center text-slate-500">
                            Aucune réservation trouvée.
                            <a href="{{ route('reservations.create') }}" class="text-indigo-400 hover:underline ml-1">Faire une réservation rapide</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($reservations->hasPages())
            <div class="px-4 py-3 border-t border-slate-800">
                {{ $reservations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
