@extends('layouts.app')
@section('title', 'Dépenses')
@section('page_title', app()->getLocale() === 'ar' ? 'سجل المصاريف' : 'Journal des Dépenses')

@section('content')
<div class="space-y-5">

    {{-- ── Stats bar ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-4">
            <p class="text-xs text-slate-500 mb-1">Ce mois-ci</p>
            <p class="text-2xl font-bold text-rose-400">{{ number_format($totalMonth, 2, ',', ' ') }} <span class="text-sm font-medium text-slate-500">DA</span></p>
        </div>
        <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-4 flex items-center gap-3">
            <a href="{{ route('expenses.create') }}"
               class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 bg-rose-600/80 hover:bg-rose-600 text-white text-sm font-medium rounded-xl transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nouvelle dépense
            </a>
        </div>
        <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-4 flex items-center gap-3">
            <a href="{{ route('expenses.report') }}"
               class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600/80 hover:bg-indigo-600 text-white text-sm font-medium rounded-xl transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Rapport Rentabilité
            </a>
        </div>
    </div>

    {{-- ── Filters ── --}}
    <form method="GET" action="{{ route('expenses.index') }}" class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-4">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Libellé, fournisseur…"
                   class="col-span-2 bg-slate-800 border border-slate-700 text-slate-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-rose-500 focus:outline-none">
            <select name="category" class="bg-slate-800 border border-slate-700 text-slate-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-rose-500 focus:outline-none">
                <option value="">Toutes catégories</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>{{ $cat->name_fr }}</option>
                @endforeach
            </select>
            <select name="method" class="bg-slate-800 border border-slate-700 text-slate-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-rose-500 focus:outline-none">
                <option value="">Tout règlement</option>
                <option value="CASH" @selected(request('method') === 'CASH')>Espèces</option>
                <option value="CHEQUE" @selected(request('method') === 'CHEQUE')>Chèque</option>
                <option value="TRANSFER" @selected(request('method') === 'TRANSFER')>Virement</option>
                <option value="OTHER" @selected(request('method') === 'OTHER')>Autre</option>
            </select>
            <input type="month" name="month" value="{{ request('month') }}"
                   class="bg-slate-800 border border-slate-700 text-slate-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-rose-500 focus:outline-none">
            <button type="submit" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white text-sm rounded-xl transition">Filtrer</button>
            @if(request()->anyFilled(['q','category','method','month']))
            <a href="{{ route('expenses.index') }}" class="px-4 py-2 border border-slate-700 text-slate-400 text-sm rounded-xl hover:bg-slate-800 transition text-center">Réinitialiser</a>
            @endif
        </div>
    </form>

    {{-- ── Table ── --}}
    <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl overflow-hidden">
        @if($expenses->isEmpty())
        <div class="py-16 text-center">
            <svg class="w-12 h-12 mx-auto text-slate-700 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
            </svg>
            <p class="text-slate-500 text-sm">Aucune dépense enregistrée.</p>
            <a href="{{ route('expenses.create') }}" class="mt-3 inline-block text-rose-400 text-sm hover:underline">Saisir la première dépense →</a>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-800/50">
                    <tr class="text-xs text-slate-400 uppercase tracking-wide">
                        <th class="px-4 py-3 text-left">Date</th>
                        <th class="px-4 py-3 text-left">Libellé</th>
                        <th class="px-4 py-3 text-left">Catégorie</th>
                        <th class="px-4 py-3 text-left">Fournisseur</th>
                        <th class="px-4 py-3 text-center">Règlement</th>
                        <th class="px-4 py-3 text-right">Montant (DA)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/40">
                    @foreach($expenses as $exp)
                    <tr class="hover:bg-slate-800/20 transition group">
                        <td class="px-4 py-3 text-slate-400 whitespace-nowrap">{{ $exp->expense_date->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-slate-200 truncate max-w-xs">{{ $exp->description }}</p>
                            @if($exp->reference)
                            <p class="text-xs text-slate-500 font-mono">Réf: {{ $exp->reference }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($exp->category)
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-slate-800 text-slate-300">
                                <span class="w-2 h-2 rounded-full shrink-0" style="background-color:{{ $exp->category->color }}"></span>
                                {{ $exp->category->name_fr }}
                            </span>
                            @else
                            <span class="text-slate-600 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-400 text-xs">{{ $exp->vendor ?: '—' }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-xs px-2 py-0.5 rounded bg-slate-800 text-slate-300">
                                {{ ['CASH'=>'Espèces','CHEQUE'=>'Chèque','TRANSFER'=>'Virement','OTHER'=>'Autre'][$exp->payment_method] ?? $exp->payment_method }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-semibold text-rose-400">
                            {{ number_format($exp->amount, 2, ',', ' ') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-800/30 border-t border-slate-700/60">
                    <tr>
                        <td colspan="5" class="px-4 py-2.5 text-xs font-medium text-slate-400">Total affiché ({{ $expenses->total() }} dépenses)</td>
                        <td class="px-4 py-2.5 text-right font-bold text-rose-400">
                            {{ number_format($expenses->sum('amount'), 2, ',', ' ') }} DA
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @if($expenses->hasPages())
        <div class="px-5 py-3 border-t border-slate-800/60">{{ $expenses->links() }}</div>
        @endif
        @endif
    </div>
</div>

@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
     class="fixed bottom-6 right-6 z-50 bg-emerald-600 text-white text-sm px-5 py-3 rounded-xl shadow-lg">
    {{ session('success') }}
</div>
@endif
@endsection
