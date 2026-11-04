@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'فحص التوفر السريع' : 'Disponibilité Immédiate')
@section('page_title', app()->getLocale() === 'ar' ? 'فحص توفر القاعات (مكالمات الزبائن)' : 'Vérification de Disponibilité en Direct')

@section('content')
<div class="space-y-6">

    <!-- Top Control Bar: Date Selector & Quick Filters -->
    <div class="bg-slate-900/90 border border-slate-800/80 rounded-2xl p-5 shadow-xl">
        <form method="GET" action="{{ route('availability.index') }}" class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <!-- Date Navigator -->
            <div class="flex items-center gap-2">
                <a href="{{ route('availability.index', ['date' => $selectedDate->copy()->subDay()->toDateString()]) }}"
                    class="p-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 transition" title="Jour précédent">
                    <svg class="w-4 h-4 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>

                <div class="relative">
                    <input type="date" name="date" value="{{ $selectedDate->toDateString() }}" onchange="this.form.submit()"
                        class="px-4 py-2.5 rounded-xl bg-slate-800 border border-slate-700 text-white font-semibold text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer">
                </div>

                <a href="{{ route('availability.index', ['date' => $selectedDate->copy()->addDay()->toDateString()]) }}"
                    class="p-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 transition" title="Jour suivant">
                    <svg class="w-4 h-4 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>

                <div class="px-3 py-1.5 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 font-bold text-sm ml-2">
                    {{ $selectedDate->locale(app()->getLocale())->isoFormat('dddd D MMMM YYYY') }}
                </div>
            </div>

            <!-- Quick Date Jump Buttons -->
            <div class="flex items-center gap-1.5 overflow-x-auto text-xs">
                <a href="{{ route('availability.index', ['date' => $presets['today']->toDateString()]) }}"
                    class="px-3 py-2 rounded-xl transition font-medium {{ $selectedDate->isToday() ? 'bg-emerald-600 text-white shadow-md' : 'bg-slate-800/80 hover:bg-slate-800 text-slate-300' }}">
                    {{ app()->getLocale() === 'ar' ? 'اليوم' : 'Aujourd\'hui' }}
                </a>
                <a href="{{ route('availability.index', ['date' => $presets['tomorrow']->toDateString()]) }}"
                    class="px-3 py-2 rounded-xl transition font-medium {{ $selectedDate->isTomorrow() ? 'bg-emerald-600 text-white shadow-md' : 'bg-slate-800/80 hover:bg-slate-800 text-slate-300' }}">
                    {{ app()->getLocale() === 'ar' ? 'غداً' : 'Demain' }}
                </a>
                <a href="{{ route('availability.index', ['date' => $presets['next_friday']->toDateString()]) }}"
                    class="px-3 py-2 rounded-xl transition font-medium {{ $selectedDate->toDateString() === $presets['next_friday']->toDateString() ? 'bg-emerald-600 text-white shadow-md' : 'bg-slate-800/80 hover:bg-slate-800 text-slate-300' }}">
                    {{ app()->getLocale() === 'ar' ? 'الجمعة' : 'Vendredi' }}
                </a>
                <a href="{{ route('availability.index', ['date' => $presets['next_saturday']->toDateString()]) }}"
                    class="px-3 py-2 rounded-xl transition font-medium {{ $selectedDate->toDateString() === $presets['next_saturday']->toDateString() ? 'bg-emerald-600 text-white shadow-md' : 'bg-slate-800/80 hover:bg-slate-800 text-slate-300' }}">
                    {{ app()->getLocale() === 'ar' ? 'السبت' : 'Samedi' }}
                </a>
            </div>
        </form>
    </div>

    <!-- Space Availability Matrix -->
    <div class="space-y-6">
        @forelse($matrix as $item)
            @php $space = $item['space']; @endphp
            <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-6 shadow-xl hover:border-slate-700/80 transition">
                <!-- Space Header -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-800/80 gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center font-bold text-emerald-400">
                            🏛️
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white tracking-tight">
                                {{ $space->name_fr }}
                                <span class="text-sm font-normal text-slate-400 font-arabic ml-1 rtl:mr-1">({{ $space->name_ar }})</span>
                            </h3>
                            <p class="text-xs text-slate-400">
                                {{ app()->getLocale() === 'ar' ? 'السعة:' : 'Capacité:' }} <span class="text-slate-200 font-semibold">{{ $space->capacity_min }} - {{ $space->capacity_max }} {{ app()->getLocale() === 'ar' ? 'شخص' : 'personnes' }}</span>
                                · {{ app()->getLocale() === 'ar' ? 'السعر القاعدي:' : 'Tarif de base:' }} <span class="text-emerald-400 font-semibold">{{ number_format($space->base_price, 0, ',', ' ') }} DA</span>
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        @if($item['active_reservations_count'] === 0)
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                                ● {{ app()->getLocale() === 'ar' ? 'القاعة شاغرة طيلة اليوم' : 'Totalement Disponible' }}
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/30">
                                ● {{ $item['active_reservations_count'] }} {{ app()->getLocale() === 'ar' ? 'حجز أو خيار مسجل' : 'réservation(s) active(s)' }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Slots Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-5">
                    @foreach($item['slots'] as $slotData)
                        @php 
                            $slot = $slotData['slot'];
                            $status = $slotData['status'];
                            $conflict = $slotData['conflict'];
                        @endphp

                        <div class="p-4 rounded-xl border transition
                            @if($status === 'AVAILABLE') bg-slate-800/40 border-slate-700/60 hover:border-emerald-500/60
                            @elseif($status === 'HOLD') bg-amber-950/20 border-amber-800/50
                            @elseif($status === 'EXPIRED_HOLD') bg-amber-950/10 border-amber-700/40 border-dashed
                            @elseif($status === 'BOOKED') bg-rose-950/20 border-rose-800/50
                            @endif">
                            
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold text-white uppercase tracking-wider">
                                    {{ app()->getLocale() === 'ar' ? $slot->name_ar : $slot->name_fr }}
                                </span>
                                <span class="text-xs font-mono text-slate-400 bg-slate-800 px-2 py-0.5 rounded">
                                    {{ substr($slot->start_time, 0, 5) }} → {{ substr($slot->end_time, 0, 5) }}
                                </span>
                            </div>

                            @if($status === 'AVAILABLE')
                                <div class="my-3">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        {{ app()->getLocale() === 'ar' ? 'متاح للحجز' : 'DISPONIBLE' }}
                                    </span>
                                    <p class="text-[11px] text-slate-400 mt-2">
                                        {{ app()->getLocale() === 'ar' ? 'يمكن حجز هذه القاعة الآن.' : 'Aucun conflit horaire.' }}
                                    </p>
                                </div>

                                <div class="pt-2 border-t border-slate-700/40 flex gap-2">
                                    <a href="{{ route('reservations.create', ['space_id' => $space->id, 'date' => $selectedDate->toDateString(), 'slot_id' => $slot->id]) }}"
                                        class="w-full py-2 px-3 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs text-center shadow transition flex items-center justify-center gap-1.5">
                                        <span>+ {{ app()->getLocale() === 'ar' ? 'حجز هذا التاريخ' : 'Bloquer cette date' }}</span>
                                    </a>
                                </div>

                            @elseif($status === 'HOLD')
                                <div class="my-3">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/30">
                                        ⏱ {{ app()->getLocale() === 'ar' ? 'خيار مؤقت' : 'OPTION TEMPORAIRE' }}
                                    </span>
                                    <p class="text-xs font-semibold text-white mt-2 truncate">
                                        {{ $conflict->customer_name }}
                                    </p>
                                    <p class="text-[11px] text-amber-400/90 mt-0.5">
                                        {{ app()->getLocale() === 'ar' ? 'ينتهي خلال' : 'Expire dans' }}: <span class="font-bold">{{ $conflict->hold_hours_remaining }}h</span>
                                    </p>
                                </div>

                                <div class="pt-2 border-t border-slate-700/40 flex items-center justify-between text-xs">
                                    <span class="text-slate-400 font-mono">{{ $conflict->customer_phone }}</span>
                                    <a href="https://wa.me/213{{ ltrim($conflict->customer_phone, '0') }}?text={{ urlencode('Bonjour ' . $conflict->customer_name . ', rappel concernant votre option pour la salle ' . $space->name_fr . ' le ' . $selectedDate->format('d/m/Y')) }}"
                                        target="_blank"
                                        class="px-2.5 py-1 rounded bg-emerald-900/60 hover:bg-emerald-800 text-emerald-300 font-medium transition flex items-center gap-1">
                                        <span>WhatsApp</span>
                                    </a>
                                </div>

                            @elseif($status === 'EXPIRED_HOLD')
                                <div class="my-3">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-amber-500/10 text-amber-300 border border-amber-500/30">
                                        ⚠️ {{ app()->getLocale() === 'ar' ? 'خيار منتهي الصلاحية' : 'OPTION EXPIRÉE' }}
                                    </span>
                                    <p class="text-xs text-slate-300 mt-2 truncate">
                                        Précédent: {{ $conflict->customer_name }}
                                    </p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">
                                        {{ app()->getLocale() === 'ar' ? 'يمكن تحريره أو إعادة تفعيله' : 'Peut être repris immédiatement.' }}
                                    </p>
                                </div>

                                <div class="pt-2 border-t border-slate-700/40">
                                    <a href="{{ route('reservations.create', ['space_id' => $space->id, 'date' => $selectedDate->toDateString(), 'slot_id' => $slot->id]) }}"
                                        class="w-full py-2 px-3 rounded-lg bg-amber-600 hover:bg-amber-500 text-white font-semibold text-xs text-center transition block">
                                        + {{ app()->getLocale() === 'ar' ? 'إعادة الحجز لمشتري جديد' : 'Reprendre ce créneau' }}
                                    </a>
                                </div>

                            @elseif($status === 'BOOKED')
                                <div class="my-3">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/30">
                                        🔒 {{ app()->getLocale() === 'ar' ? 'محجوز - مؤكد' : 'RÉSERVÉ - CONFIRMÉ' }}
                                    </span>
                                    <p class="text-xs font-bold text-white mt-2 truncate">
                                        {{ $conflict->bride_name && $conflict->groom_name ? $conflict->groom_name . ' & ' . $conflict->bride_name : $conflict->customer_name }}
                                    </p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">
                                        {{ $conflict->event_type }} · {{ $conflict->expected_guests }} invités
                                    </p>
                                </div>

                                <div class="pt-2 border-t border-slate-700/40 flex items-center justify-between text-xs">
                                    <span class="text-slate-400">Solde dû:</span>
                                    <span class="font-bold {{ $conflict->remaining_balance > 0 ? 'text-rose-400' : 'text-emerald-400' }}">
                                        {{ number_format($conflict->remaining_balance, 0, ',', ' ') }} DA
                                    </span>
                                </div>
                            @endif

                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-12 text-center">
                <p class="text-slate-400 text-sm">Aucune salle configurée. Veuillez ajouter vos espaces dans les paramètres.</p>
                <a href="{{ route('settings.spaces') }}" class="mt-4 inline-block px-4 py-2 bg-emerald-600 rounded-xl text-white font-semibold text-xs">
                    + Configurer mes salles
                </a>
            </div>
        @endforelse
    </div>

</div>
@endsection
