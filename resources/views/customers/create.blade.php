@extends('layouts.app')
@section('title', 'Nouveau Client')
@section('page_title', 'Nouveau Dossier Client')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-6 shadow-xl">

        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-emerald-900/50 border border-emerald-700/50 flex items-center justify-center text-emerald-400 text-xl">
                👤
            </div>
            <div>
                <h2 class="text-base font-bold text-white">Nouveau Client</h2>
                <p class="text-xs text-slate-400">Créer une fiche client dans le CRM</p>
            </div>
        </div>

        @if($errors->any())
            <div class="mb-4 p-3 rounded-xl bg-rose-950/40 border border-rose-800/50 text-rose-400 text-xs">
                <ul class="space-y-1 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('customers.store') }}" class="space-y-5">
            @csrf

            <!-- Contact -->
            <fieldset class="space-y-3">
                <legend class="text-[11px] text-slate-500 uppercase tracking-widest font-semibold mb-2">👤 Identité & Contact</legend>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Nom complet <span class="text-rose-500">*</span></label>
                    <input type="text" name="full_name" value="{{ old('full_name') }}" required
                        placeholder="Ex: Mohamed Belhadj"
                        class="w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Téléphone <span class="text-rose-500">*</span></label>
                        <input type="text" name="phone" value="{{ old('phone') }}" required
                            placeholder="0555 12 34 56"
                            class="w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">2ème téléphone</label>
                        <input type="text" name="second_phone" value="{{ old('second_phone') }}"
                            placeholder="0770 ..."
                            class="w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">WhatsApp</label>
                        <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number') }}"
                            placeholder="Si différent du tél."
                            class="w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>
            </fieldset>

            <!-- Mariés -->
            <fieldset class="space-y-3">
                <legend class="text-[11px] text-slate-500 uppercase tracking-widest font-semibold mb-2">💍 Mariés (optionnel)</legend>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Prénom du marié</label>
                        <input type="text" name="groom_name" value="{{ old('groom_name') }}"
                            placeholder="Ex: Walid"
                            class="w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Prénom de la mariée</label>
                        <input type="text" name="bride_name" value="{{ old('bride_name') }}"
                            placeholder="Ex: Samira"
                            class="w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>
            </fieldset>

            <!-- Location -->
            <fieldset class="space-y-3">
                <legend class="text-[11px] text-slate-500 uppercase tracking-widest font-semibold mb-2">📍 Localisation</legend>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Wilaya</label>
                        <select name="wilaya_id" id="wilaya_select"
                            class="w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="">— Choisir la wilaya —</option>
                            @foreach($wilayas as $w)
                                <option value="{{ $w->id }}" {{ old('wilaya_id') == $w->id ? 'selected' : '' }}>
                                    {{ $w->code }} - {{ $w->name_fr }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Commune</label>
                        <select name="commune_id" id="commune_select"
                            class="w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="">— Choisir —</option>
                            @foreach($communes as $c)
                                <option value="{{ $c->id }}" {{ old('commune_id') == $c->id ? 'selected' : '' }}>{{ $c->name_fr }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </fieldset>

            <!-- Meta -->
            <fieldset class="space-y-3">
                <legend class="text-[11px] text-slate-500 uppercase tracking-widest font-semibold mb-2">📌 Informations commerciales</legend>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Source client</label>
                        <select name="source" class="w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            @foreach(\App\Models\Customer::SOURCES as $key => $label)
                                <option value="{{ $key }}" {{ old('source', 'RECOMMENDATION') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Langue préférée</label>
                        <select name="preferred_language" class="w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="fr" {{ old('preferred_language', 'fr') === 'fr' ? 'selected' : '' }}>🇫🇷 Français</option>
                            <option value="ar" {{ old('preferred_language') === 'ar' ? 'selected' : '' }}>🇩🇿 العربية</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Notes internes</label>
                    <textarea name="internal_notes" rows="2"
                        placeholder="Informations supplémentaires sur le client..."
                        class="w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 resize-none">{{ old('internal_notes') }}</textarea>
                </div>
            </fieldset>

            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('customers.index') }}" class="text-xs text-slate-400 hover:text-white transition">← Retour</a>
                <button type="submit"
                    class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold transition shadow-md">
                    ✅ Créer le dossier client
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('wilaya_select').addEventListener('change', function() {
    const wilayaId = this.value;
    const sel = document.getElementById('commune_select');
    sel.innerHTML = '<option value="">Chargement...</option>';

    if (!wilayaId) {
        sel.innerHTML = '<option value="">— Choisir —</option>';
        return;
    }

    fetch(`/api/wilayas/${wilayaId}/communes`)
        .then(r => r.json())
        .then(data => {
            sel.innerHTML = '<option value="">— Commune —</option>';
            data.forEach(c => {
                const opt = document.createElement('option');
                opt.value = c.id;
                opt.textContent = c.name_fr;
                sel.appendChild(opt);
            });
        });
});
</script>
@endpush
