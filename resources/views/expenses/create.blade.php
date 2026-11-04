@extends('layouts.app')
@section('title', 'Nouvelle Dépense')
@section('page_title', 'Saisie d\'une Dépense')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-6 shadow-xl">

        <p class="text-slate-400 text-sm mb-6">Enregistrez un décaissement : loyer, charges, fournitures, prestataires, etc.</p>

        @if($errors->any())
        <div class="mb-4 bg-rose-500/10 border border-rose-500/30 rounded-xl px-4 py-3 text-rose-400 text-sm space-y-1">
            @foreach($errors->all() as $e)<p>• {{ $e }}</p>@endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('expenses.store') }}" class="space-y-5">
            @csrf

            {{-- Description --}}
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1">Libellé de la dépense <span class="text-rose-400">*</span></label>
                <input type="text" name="description" value="{{ old('description') }}"
                       placeholder="Ex: Facture électricité Sonelgaz – Septembre"
                       class="w-full bg-slate-800 border border-slate-700 text-white rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-rose-500 focus:outline-none"
                       required maxlength="500">
            </div>

            {{-- Category + Amount row --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1">Catégorie</label>
                    <select name="expense_category_id"
                            class="w-full bg-slate-800 border border-slate-700 text-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-rose-500 focus:outline-none">
                        <option value="">— Sans catégorie —</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(old('expense_category_id') == $cat->id)>{{ $cat->name_fr }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1">Montant (DA) <span class="text-rose-400">*</span></label>
                    <input type="number" name="amount" value="{{ old('amount') }}" min="1" step="0.01"
                           placeholder="0.00"
                           class="w-full bg-slate-800 border border-slate-700 text-white rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-rose-500 focus:outline-none"
                           required>
                </div>
            </div>

            {{-- Date + Vendor row --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1">Date de la dépense <span class="text-rose-400">*</span></label>
                    <input type="date" name="expense_date" value="{{ old('expense_date', today()->toDateString()) }}"
                           class="w-full bg-slate-800 border border-slate-700 text-white rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-rose-500 focus:outline-none"
                           required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1">Fournisseur / Bénéficiaire</label>
                    <input type="text" name="vendor" value="{{ old('vendor') }}"
                           placeholder="Ex: Sonelgaz, ALGÉRIE TÉLÉCOM…"
                           class="w-full bg-slate-800 border border-slate-700 text-white rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-rose-500 focus:outline-none"
                           maxlength="150">
                </div>
            </div>

            {{-- Payment method --}}
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-2">Mode de règlement <span class="text-rose-400">*</span></label>
                <div class="grid grid-cols-4 gap-2">
                    @foreach(['CASH' => 'Espèces', 'CHEQUE' => 'Chèque', 'TRANSFER' => 'Virement', 'OTHER' => 'Autre'] as $val => $label)
                    <label class="cursor-pointer">
                        <input type="radio" name="payment_method" value="{{ $val }}" class="sr-only peer"
                               @checked(old('payment_method', 'CASH') === $val)>
                        <span class="flex items-center justify-center px-3 py-2 text-xs font-medium rounded-xl border border-slate-700
                            peer-checked:bg-rose-600/30 peer-checked:border-rose-500 peer-checked:text-rose-300
                            text-slate-400 hover:border-slate-600 hover:text-slate-300 transition">
                            {{ $label }}
                        </span>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Cheque fields (shown by JS when CHEQUE selected) --}}
            <div id="cheque-fields" class="grid grid-cols-2 gap-4 {{ old('payment_method') === 'CHEQUE' ? '' : 'hidden' }}">
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1">N° Chèque</label>
                    <input type="text" name="cheque_number" value="{{ old('cheque_number') }}"
                           class="w-full bg-slate-800 border border-slate-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1">Banque</label>
                    <input type="text" name="bank_name" value="{{ old('bank_name') }}"
                           class="w-full bg-slate-800 border border-slate-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none">
                </div>
            </div>

            {{-- Reference + Notes --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1">N° Reçu / Référence</label>
                    <input type="text" name="reference" value="{{ old('reference') }}"
                           placeholder="Optionnel"
                           class="w-full bg-slate-800 border border-slate-700 text-white rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-rose-500 focus:outline-none"
                           maxlength="50">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1">Notes internes</label>
                    <input type="text" name="notes" value="{{ old('notes') }}"
                           class="w-full bg-slate-800 border border-slate-700 text-white rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-rose-500 focus:outline-none">
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex gap-3 pt-2">
                <a href="{{ route('expenses.index') }}"
                   class="flex-1 text-center px-4 py-2.5 border border-slate-700 text-slate-300 text-sm rounded-xl hover:bg-slate-800 transition">
                    Annuler
                </a>
                <button type="submit"
                        class="flex-1 px-4 py-2.5 bg-rose-600 hover:bg-rose-500 text-white text-sm font-semibold rounded-xl transition">
                    Enregistrer la dépense
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.querySelectorAll('input[name="payment_method"]').forEach(r => {
    r.addEventListener('change', () => {
        document.getElementById('cheque-fields').classList.toggle('hidden', r.value !== 'CHEQUE');
    });
});
</script>
@endsection
