@extends('layouts.app')

@section('title', $title)
@section('page_title', $title)

@section('content')
<div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-8 text-center max-w-xl mx-auto my-12">
    <div class="w-16 h-16 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center mx-auto mb-4 border border-emerald-500/20">
        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
        </svg>
    </div>
    <h3 class="text-xl font-bold text-white mb-2">{{ $title }}</h3>
    <p class="text-xs text-slate-400 mb-6">
        Module planifié pour : <span class="text-emerald-400 font-semibold">{{ $phase }}</span>
    </p>
    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold border border-slate-700 transition">
        <span>← Retour au tableau de bord</span>
    </a>
</div>
@endsection
