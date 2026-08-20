@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-10 px-4 sm:px-6 lg:px-8 space-y-8">
    
    <!-- Client Header -->
    <div class="site-card rounded-3xl border border-white/10 p-8 sm:p-10 flex flex-col md:flex-row md:items-center justify-between gap-6 shadow-2xl relative overflow-hidden">
        <div class="space-y-2 relative z-10">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold uppercase tracking-widest px-2.5 py-1 rounded-md bg-theme-primary/20 text-theme-primary border border-[var(--theme-primary)]/30">Client Portal</span>
                <span class="text-xs text-zinc-400">Account #{{ Auth::id() }}</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-serif font-bold text-white">Welcome back, {{ Auth::user()->name }}</h1>
            <p class="text-xs text-zinc-400">Track your active photoshoot milestones, access digital invoices, and manage session bookings.</p>
        </div>

        <div class="flex items-center gap-3 relative z-10 shrink-0">
            <a href="{{ route('home') }}#packages" class="px-5 py-3 rounded-full font-bold text-xs uppercase tracking-wider btn-gold-dynamic shadow-xl shadow-[var(--theme-primary)]/20 flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Book New Session</span>
            </a>
            <a href="{{ route('contact') }}" class="px-4 py-3 rounded-full text-xs font-semibold text-white border border-white/10 hover:bg-white/5 transition flex items-center gap-1.5">
                <i data-lucide="phone" class="w-4 h-4 text-theme-primary"></i>
                <span>Concierge</span>
            </a>
        </div>
    </div>

    <!-- Active Sessions List -->
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-serif font-bold text-white flex items-center gap-2">
                    <i data-lucide="camera" class="w-5 h-5 text-theme-primary"></i>
                    <span>Your Studio Sessions ({{ count($bookings) }})</span>
                </h2>
                <p class="text-xs text-zinc-400 mt-0.5">Real-time status updates from our creative director and post-production team.</p>
            </div>
        </div>

        @if($bookings->isEmpty())
            <div class="site-card rounded-3xl border border-white/10 p-12 text-center text-zinc-400 space-y-4">
                <div class="w-16 h-16 rounded-2xl bg-white/5 mx-auto flex items-center justify-center text-zinc-600">
                    <i data-lucide="calendar-plus" class="w-8 h-8"></i>
                </div>
                <h3 class="text-lg font-bold text-white">No Bookings Yet</h3>
                <p class="text-xs text-zinc-400 max-w-sm mx-auto">You haven't reserved any studio photoshoot sessions yet. Browse our curated tiers to get started.</p>
                <a href="{{ route('home') }}#packages" class="inline-flex items-center gap-2 px-6 py-3 rounded-full btn-gold-dynamic text-xs font-bold uppercase tracking-wider">
                    <span>Explore Packages</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>
        @else
            <div class="space-y-6">
                @foreach($bookings as $b)
                    @php
                        $stepLevels = [
                            'pending' => 1,
                            'progress' => 2,
                            'active' => 3,
                            'next_level' => 4,
                            'completed' => 5,
                            'cancelled' => 0,
                        ];
                        $currentStep = $stepLevels[$b->status] ?? 1;
                    @endphp
                    <div class="site-card rounded-3xl border border-white/10 p-6 sm:p-8 space-y-6 shadow-2xl relative overflow-hidden">
                        
                        <!-- Top Info -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-white/10 pb-6">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-2xl overflow-hidden shrink-0 border border-white/10 bg-black">
                                    <img src="{{ $b->package->image_path ?: 'https://images.unsplash.com/photo-1492691527719-9d1e07e534b4?w=800' }}" alt="{{ $b->package->name ?? 'Package' }}" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-widest text-theme-primary">Booking #{{ $b->id }}</span>
                                    <h3 class="text-xl font-serif font-bold text-white">{{ $b->package->name ?? 'Photoshoot Package' }}</h3>
                                    <div class="text-xs text-zinc-400 mt-0.5 flex items-center gap-3">
                                        <span>Date: <strong class="text-zinc-200">{{ \Carbon\Carbon::parse($b->booking_date)->format('M d, Y') }}</strong></span>
                                        <span>&bull;</span>
                                        <span>Payment: <strong class="text-emerald-400 uppercase font-bold">{{ $b->payment_status }}</strong></span>
                                    </div>
                                </div>
                            </div>

                            <div class="text-right">
                                <div class="text-xs text-zinc-400">Total Investment</div>
                                <div class="text-2xl font-serif font-bold text-emerald-400">{{ $siteSettings['currency_symbol'] ?? '₹' }}{{ number_format($b->amount) }}</div>
                            </div>
                        </div>

                        <!-- 5-Step Visual Lifecycle Tracker -->
                        @if($b->status !== 'cancelled')
                            <div class="space-y-3">
                                <div class="text-xs font-bold uppercase tracking-wider text-zinc-300">Production Workflow Progress:</div>
                                <div class="grid grid-cols-5 gap-2 text-center text-[10px]">
                                    <!-- Step 1 -->
                                    <div class="space-y-1.5">
                                        <div class="h-2 rounded-full {{ $currentStep >= 1 ? 'bg-[var(--theme-primary)]' : 'bg-white/10' }}"></div>
                                        <span class="{{ $currentStep >= 1 ? 'text-white font-bold' : 'text-zinc-500' }}">1. Reserved</span>
                                    </div>
                                    <!-- Step 2 -->
                                    <div class="space-y-1.5">
                                        <div class="h-2 rounded-full {{ $currentStep >= 2 ? 'bg-[var(--theme-primary)]' : 'bg-white/10' }}"></div>
                                        <span class="{{ $currentStep >= 2 ? 'text-white font-bold' : 'text-zinc-500' }}">2. Briefing</span>
                                    </div>
                                    <!-- Step 3 -->
                                    <div class="space-y-1.5">
                                        <div class="h-2 rounded-full {{ $currentStep >= 3 ? 'bg-[var(--theme-primary)]' : 'bg-white/10' }}"></div>
                                        <span class="{{ $currentStep >= 3 ? 'text-white font-bold' : 'text-zinc-500' }}">3. In Studio</span>
                                    </div>
                                    <!-- Step 4 -->
                                    <div class="space-y-1.5">
                                        <div class="h-2 rounded-full {{ $currentStep >= 4 ? 'bg-[var(--theme-primary)]' : 'bg-white/10' }}"></div>
                                        <span class="{{ $currentStep >= 4 ? 'text-white font-bold' : 'text-zinc-500' }}">4. Retouching</span>
                                    </div>
                                    <!-- Step 5 -->
                                    <div class="space-y-1.5">
                                        <div class="h-2 rounded-full {{ $currentStep >= 5 ? 'bg-emerald-400' : 'bg-white/10' }}"></div>
                                        <span class="{{ $currentStep >= 5 ? 'text-emerald-300 font-bold' : 'text-zinc-500' }}">5. Master Delivery</span>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="p-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-xs">
                                This booking has been cancelled. If you have any questions or require a refund review, please message concierge support.
                            </div>
                        @endif

                        <!-- Notes & Receipt Details -->
                        <div class="p-4 rounded-2xl bg-white/5 border border-white/5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs">
                            <div class="space-y-1">
                                <div class="text-zinc-400">
                                    <span>Txn Reference: </span>
                                    <span class="text-cyan-400 font-mono font-bold">{{ $b->latestTransaction->transaction_ref ?? ('TRX-' . $b->id) }}</span>
                                </div>
                                <div class="text-zinc-400">Order ID: <span class="text-white font-mono">{{ $b->razorpay_order_id ?? 'N/A' }}</span></div>
                                <div class="text-zinc-400">Payment ID: <span class="text-emerald-400 font-mono">{{ $b->razorpay_payment_id ?? 'Pending / Simulated' }}</span></div>
                                @if($b->notes)
                                    <div class="text-zinc-300 italic pt-1">Note: &ldquo;{{ $b->notes }}&rdquo;</div>
                                @endif
                            </div>

                            <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/15 text-white border border-white/10 flex items-center gap-2 transition self-start sm:self-auto">
                                <i data-lucide="printer" class="w-4 h-4 text-theme-primary"></i>
                                <span>Print Receipt</span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Client Payment Transactions Ledger -->
    @if(isset($transactions) && $transactions->isNotEmpty())
        <div class="space-y-4 pt-4 border-t border-white/10">
            <h3 class="text-lg font-serif font-bold text-white flex items-center gap-2">
                <i data-lucide="receipt" class="w-5 h-5 text-emerald-400"></i>
                <span>Payment & Transaction History ({{ count($transactions) }})</span>
            </h3>

            <div class="site-card rounded-2xl border border-white/10 overflow-hidden shadow-xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-zinc-300">
                        <thead class="bg-white/5 border-b border-white/10 text-[11px] uppercase tracking-wider text-zinc-400 font-semibold">
                            <tr>
                                <th class="p-3.5">Transaction Ref</th>
                                <th class="p-3.5">Package</th>
                                <th class="p-3.5">Amount</th>
                                <th class="p-3.5">Status</th>
                                <th class="p-3.5">Payment Method</th>
                                <th class="p-3.5">Date</th>
                                <th class="p-3.5 text-right">Payment ID</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 font-mono">
                            @foreach($transactions as $txn)
                                @php $badge = $txn->status_badge; @endphp
                                <tr class="hover:bg-white/[0.02]">
                                    <td class="p-3.5 text-white font-bold">{{ $txn->transaction_ref }}</td>
                                    <td class="p-3.5 font-sans">{{ $txn->booking->package->name ?? 'Photoshoot' }}</td>
                                    <td class="p-3.5 text-emerald-400 font-bold font-sans">{{ $siteSettings['currency_symbol'] ?? '₹' }}{{ number_format($txn->amount) }}</td>
                                    <td class="p-3.5 font-sans">
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase border {{ $badge['bg'] }} {{ $badge['text'] }} {{ $badge['border'] }}">
                                            {{ $badge['label'] }}
                                        </span>
                                    </td>
                                    <td class="p-3.5 uppercase text-zinc-400 font-sans text-[11px]">{{ $txn->payment_method ?: 'Razorpay' }}</td>
                                    <td class="p-3.5 text-zinc-400 font-sans text-[11px]">{{ $txn->created_at->format('M j, Y') }}</td>
                                    <td class="p-3.5 text-right text-zinc-400 text-[11px]">{{ $txn->razorpay_payment_id ?: 'Pending' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
