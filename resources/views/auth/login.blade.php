@extends('layouts.guest')

@section('title', app()->getLocale() === 'ar' ? 'تسجيل الدخول' : 'Connexion')

@section('content')
<div class="bg-slate-900/90 backdrop-blur-xl border border-slate-800/80 rounded-2xl shadow-2xl p-6 sm:p-8">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-white tracking-tight">
            {{ app()->getLocale() === 'ar' ? 'مرحباً بكم مجدداً' : 'Espace de Connexion' }}
        </h2>
        <p class="text-sm text-slate-400 mt-1">
            {{ app()->getLocale() === 'ar' ? 'قم بتسجيل الدخول للوصول إلى لوحة التحكم' : 'Accédez au système de gestion de votre salle des fêtes' }}
        </p>
    </div>

    @if ($errors->any())
        <div class="mb-5 p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-sm">
            <div class="flex items-center gap-2 font-medium mb-1">
                <svg class="w-4 h-4 text-rose-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span>{{ app()->getLocale() === 'ar' ? 'خطأ في التحقق' : 'Erreur de connexion' }}</span>
            </div>
            <ul class="list-disc list-inside text-xs space-y-0.5 text-rose-200/90">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email -->
        <div>
            <label for="email" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                {{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Adresse E-mail' }}
            </label>
            <div class="relative">
                <input id="email" type="email" name="email" value="{{ old('email', 'owner@palaiselbahia.dz') }}" required autofocus
                    class="w-full px-4 py-3 rounded-xl bg-slate-800/80 border border-slate-700/80 text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
            </div>
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-2">
                <label for="password" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider">
                    {{ app()->getLocale() === 'ar' ? 'كلمة المرور' : 'Mot de passe' }}
                </label>
            </div>
            <div class="relative">
                <input id="password" type="password" name="password" value="password" required
                    class="w-full px-4 py-3 rounded-xl bg-slate-800/80 border border-slate-700/80 text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
            </div>
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between text-xs">
            <label class="flex items-center gap-2 cursor-pointer text-slate-300">
                <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-slate-800 border-slate-700 text-emerald-600 focus:ring-emerald-500 focus:ring-offset-slate-900">
                <span>{{ app()->getLocale() === 'ar' ? 'تذكرني على هذا الجهاز' : 'Se souvenir de moi' }}</span>
            </label>
        </div>

        <!-- Submit Button -->
        <button type="submit"
            class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-semibold text-sm shadow-lg shadow-emerald-900/30 transition transform active:scale-[0.99] flex items-center justify-center gap-2">
            <span>{{ app()->getLocale() === 'ar' ? 'دخول إلى النظام' : 'Se connecter' }}</span>
            <svg class="w-4 h-4 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
            </svg>
        </button>
    </form>

    <!-- Quick Role Switcher for Testing -->
    <div class="mt-8 pt-6 border-t border-slate-800/80">
        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">
            {{ app()->getLocale() === 'ar' ? 'حسابات تجريبية للأدوار الخمسة:' : 'Comptes de démonstration (5 rôles RBAC) :' }}
        </p>
        <div class="grid grid-cols-2 gap-2 text-xs">
            <button type="button" onclick="setRole('owner@palaiselbahia.dz')"
                class="px-2.5 py-2 rounded-lg bg-slate-800/60 hover:bg-slate-800 border border-slate-700/60 text-emerald-400 text-left rtl:text-right font-medium transition">
                👑 {{ app()->getLocale() === 'ar' ? 'المالك (Owner)' : 'Propriétaire (Owner)' }}
            </button>
            <button type="button" onclick="setRole('manager@palaiselbahia.dz')"
                class="px-2.5 py-2 rounded-lg bg-slate-800/60 hover:bg-slate-800 border border-slate-700/60 text-teal-400 text-left rtl:text-right font-medium transition">
                💼 {{ app()->getLocale() === 'ar' ? 'المدير (Manager)' : 'Gérant (Manager)' }}
            </button>
            <button type="button" onclick="setRole('reception@palaiselbahia.dz')"
                class="px-2.5 py-2 rounded-lg bg-slate-800/60 hover:bg-slate-800 border border-slate-700/60 text-amber-400 text-left rtl:text-right font-medium transition">
                🛎️ {{ app()->getLocale() === 'ar' ? 'الاستقبال (Reception)' : 'Réceptionniste' }}
            </button>
            <button type="button" onclick="setRole('compta@palaiselbahia.dz')"
                class="px-2.5 py-2 rounded-lg bg-slate-800/60 hover:bg-slate-800 border border-slate-700/60 text-sky-400 text-left rtl:text-right font-medium transition">
                📊 {{ app()->getLocale() === 'ar' ? 'المحاسب (Accountant)' : 'Comptable' }}
            </button>
            <button type="button" onclick="setRole('events@palaiselbahia.dz')" class="col-span-2 px-2.5 py-2 rounded-lg bg-slate-800/60 hover:bg-slate-800 border border-slate-700/60 text-purple-400 text-left rtl:text-right font-medium transition">
                📋 {{ app()->getLocale() === 'ar' ? 'منسق الحفل (Event Coordinator)' : 'Coordinateur d\'Événements' }}
            </button>
        </div>
        <p class="text-[11px] text-slate-500 mt-2 text-center">
            Mot de passe commun : <code class="text-slate-300">password</code>
        </p>
    </div>
</div>

<script>
function setRole(email) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = 'password';
}
</script>
@endsection
