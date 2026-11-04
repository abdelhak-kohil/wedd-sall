@extends('layouts.app')
@section('title', 'Packs & Forfaits')
@section('page_title', app()->getLocale() === 'ar' ? 'باقات وخدمات الأفراح' : 'Catalogue des Forfaits & Prestations')

@section('content')
<div class="space-y-6">

    <!-- Top Bar -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 shadow-xl flex items-center justify-between">
        <div>
            <h2 class="text-base font-bold text-white">Forfaits & Packs Événements</h2>
            <p class="text-xs text-slate-400">Configurez vos formules de mariage, fiançailles et prestations complémentaires</p>
        </div>

        <button type="button" onclick="document.getElementById('new_package_modal').classList.remove('hidden')"
            class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold shadow-lg transition flex items-center gap-2">
            <span>+ {{ app()->getLocale() === 'ar' ? 'إضافة باقة جديدة' : 'Nouveau Forfait' }}</span>
        </button>
    </div>

    <!-- Package Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @forelse($packages as $pkg)
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 shadow-xl flex flex-col justify-between hover:border-slate-700 transition relative {{ !$pkg->is_active ? 'opacity-60' : '' }}">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold px-2.5 py-0.5 rounded-full {{ $pkg->is_active ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : 'bg-slate-800 text-slate-400' }}">
                            {{ $pkg->is_active ? 'Actif' : 'Désactivé' }}
                        </span>
                        <form method="POST" action="{{ route('packages.toggle', $pkg) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-[11px] text-slate-400 hover:text-white underline">
                                {{ $pkg->is_active ? 'Désactiver' : 'Activer' }}
                            </button>
                        </form>
                    </div>

                    <h3 class="text-base font-bold text-white">{{ $pkg->name_fr }}</h3>
                    @if($pkg->name_ar)
                        <p class="text-xs font-semibold text-amber-300/80 font-arabic mt-0.5">{{ $pkg->name_ar }}</p>
                    @endif

                    @if($pkg->description_fr)
                        <p class="text-xs text-slate-400 mt-2">{{ $pkg->description_fr }}</p>
                    @endif

                    <!-- Price Block -->
                    <div class="my-5 p-3.5 bg-slate-800/60 rounded-xl border border-slate-700/60">
                        <span class="text-[11px] text-slate-400 block">Tarif de base</span>
                        <span class="text-xl font-bold font-mono text-emerald-400">
                            {{ number_format($pkg->base_price, 0, ',', ' ') }} DA
                        </span>
                        @if($pkg->price_per_guest > 0)
                            <span class="text-xs text-slate-300 block mt-1 font-mono">
                                + {{ number_format($pkg->price_per_guest, 0, ',', ' ') }} DA / invité
                            </span>
                        @endif
                    </div>

                    <!-- Included items -->
                    @if(!empty($pkg->included_items))
                        <div>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Inclus dans la formule :</span>
                            <ul class="space-y-1.5 text-xs text-slate-300">
                                @foreach($pkg->included_items as $item)
                                    <li class="flex items-start gap-2">
                                        <span class="text-emerald-400 font-bold shrink-0">✓</span>
                                        <span>{{ $item }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                <div class="mt-6 pt-4 border-t border-slate-800 flex items-center justify-between text-xs">
                    <span class="text-slate-500 font-mono">{{ $pkg->reservations()->count() }} réservation(s)</span>
                    <a href="{{ route('quotations.create', ['package_id' => $pkg->id]) }}" class="text-indigo-400 hover:underline font-semibold">
                        Créer devis avec ce pack →
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-3 py-12 text-center text-slate-500 bg-slate-900/50 border border-slate-800 rounded-2xl">
                Aucun forfait configuré. Cliquez sur "+ Nouveau Forfait" pour créer vos formules.
            </div>
        @endforelse
    </div>

</div>

<!-- New Package Modal -->
<div id="new_package_modal" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 max-w-lg w-full shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3 mb-4">
            <h3 class="text-sm font-bold text-white">Créer un Nouveau Forfait</h3>
            <button type="button" onclick="document.getElementById('new_package_modal').classList.add('hidden')" class="text-slate-400 hover:text-white">✕</button>
        </div>

        <form method="POST" action="{{ route('packages.store') }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-slate-300 mb-1">Nom (Français) <span class="text-rose-500">*</span></label>
                    <input type="text" name="name_fr" required placeholder="Ex: Pack Prestige"
                        class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white">
                </div>
                <div>
                    <label class="block text-xs text-slate-300 mb-1">Nom (Arabe)</label>
                    <input type="text" name="name_ar" placeholder="باقة البرستيج" dir="rtl"
                        class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white font-arabic">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-slate-300 mb-1">Prix de base (DA) <span class="text-rose-500">*</span></label>
                    <input type="number" step="1000" name="base_price" required placeholder="250000"
                        class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs font-mono text-emerald-400 font-bold">
                </div>
                <div>
                    <label class="block text-xs text-slate-300 mb-1">Supplément par invité (DA)</label>
                    <input type="number" step="50" name="price_per_guest" value="0" placeholder="0"
                        class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs font-mono text-white">
                </div>
            </div>

            <div>
                <label class="block text-xs text-slate-300 mb-1">Description courte</label>
                <input type="text" name="description_fr" placeholder="Ex: Idéal pour grands mariages..."
                    class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white">
            </div>

            <div>
                <label class="block text-xs text-slate-300 mb-1">Prestations incluses (1 par ligne)</label>
                <textarea name="included_items_text" rows="4" placeholder="Sonorisation & DJ&#10;Caméra 4K salle dames&#10;Trône d'honneur royal&#10;Fumée lourde"
                    class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white resize-none"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
                <button type="button" onclick="document.getElementById('new_package_modal').classList.add('hidden')"
                    class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs rounded-xl transition">
                    Annuler
                </button>
                <button type="submit"
                    class="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl transition shadow">
                    Enregistrer le forfait
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
