@extends('layouts.app')
@section('title', 'Stock & Inventaire')
@section('page_title', 'Stock & Inventaire Matériel')

@section('content')
<div class="space-y-5">

    {{-- ── TOP BAR ── --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <span class="text-2xl">📦</span>
            <div>
                <h1 class="text-base font-bold text-white">Stock & Inventaire Matériel</h1>
                <p class="text-xs text-slate-400">{{ $stats['total_items'] }} articles · {{ $stats['low_stock'] }} alertes</p>
            </div>
        </div>
        <button onclick="openAddModal()"
            class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 text-white text-xs font-bold transition shadow flex items-center gap-1.5">
            <span class="text-base leading-none">+</span> Ajouter un article
        </button>
    </div>

    {{-- flash --}}
    @if(session('success'))
        <div class="bg-emerald-950/70 border border-emerald-700/60 rounded-xl px-4 py-3 text-emerald-300 text-xs flex items-center justify-between">
            <span>✓ {{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-white">✕</button>
        </div>
    @endif

    {{-- ── KPI STRIP ── --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-3.5 text-center">
            <p class="text-[10px] uppercase text-slate-500 font-bold tracking-wider">Articles</p>
            <p class="text-2xl font-bold text-white font-mono mt-1">{{ $stats['total_items'] }}</p>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-3.5 text-center">
            <p class="text-[10px] uppercase text-slate-500 font-bold tracking-wider">Valeur parc</p>
            <p class="text-xl font-bold text-amber-400 font-mono mt-1">{{ number_format($stats['total_value'], 0, ',', ' ') }} <span class="text-xs">DA</span></p>
        </div>
        <div class="bg-amber-950/30 border border-amber-800/50 rounded-xl p-3.5 text-center">
            <p class="text-[10px] uppercase text-amber-500 font-bold tracking-wider">⚠ Stock faible</p>
            <p class="text-2xl font-bold text-amber-400 font-mono mt-1">{{ $stats['low_stock'] }}</p>
        </div>
        <div class="bg-rose-950/30 border border-rose-800/50 rounded-xl p-3.5 text-center">
            <p class="text-[10px] uppercase text-rose-500 font-bold tracking-wider">⛔ Rupture</p>
            <p class="text-2xl font-bold text-rose-400 font-mono mt-1">{{ $stats['out_of_stock'] }}</p>
        </div>
    </div>

    {{-- ── CATEGORY TABS ── --}}
    <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs">
        <a href="{{ route('inventory.index', request()->except(['category','page'])) }}"
           class="px-3 py-1.5 rounded-lg font-semibold shrink-0 transition {{ !request('category') ? 'bg-amber-600 text-white' : 'bg-slate-900 border border-slate-800 text-slate-400 hover:text-white' }}">
            Tout ({{ $stats['total_items'] }})
        </a>
        @foreach($categories as $key => $cat)
            <a href="{{ route('inventory.index', array_merge(request()->except(['page']), ['category' => $key])) }}"
               class="px-3 py-1.5 rounded-lg font-semibold shrink-0 transition flex items-center gap-1 {{ request('category') === $key ? 'bg-amber-600 text-white' : 'bg-slate-900 border border-slate-800 text-slate-400 hover:text-white' }}">
                {{ $cat['icon'] }} {{ $cat['label'] }}
            </a>
        @endforeach
    </div>

    {{-- ── SEARCH ── --}}
    <form method="GET" action="{{ route('inventory.index') }}" class="flex items-center gap-2">
        @if(request('category'))
            <input type="hidden" name="category" value="{{ request('category') }}">
        @endif
        <div class="relative flex-1 max-w-sm">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher un article…"
                class="w-full pl-8 pr-3 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-amber-500">
            <span class="absolute left-2.5 top-2.5 text-slate-500 text-xs">🔍</span>
        </div>
        <button type="submit" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-white text-xs rounded-xl transition">Filtrer</button>
        @if(request()->hasAny(['q','category','low_stock']))
            <a href="{{ route('inventory.index') }}" class="text-xs text-rose-400 hover:underline">Réinitialiser</a>
        @endif
    </form>

    {{-- ── ITEM CARDS ── --}}
    @if($items->isEmpty())
        <div class="py-20 text-center text-slate-500">
            <div class="text-5xl mb-3">📦</div>
            <p class="text-sm font-medium text-slate-300">Aucun matériel trouvé.</p>
            <button onclick="openAddModal()" class="mt-4 px-5 py-2 bg-amber-600 hover:bg-amber-500 text-white text-xs font-bold rounded-xl transition">+ Ajouter un article</button>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($items as $item)
            @php
                $pct = $item->quantity_total > 0 ? round(($item->quantity_available / $item->quantity_total) * 100) : 0;
                $color = $item->is_out_of_stock ? 'rose' : ($item->is_low_stock ? 'amber' : 'emerald');
                if($item->is_out_of_stock)     { $bIcon='🔴'; $bLabel='Rupture';   $bClass='bg-rose-950/80 border-rose-700/80 text-rose-300'; }
                elseif($item->is_low_stock)    { $bIcon='🟡'; $bLabel='Faible';    $bClass='bg-amber-950/80 border-amber-700/80 text-amber-300'; }
                else                           { $bIcon='🟢'; $bLabel='En stock';  $bClass='bg-emerald-950/80 border-emerald-700/80 text-emerald-300'; }
                $barClass = $color === 'rose' ? 'bg-rose-500' : ($color === 'amber' ? 'bg-amber-500' : 'bg-emerald-500');
                $numClass = $color === 'rose' ? 'text-rose-400' : ($color === 'amber' ? 'text-amber-400' : 'text-emerald-400');
            @endphp
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 flex flex-col gap-3 hover:border-slate-700 transition group">

                {{-- Header --}}
                <div class="flex items-start justify-between gap-2">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="text-xl shrink-0">{{ $item->category_icon }}</span>
                        <div class="min-w-0">
                            <p class="font-bold text-white text-sm truncate group-hover:text-amber-400 transition">{{ $item->name }}</p>
                            <p class="text-[10px] text-slate-500">{{ $item->category_label }}</p>
                        </div>
                    </div>
                    <span class="text-[10px] px-2 py-0.5 rounded-full border font-bold shrink-0 {{ $bClass }}">
                        {{ $bIcon }} {{ $bLabel }}
                    </span>
                </div>

                {{-- Availability gauge --}}
                <div>
                    <div class="flex items-baseline justify-between mb-1">
                        <span class="text-xs text-slate-400">Disponible</span>
                        <span class="font-mono font-bold text-sm {{ $numClass }}">
                            {{ $item->quantity_available }}<span class="text-slate-500 text-xs font-normal"> / {{ $item->quantity_total }} {{ $item->unit }}</span>
                        </span>
                    </div>
                    <div class="w-full h-2 bg-slate-800 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all {{ $barClass }}" style="width: {{ $pct }}%"></div>
                    </div>
                    @if($item->quantity_alert > 0)
                        <p class="text-[10px] text-slate-600 mt-1">Seuil alerte : {{ $item->quantity_alert }} {{ $item->unit }}</p>
                    @endif
                </div>

                {{-- Quick +/- stock --}}
                <div class="flex items-center gap-2">
                    <form method="POST" action="{{ route('inventory.quickstock', $item) }}" class="flex-1">
                        @csrf
                        <input type="hidden" name="type" value="OUT">
                        <input type="hidden" name="qty" value="1">
                        <button type="submit" class="w-full py-1.5 rounded-lg bg-slate-800 hover:bg-rose-900/50 border border-slate-700 hover:border-rose-700 text-slate-300 hover:text-rose-300 text-xs font-bold transition">
                            − 1
                        </button>
                    </form>
                    <form method="POST" action="{{ route('inventory.quickstock', $item) }}" class="flex-1">
                        @csrf
                        <input type="hidden" name="type" value="IN">
                        <input type="hidden" name="qty" value="1">
                        <button type="submit" class="w-full py-1.5 rounded-lg bg-slate-800 hover:bg-emerald-900/50 border border-slate-700 hover:border-emerald-700 text-slate-300 hover:text-emerald-300 text-xs font-bold transition">
                            + 1
                        </button>
                    </form>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2 pt-1 border-t border-slate-800">
                    <button type="button"
                        onclick="openEditModal({{ json_encode([
                            'id'                 => $item->id,
                            'name'               => $item->name,
                            'category'           => $item->category,
                            'unit'               => $item->unit,
                            'quantity_total'     => $item->quantity_total,
                            'quantity_available' => $item->quantity_available,
                            'quantity_alert'     => $item->quantity_alert,
                            'location'           => $item->location,
                            'unit_cost'          => (float) $item->unit_cost,
                        ]) }})"
                        class="flex-1 py-1.5 rounded-lg bg-amber-600/20 hover:bg-amber-600/40 border border-amber-600/40 text-amber-300 text-xs font-semibold transition text-center">
                        ✏️ Modifier
                    </button>

                    @if(auth()->user()->isOwner() || auth()->user()->isManager())
                        <form method="POST" action="{{ route('inventory.destroy', $item) }}"
                            onsubmit="return confirm('Supprimer « {{ $item->name }} » ?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="py-1.5 px-2.5 rounded-lg bg-slate-800 hover:bg-rose-900/40 border border-slate-700 hover:border-rose-700 text-slate-500 hover:text-rose-400 text-xs transition">
                                🗑
                            </button>
                        </form>
                    @endif
                </div>

            </div>
            @endforeach
        </div>
    @endif

</div>

{{-- MODAL: AJOUTER --}}
<div id="add_item_modal" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 max-w-md w-full shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-sm font-bold text-white">📦 Ajouter un article</h3>
            <button onclick="document.getElementById('add_item_modal').classList.add('hidden')" class="text-slate-400 hover:text-white">✕</button>
        </div>
        <form method="POST" action="{{ route('inventory.store') }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Nom <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required placeholder="Ex: Tables rondes 180cm"
                    class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Catégorie <span class="text-rose-500">*</span></label>
                    <select name="category" required class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                        <option value="">— Choisir —</option>
                        @foreach($categories as $key => $cat)
                            <option value="{{ $key }}">{{ $cat['icon'] }} {{ $cat['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Unité <span class="text-rose-500">*</span></label>
                    <select name="unit" required class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                        <option value="pièce">pièce</option>
                        <option value="lot">lot</option>
                        <option value="set">set</option>
                        <option value="paire">paire</option>
                        <option value="douzaine">douzaine</option>
                        <option value="carton">carton</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Total <span class="text-rose-500">*</span></label>
                    <input type="number" name="quantity_total" id="add_qty_total" required min="0"
                        oninput="document.getElementById('add_qty_avail').value=this.value"
                        class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white font-mono focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Disponible</label>
                    <input type="number" name="quantity_available" id="add_qty_avail" min="0"
                        class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white font-mono focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Seuil</label>
                    <input type="number" name="quantity_alert" min="0" value="5"
                        class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white font-mono focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Emplacement</label>
                    <input type="text" name="location" placeholder="Ex: Hangar A"
                        class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Coût unitaire (DA)</label>
                    <input type="number" name="unit_cost" min="0" step="50"
                        class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white font-mono focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-2 border-t border-slate-800">
                <button type="button" onclick="document.getElementById('add_item_modal').classList.add('hidden')"
                    class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs rounded-xl transition">Annuler</button>
                <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-500 text-white text-xs font-bold rounded-xl transition shadow">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: MODIFIER --}}
<div id="edit_item_modal" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 max-w-md w-full shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <div>
                <h3 class="text-sm font-bold text-white">✏️ Modifier</h3>
                <p id="edit_subtitle" class="text-xs text-amber-400"></p>
            </div>
            <button onclick="document.getElementById('edit_item_modal').classList.add('hidden')" class="text-slate-400 hover:text-white">✕</button>
        </div>
        <form method="POST" id="edit_form" class="space-y-3">
            @csrf @method('PUT')
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Nom <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="e_name" required
                    class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Catégorie</label>
                    <select name="category" id="e_category" class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                        @foreach($categories as $key => $cat)
                            <option value="{{ $key }}">{{ $cat['icon'] }} {{ $cat['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Unité</label>
                    <select name="unit" id="e_unit" class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                        <option value="pièce">pièce</option>
                        <option value="lot">lot</option>
                        <option value="set">set</option>
                        <option value="paire">paire</option>
                        <option value="douzaine">douzaine</option>
                        <option value="carton">carton</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Total</label>
                    <input type="number" name="quantity_total" id="e_total" min="0"
                        class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white font-mono focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Disponible</label>
                    <input type="number" name="quantity_available" id="e_available" min="0"
                        class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white font-mono focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Seuil</label>
                    <input type="number" name="quantity_alert" id="e_alert" min="0"
                        class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white font-mono focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Emplacement</label>
                    <input type="text" name="location" id="e_location"
                        class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Coût unitaire (DA)</label>
                    <input type="number" name="unit_cost" id="e_cost" min="0" step="50"
                        class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white font-mono focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-2 border-t border-slate-800">
                <button type="button" onclick="document.getElementById('edit_item_modal').classList.add('hidden')"
                    class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs rounded-xl transition">Annuler</button>
                <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-500 text-white text-xs font-bold rounded-xl transition shadow">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openAddModal() {
    document.getElementById('add_item_modal').classList.remove('hidden');
}
function openEditModal(item) {
    document.getElementById('edit_subtitle').textContent = item.name;
    document.getElementById('edit_form').action = '/inventory/' + item.id;
    document.getElementById('e_name').value      = item.name;
    document.getElementById('e_total').value     = item.quantity_total;
    document.getElementById('e_available').value = item.quantity_available;
    document.getElementById('e_alert').value     = item.quantity_alert;
    document.getElementById('e_location').value  = item.location || '';
    document.getElementById('e_cost').value      = item.unit_cost || '';
    ['e_category','e_unit'].forEach(id => {
        const key = id === 'e_category' ? 'category' : 'unit';
        const el = document.getElementById(id);
        [...el.options].forEach(o => o.selected = o.value === item[key]);
    });
    document.getElementById('edit_item_modal').classList.remove('hidden');
}
</script>
@endpush

@endsection
