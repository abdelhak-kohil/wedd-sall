@extends('layouts.app')
@section('title', 'Rapport Rentabilité ' . $year)
@section('page_title', 'Rapport Rentabilité & Dépenses')

@section('content')
<div class="space-y-6">

    {{-- ── Year selector ── --}}
    <div class="flex items-center gap-3 flex-wrap">
        @foreach($availableYears as $y)
        <a href="{{ route('expenses.report', ['year' => $y]) }}"
           class="px-4 py-1.5 rounded-xl text-sm font-medium transition
               {{ $y == $year ? 'bg-indigo-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white hover:bg-slate-700' }}">
            {{ $y }}
        </a>
        @endforeach
        <a href="{{ route('expenses.create') }}"
           class="ml-auto px-4 py-1.5 bg-rose-600/80 hover:bg-rose-600 text-white text-sm font-medium rounded-xl transition">
            + Nouvelle dépense
        </a>
    </div>

    {{-- ── KPI Cards ── --}}
    @php
        $marginPct = $yearTotals['net_revenue'] > 0
            ? round($yearTotals['profit'] / $yearTotals['net_revenue'] * 100, 1)
            : 0;
    @endphp
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-slate-900/80 border border-emerald-900/40 rounded-2xl p-4">
            <p class="text-xs text-slate-500 mb-1">Chiffre d'affaires net</p>
            <p class="text-xl font-bold text-emerald-400">{{ number_format($yearTotals['net_revenue'], 0, ',', ' ') }} <span class="text-xs font-normal text-slate-500">DA</span></p>
        </div>
        <div class="bg-slate-900/80 border border-rose-900/40 rounded-2xl p-4">
            <p class="text-xs text-slate-500 mb-1">Dépenses totales</p>
            <p class="text-xl font-bold text-rose-400">{{ number_format($yearTotals['expenses'], 0, ',', ' ') }} <span class="text-xs font-normal text-slate-500">DA</span></p>
        </div>
        <div class="bg-slate-900/80 border border-{{ $yearTotals['profit'] >= 0 ? 'indigo' : 'rose' }}-900/40 rounded-2xl p-4">
            <p class="text-xs text-slate-500 mb-1">Bénéfice net</p>
            <p class="text-xl font-bold text-{{ $yearTotals['profit'] >= 0 ? 'indigo-400' : 'rose-500' }}">
                {{ $yearTotals['profit'] >= 0 ? '+' : '' }}{{ number_format($yearTotals['profit'], 0, ',', ' ') }} <span class="text-xs font-normal text-slate-500">DA</span>
            </p>
        </div>
        <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-4">
            <p class="text-xs text-slate-500 mb-1">Marge nette</p>
            <p class="text-xl font-bold {{ $marginPct >= 0 ? 'text-indigo-400' : 'text-rose-400' }}">{{ $marginPct }}%</p>
            <p class="text-xs text-slate-600 mt-0.5">{{ $yearTotals['reservations'] }} événements</p>
        </div>
    </div>

    {{-- ── Monthly breakdown table ── --}}
    <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-800/60">
            <h2 class="text-sm font-semibold text-slate-300">Tableau mensuel — {{ $year }}</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-800/50">
                    <tr class="text-xs text-slate-400 uppercase tracking-wide">
                        <th class="px-4 py-3 text-left">Mois</th>
                        <th class="px-4 py-3 text-right">Recettes brutes</th>
                        <th class="px-4 py-3 text-right">Remboursements</th>
                        <th class="px-4 py-3 text-right">CA net</th>
                        <th class="px-4 py-3 text-right">Dépenses</th>
                        <th class="px-4 py-3 text-right">Bénéfice</th>
                        <th class="px-4 py-3 text-center">Événements</th>
                        <th class="px-4 py-3 text-right w-32">Marge</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/40">
                    @php
                        $monthNames = ['Jan','Fév','Mar','Avr','Mai','Juin','Juil','Août','Sep','Oct','Nov','Déc'];
                    @endphp
                    @foreach($months as $m => $row)
                    @php
                        $marge = $row['net_revenue'] > 0 ? round($row['profit'] / $row['net_revenue'] * 100, 1) : 0;
                        $isCurrent = $m == now()->month && $year == now()->year;
                    @endphp
                    <tr class="hover:bg-slate-800/20 transition {{ $isCurrent ? 'bg-indigo-950/20' : '' }}">
                        <td class="px-4 py-3 font-medium text-slate-200">
                            {{ $monthNames[$m - 1] }}
                            @if($isCurrent)<span class="ml-1 text-[10px] bg-indigo-600/30 text-indigo-400 px-1.5 py-0.5 rounded">En cours</span>@endif
                        </td>
                        <td class="px-4 py-3 text-right text-emerald-400">
                            {{ $row['revenue'] > 0 ? number_format($row['revenue'], 0, ',', ' ') : '—' }}
                        </td>
                        <td class="px-4 py-3 text-right text-rose-400 text-xs">
                            {{ $row['refunds'] > 0 ? '-'.number_format($row['refunds'], 0, ',', ' ') : '—' }}
                        </td>
                        <td class="px-4 py-3 text-right font-medium text-slate-200">
                            {{ $row['net_revenue'] > 0 ? number_format($row['net_revenue'], 0, ',', ' ') : '—' }}
                        </td>
                        <td class="px-4 py-3 text-right text-rose-400">
                            {{ $row['expenses'] > 0 ? number_format($row['expenses'], 0, ',', ' ') : '—' }}
                        </td>
                        <td class="px-4 py-3 text-right font-semibold {{ $row['profit'] > 0 ? 'text-indigo-400' : ($row['profit'] < 0 ? 'text-rose-500' : 'text-slate-600') }}">
                            @if($row['profit'] != 0)
                                {{ $row['profit'] >= 0 ? '+' : '' }}{{ number_format($row['profit'], 0, ',', ' ') }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center text-slate-400">{{ $row['reservations'] ?: '—' }}</td>
                        <td class="px-4 py-3">
                            @if($row['net_revenue'] > 0)
                            <div class="flex items-center gap-2">
                                <div class="flex-1 h-1.5 bg-slate-800 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full {{ $marge >= 0 ? 'bg-indigo-500' : 'bg-rose-500' }}"
                                         style="width:{{ min(abs($marge), 100) }}%"></div>
                                </div>
                                <span class="text-xs font-medium {{ $marge >= 0 ? 'text-indigo-400' : 'text-rose-400' }} w-10 text-right">{{ $marge }}%</span>
                            </div>
                            @else
                            <span class="text-slate-700 text-xs">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-800/40 border-t border-slate-700/60 font-semibold">
                    @php $yMarge = $yearTotals['net_revenue'] > 0 ? round($yearTotals['profit'] / $yearTotals['net_revenue'] * 100, 1) : 0; @endphp
                    <tr class="text-sm">
                        <td class="px-4 py-3 text-white">TOTAL {{ $year }}</td>
                        <td class="px-4 py-3 text-right text-emerald-400">{{ number_format($yearTotals['revenue'], 0, ',', ' ') }}</td>
                        <td class="px-4 py-3 text-right text-rose-400 text-xs">{{ $yearTotals['refunds'] > 0 ? '-'.number_format($yearTotals['refunds'], 0, ',', ' ') : '—' }}</td>
                        <td class="px-4 py-3 text-right text-white">{{ number_format($yearTotals['net_revenue'], 0, ',', ' ') }}</td>
                        <td class="px-4 py-3 text-right text-rose-400">{{ number_format($yearTotals['expenses'], 0, ',', ' ') }}</td>
                        <td class="px-4 py-3 text-right {{ $yearTotals['profit'] >= 0 ? 'text-indigo-400' : 'text-rose-500' }}">
                            {{ $yearTotals['profit'] >= 0 ? '+' : '' }}{{ number_format($yearTotals['profit'], 0, ',', ' ') }}
                        </td>
                        <td class="px-4 py-3 text-center text-slate-300">{{ $yearTotals['reservations'] }}</td>
                        <td class="px-4 py-3 text-right {{ $yMarge >= 0 ? 'text-indigo-400' : 'text-rose-400' }}">{{ $yMarge }}%</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- ── Expenses by Category ── --}}
    @if($byCategory->isNotEmpty())
    <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-800/60">
            <h2 class="text-sm font-semibold text-slate-300">Répartition des dépenses par catégorie — {{ $year }}</h2>
        </div>
        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @php $totalExp = $byCategory->sum('total'); @endphp
            @foreach($byCategory->sortByDesc('total') as $catRow)
            @php
                $pct = $totalExp > 0 ? round($catRow->total / $totalExp * 100, 1) : 0;
                $color = $catRow->category->color ?? '#64748b';
            @endphp
            <div class="bg-slate-800/50 rounded-xl p-3">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color:{{ $color }}"></span>
                        <span class="text-xs font-medium text-slate-300">{{ $catRow->category->name_fr ?? 'Sans catégorie' }}</span>
                    </div>
                    <span class="text-xs text-slate-500">{{ $pct }}%</span>
                </div>
                <div class="h-1.5 bg-slate-700 rounded-full overflow-hidden mb-2">
                    <div class="h-full rounded-full" style="width:{{ $pct }}%; background-color:{{ $color }}"></div>
                </div>
                <p class="text-sm font-semibold text-rose-400 text-right">{{ number_format($catRow->total, 0, ',', ' ') }} DA</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
