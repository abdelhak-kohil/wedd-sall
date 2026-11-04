@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'تهيئة القاعات والأوقات' : 'Configuration des Salles & Créneaux')
@section('page_title', app()->getLocale() === 'ar' ? 'إدارة القاعات والفضاءات وأوقات الحجز' : 'Gestion des Salles & Créneaux Horaires')

@section('content')
<div class="space-y-8">

    <!-- 1. Physical Spaces Management -->
    <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-6 shadow-xl">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-800 gap-4 mb-6">
            <div>
                <h3 class="text-base font-bold text-white tracking-tight">
                    {{ app()->getLocale() === 'ar' ? 'القاعات والفضاءات المتاحة' : 'Espaces & Salles de Réception' }}
                </h3>
                <p class="text-xs text-slate-400 mt-1">
                    {{ app()->getLocale() === 'ar' ? 'تحديد سعة كل فضاء والأسعار القاعدية للحجز' : 'Définissez vos différentes salles, capacités min/max et prix de base.' }}
                </p>
            </div>
            
            <button type="button" onclick="document.getElementById('modal-add-space').classList.toggle('hidden')"
                class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs transition shadow-md flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>+ {{ app()->getLocale() === 'ar' ? 'إضافة قاعة جديدة' : 'Nouvelle Salle' }}</span>
            </button>
        </div>

        <!-- Spaces Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @forelse($spaces as $space)
                <div class="p-5 rounded-xl bg-slate-800/40 border border-slate-700/60 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-sm font-bold text-white">{{ $space->name_fr }}</h4>
                            <span class="text-xs font-arabic text-slate-400">{{ $space->name_ar }}</span>
                        </div>
                        <p class="text-xs text-slate-400 mb-1">
                            Capacité: <span class="text-slate-200 font-semibold">{{ $space->capacity_min }} à {{ $space->capacity_max }} personnes</span>
                        </p>
                        <p class="text-xs text-slate-400">
                            Tarif de base: <span class="text-emerald-400 font-bold font-mono">{{ number_format($space->base_price, 0, ',', ' ') }} DA</span>
                        </p>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-700/40 flex items-center justify-between text-xs">
                        <span class="inline-flex items-center gap-1 text-[11px] text-emerald-400">
                            ● Actif
                        </span>
                        <span class="text-[11px] text-slate-500 font-mono">ID #{{ $space->id }}</span>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-6 text-slate-500 text-xs">
                    Aucun espace configuré.
                </div>
            @endforelse
        </div>
    </div>

    <!-- 2. Time Slots Management -->
    <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-6 shadow-xl">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-800 gap-4 mb-6">
            <div>
                <h3 class="text-base font-bold text-white tracking-tight">
                    {{ app()->getLocale() === 'ar' ? 'الـفترات الزمنية المعتمدة (Créneaux)' : 'Créneaux Horaires Standards' }}
                </h3>
                <p class="text-xs text-slate-400 mt-1">
                    {{ app()->getLocale() === 'ar' ? 'فترات الحجز اليومية (مساء، سهرة، يوم كامل...)' : 'Plages horaires prédéfinies pour les réservations (Après-midi, Soirée, etc.).' }}
                </p>
            </div>

            <button type="button" onclick="document.getElementById('modal-add-slot').classList.toggle('hidden')"
                class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs border border-slate-700 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>+ {{ app()->getLocale() === 'ar' ? 'إضافة فترة زمنية' : 'Nouveau Créneau' }}</span>
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @forelse($slots as $slot)
                <div class="p-4 rounded-xl bg-slate-800/40 border border-slate-700/60 flex items-center justify-between">
                    <div>
                        <h4 class="text-xs font-bold text-white mb-0.5">{{ $slot->name_fr }} ({{ $slot->name_ar }})</h4>
                        <p class="text-xs font-mono text-emerald-400">
                            {{ $slot->formatted_range }}
                            @if($slot->is_overnight)
                                <span class="text-[10px] text-amber-400 bg-amber-950/40 px-1 rounded ml-1">Nuit</span>
                            @endif
                        </p>
                    </div>
                    <span class="text-xs text-slate-500 font-mono">#{{ $slot->sort_order }}</span>
                </div>
            @empty
                <div class="col-span-3 text-center py-6 text-slate-500 text-xs">
                    Aucun créneau configuré.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Modal: Add Space (Hidden by default) -->
    <div id="modal-add-space" class="hidden fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl max-w-md w-full p-6 shadow-2xl">
            <h4 class="text-base font-bold text-white mb-4">Ajouter une nouvelle salle</h4>
            <form method="POST" action="{{ route('settings.spaces.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="venue_id" value="{{ $venues->first()->id ?? 1 }}">

                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Nom (Français)</label>
                    <input type="text" name="name_fr" required placeholder="ex: Salle Royale"
                        class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white text-xs">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Nom (Arabe)</label>
                    <input type="text" name="name_ar" required placeholder="ex: القاعة الملكية"
                        class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white text-xs font-arabic">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1">Capacité Min</label>
                        <input type="number" name="capacity_min" value="50" min="10" required
                            class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white text-xs">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1">Capacité Max</label>
                        <input type="number" name="capacity_max" value="400" min="10" required
                            class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white text-xs">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Prix de base (DA)</label>
                    <input type="number" name="base_price" value="150000" min="0" step="1000" required
                        class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white text-xs font-mono">
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-800">
                    <button type="button" onclick="document.getElementById('modal-add-space').classList.add('hidden')"
                        class="px-3 py-2 bg-slate-800 text-slate-400 rounded-xl text-xs">Annuler</button>
                    <button type="submit"
                        class="px-4 py-2 bg-emerald-600 text-white font-semibold rounded-xl text-xs hover:bg-emerald-500">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Add Slot (Hidden by default) -->
    <div id="modal-add-slot" class="hidden fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl max-w-md w-full p-6 shadow-2xl">
            <h4 class="text-base font-bold text-white mb-4">Ajouter un créneau horaire</h4>
            <form method="POST" action="{{ route('settings.slots.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Libellé (Français)</label>
                    <input type="text" name="name_fr" required placeholder="ex: Soirée"
                        class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white text-xs">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Libellé (Arabe)</label>
                    <input type="text" name="name_ar" required placeholder="ex: سهرة"
                        class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white text-xs font-arabic">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1">Début</label>
                        <input type="time" name="start_time" required
                            class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white text-xs">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1">Fin</label>
                        <input type="time" name="end_time" required
                            class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white text-xs">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-800">
                    <button type="button" onclick="document.getElementById('modal-add-slot').classList.add('hidden')"
                        class="px-3 py-2 bg-slate-800 text-slate-400 rounded-xl text-xs">Annuler</button>
                    <button type="submit"
                        class="px-4 py-2 bg-emerald-600 text-white font-semibold rounded-xl text-xs hover:bg-emerald-500">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
