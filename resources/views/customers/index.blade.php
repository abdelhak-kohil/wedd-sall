@extends('layouts.app')
@section('title', 'Clients & Prospects')
@section('page_title', app()->getLocale() === 'ar' ? 'إدارة الزبائن والمقبلين' : 'Gestion des Clients & Prospects')

@section('content')
<div class="space-y-5">

    <!-- Toolbar: Search + New Customer -->
    <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <form method="GET" action="{{ route('customers.index') }}" class="flex items-center gap-2 flex-1">
            <div class="relative flex-1 max-w-sm">
                <span class="absolute left-3 rtl:right-3 rtl:left-auto top-1/2 -translate-y-1/2 text-slate-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0" />
                    </svg>
                </span>
                <input type="text" name="q" value="{{ request('q') }}"
                    placeholder="{{ app()->getLocale() === 'ar' ? 'بحث بالاسم أو الهاتف...' : 'Recherche nom, téléphone, mariés...' }}"
                    class="w-full pl-9 rtl:pr-9 rtl:pl-3 pr-3 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <select name="wilaya_id" onchange="this.form.submit()" class="px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="">Toutes les wilayas</option>
                @foreach($wilayas as $w)
                    <option value="{{ $w->id }}" {{ request('wilaya_id') == $w->id ? 'selected' : '' }}>
                        {{ $w->name_fr }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="px-3.5 py-2.5 rounded-xl bg-slate-700 hover:bg-slate-600 text-white text-xs font-medium transition">
                Filtrer
            </button>
        </form>

        <a href="{{ route('customers.create') }}"
            class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold transition shadow-md flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>+ {{ app()->getLocale() === 'ar' ? 'زبون جديد' : 'Nouveau Client' }}</span>
        </a>
    </div>

    <!-- Customer Table -->
    <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl overflow-hidden shadow-xl">
        <table class="w-full text-xs">
            <thead class="text-[11px] text-slate-400 uppercase tracking-wider bg-slate-900 border-b border-slate-800">
                <tr>
                    <th class="py-3.5 px-4 text-left rtl:text-right">Client</th>
                    <th class="py-3.5 px-4 text-left rtl:text-right hidden md:table-cell">Téléphone</th>
                    <th class="py-3.5 px-4 text-left rtl:text-right hidden lg:table-cell">Mariés</th>
                    <th class="py-3.5 px-4 text-left rtl:text-right hidden lg:table-cell">Wilaya</th>
                    <th class="py-3.5 px-4 text-left rtl:text-right">Source</th>
                    <th class="py-3.5 px-4 text-left rtl:text-right hidden md:table-cell">Réservations</th>
                    <th class="py-3.5 px-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60">
                @forelse($customers as $customer)
                    <tr class="hover:bg-slate-800/30 transition">
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-slate-800 border border-slate-700 flex items-center justify-center font-bold text-xs text-emerald-400 shrink-0">
                                    {{ strtoupper(mb_substr($customer->full_name, 0, 1)) }}
                                </div>
                                <div>
                                    <span class="font-semibold text-white">{{ $customer->full_name }}</span>
                                    <span class="block text-[11px] text-slate-500 font-mono">{{ $customer->customer_code }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 hidden md:table-cell">
                            <a href="{{ $customer->whatsappLink() }}" target="_blank"
                                class="text-emerald-400 hover:underline font-mono">{{ $customer->phone }}</a>
                        </td>
                        <td class="py-3.5 px-4 hidden lg:table-cell text-slate-300">
                            {{ $customer->bride_name && $customer->groom_name
                                ? $customer->groom_name . ' & ' . $customer->bride_name
                                : '—' }}
                        </td>
                        <td class="py-3.5 px-4 hidden lg:table-cell text-slate-400">
                            {{ $customer->wilaya?->name_fr ?? '—' }}
                        </td>
                        <td class="py-3.5 px-4 text-slate-400">
                            {{ \App\Models\Customer::SOURCES[$customer->source] ?? $customer->source }}
                        </td>
                        <td class="py-3.5 px-4 hidden md:table-cell text-center">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-800 text-slate-300 border border-slate-700">
                                {{ $customer->reservations_count ?? $customer->reservations()->count() }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-2 justify-end">
                                <a href="{{ $customer->whatsappLink() }}" target="_blank"
                                    class="p-1.5 rounded-lg text-emerald-400 hover:bg-emerald-950/40 transition" title="WhatsApp">
                                    💬
                                </a>
                                <a href="{{ route('customers.show', $customer) }}"
                                    class="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-white transition">
                                    Voir →
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-10 text-center text-slate-500">
                            Aucun client trouvé.
                            <a href="{{ route('customers.create') }}" class="text-emerald-400 hover:underline ml-1">Créer le premier client</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($customers->hasPages())
            <div class="px-4 py-4 border-t border-slate-800">
                {{ $customers->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
