@extends('layouts.app')

@section('content')
<div style="background-color:#6a299c;" class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8" x-data="{ activeTab: 'overview', editingPackage: null }">

    <!-- Top Admin Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-8 border-b border-white/10">
        <div>
            <div class="flex items-center gap-3">
                <span class="px-2.5 py-1 text-xs font-bold uppercase tracking-wider rounded-md bg-purple-500/20 text-purple-300 border border-purple-500/30">Super Admin Console</span>
                <span class="text-xs text-zinc-400">Live Studio Engine</span>
            </div>
            <h1 class="text-3xl font-serif font-bold text-white mt-2">Executive Studio Dashboard</h1>
            <p class="text-sm text-zinc-400 mt-1">Manage bookings, pricing tiers, portfolio showcase, journal stories, and customize your live theme colors in real time.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('home') }}" target="_blank" class="px-4 py-2 text-xs font-semibold text-zinc-300 hover:text-white bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl flex items-center gap-2 transition">
                <i data-lucide="external-link" class="w-4 h-4 text-theme-primary"></i>
                <span>View Live Site</span>
            </a>
            <a href="{{ route('sitemap.xml') }}" target="_blank" class="px-4 py-2 text-xs font-semibold text-zinc-300 hover:text-white bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl flex items-center gap-2 transition">
                <i data-lucide="globe" class="w-4 h-4 text-emerald-400"></i>
                <span>Sitemap XML</span>
            </a>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div style="background-color:#5b1690;" class="flex items-center gap-2 overflow-x-auto py-6 border-b border-white/5 scrollbar-none">
        <button @click="activeTab = 'overview'" :class="activeTab === 'overview' ? 'bg-[var(--theme-primary)] text-black font-bold shadow-lg shadow-[var(--theme-primary)]/20' : 'text-zinc-400 hover:text-white hover:bg-white/5'" class="px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 shrink-0 flex items-center gap-2">
            <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
            <span>Overview</span>
        </button>

        <button @click="activeTab = 'theme_settings'" :class="activeTab === 'theme_settings' ? 'bg-[var(--theme-primary)] text-black font-bold shadow-lg shadow-[var(--theme-primary)]/20' : 'text-zinc-400 hover:text-white hover:bg-white/5'" class="px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 shrink-0 flex items-center gap-2">
            <i data-lucide="palette" class="w-4 h-4 text-pink-400"></i>
            <span>Theme & Colors</span>
        </button>

        <button @click="activeTab = 'site_settings'" :class="activeTab === 'site_settings' ? 'bg-[var(--theme-primary)] text-black font-bold shadow-lg shadow-[var(--theme-primary)]/20' : 'text-zinc-400 hover:text-white hover:bg-white/5'" class="px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 shrink-0 flex items-center gap-2">
            <i data-lucide="settings" class="w-4 h-4"></i>
            <span>Site & SEO Config</span>
        </button>

        <button @click="activeTab = 'bookings'" :class="activeTab === 'bookings' ? 'bg-[var(--theme-primary)] text-black font-bold shadow-lg shadow-[var(--theme-primary)]/20' : 'text-zinc-400 hover:text-white hover:bg-white/5'" class="px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 shrink-0 flex items-center gap-2">
            <i data-lucide="calendar" class="w-4 h-4 text-cyan-400"></i>
            <span>Bookings ({{ count($bookings) }})</span>
        </button>

        <button @click="activeTab = 'packages'" :class="activeTab === 'packages' ? 'bg-[var(--theme-primary)] text-black font-bold shadow-lg shadow-[var(--theme-primary)]/20' : 'text-zinc-400 hover:text-white hover:bg-white/5'" class="px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 shrink-0 flex items-center gap-2">
            <i data-lucide="layers" class="w-4 h-4 text-amber-400"></i>
            <span>Packages ({{ count($packages) }})</span>
        </button>

        <button @click="activeTab = 'gallery'" :class="activeTab === 'gallery' ? 'bg-[var(--theme-primary)] text-black font-bold shadow-lg shadow-[var(--theme-primary)]/20' : 'text-zinc-400 hover:text-white hover:bg-white/5'" class="px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 shrink-0 flex items-center gap-2">
            <i data-lucide="image" class="w-4 h-4 text-emerald-400"></i>
            <span>Gallery ({{ count($gallery) }})</span>
        </button>

        <button @click="activeTab = 'blogs'" :class="activeTab === 'blogs' ? 'bg-[var(--theme-primary)] text-black font-bold shadow-lg shadow-[var(--theme-primary)]/20' : 'text-zinc-400 hover:text-white hover:bg-white/5'" class="px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 shrink-0 flex items-center gap-2">
            <i data-lucide="book-open" class="w-4 h-4 text-blue-400"></i>
            <span>Journal Articles ({{ count($blogs) }})</span>
        </button>

        <button @click="activeTab = 'messages'" :class="activeTab === 'messages' ? 'bg-[var(--theme-primary)] text-black font-bold shadow-lg shadow-[var(--theme-primary)]/20' : 'text-zinc-400 hover:text-white hover:bg-white/5'" class="px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 shrink-0 flex items-center gap-2">
            <i data-lucide="mail" class="w-4 h-4 text-violet-400"></i>
            <span>Inquiries ({{ count($messages) }})</span>
            @if($unreadMessagesCount > 0)
                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
            @endif
        </button>

        <button @click="activeTab = 'vendors'" :class="activeTab === 'vendors' ? 'bg-[var(--theme-primary)] text-black font-bold shadow-lg shadow-[var(--theme-primary)]/20' : 'text-zinc-400 hover:text-white hover:bg-white/5'" class="px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 shrink-0 flex items-center gap-2">
            <i data-lucide="camera" class="w-4 h-4 text-teal-400"></i>
            <span>Photographers ({{ count($vendors) }})</span>
        </button>
    </div>

    <!-- TAB 1: OVERVIEW -->
    <div x-show="activeTab === 'overview'" class="mt-8 space-y-8">
        <!-- KPI Metrics Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="site-card p-6 rounded-2xl border border-white/10 relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-emerald-500/10 rounded-full blur-xl pointer-events-none group-hover:bg-emerald-500/20 transition-all"></div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Gross Revenue</span>
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                        <i data-lucide="indian-rupee" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="text-3xl font-bold text-white mt-4">{{ $siteSettings['currency_symbol'] ?? '₹' }}{{ number_format($totalEarnings) }}</div>
                <div class="text-xs text-emerald-400 flex items-center gap-1 mt-2">
                    <i data-lucide="trending-up" class="w-3.5 h-3.5"></i>
                    <span>Razorpay Captured Total</span>
                </div>
            </div>

            <div class="site-card p-6 rounded-2xl border border-white/10 relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-theme-primary/10 rounded-full blur-xl pointer-events-none group-hover:bg-theme-primary/20 transition-all"></div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Booked Sessions</span>
                    <div class="w-10 h-10 rounded-xl bg-theme-primary/10 border border-theme-primary/20 flex items-center justify-center text-theme-primary">
                        <i data-lucide="calendar-check-2" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="text-3xl font-bold text-white mt-4">{{ $totalBookings }}</div>
                <div class="text-xs text-zinc-400 mt-2">Across all packages</div>
            </div>

            <div class="site-card p-6 rounded-2xl border border-white/10 relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-cyan-500/10 rounded-full blur-xl pointer-events-none group-hover:bg-cyan-500/20 transition-all"></div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Studio Visitors</span>
                    <div class="w-10 h-10 rounded-xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center text-cyan-400">
                        <i data-lucide="users" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="text-3xl font-bold text-white mt-4">{{ number_format($totalVisitors) }}</div>
                <div class="text-xs text-cyan-400 flex items-center gap-1 mt-2">
                    <i data-lucide="activity" class="w-3.5 h-3.5"></i>
                    <span>Real unique IP captures</span>
                </div>
            </div>

            <div class="site-card p-6 rounded-2xl border border-white/10 relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-purple-500/10 rounded-full blur-xl pointer-events-none group-hover:bg-purple-500/20 transition-all"></div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Live Assets</span>
                    <div class="w-10 h-10 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400">
                        <i data-lucide="folder-kanban" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="text-3xl font-bold text-white mt-4">{{ $totalPackages + count($gallery) + count($blogs) }}</div>
                <div class="text-xs text-zinc-400 mt-2">{{ $totalPackages }} Pkgs • {{ count($gallery) }} Photos • {{ count($blogs) }} Blogs</div>
            </div>
        </div>

        <!-- Recent Activity Sections -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Recent Bookings -->
            <div class="lg:col-span-2 site-card rounded-2xl border border-white/10 p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <i data-lucide="clock" class="w-4 h-4 text-theme-primary"></i>
                        <span>Recent Client Bookings</span>
                    </h3>
                    <button @click="activeTab = 'bookings'" class="text-xs text-theme-primary hover:underline">View All &rarr;</button>
                </div>

                @if($bookings->isEmpty())
                    <div class="py-12 text-center text-zinc-500 text-sm">No bookings recorded yet.</div>
                @else
                    <div class="divide-y divide-white/5 overflow-x-auto">
                        <table class="w-full text-left text-xs text-zinc-300">
                            <thead>
                                <tr class="text-zinc-500 border-b border-white/5 pb-2">
                                    <th class="py-2.5 font-semibold">Client</th>
                                    <th class="py-2.5 font-semibold">Package</th>
                                    <th class="py-2.5 font-semibold">Shoot Date</th>
                                    <th class="py-2.5 font-semibold">Amount</th>
                                    <th class="py-2.5 font-semibold">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                @foreach($bookings->take(6) as $b)
                                    <tr class="hover:bg-white/5 transition">
                                        <td class="py-3 font-medium text-white">{{ $b->user->name ?? 'Guest Client' }}</td>
                                        <td class="py-3 text-zinc-300">{{ $b->package->name ?? 'Custom Session' }}</td>
                                        <td class="py-3 text-zinc-400">{{ \Carbon\Carbon::parse($b->booking_date)->format('M d, Y') }}</td>
                                        <td class="py-3 font-semibold text-emerald-400">{{ $siteSettings['currency_symbol'] ?? '₹' }}{{ number_format($b->amount) }}</td>
                                        <td class="py-3">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                                                {{ $b->status === 'completed' ? 'bg-emerald-500/20 text-emerald-300' : '' }}
                                                {{ $b->status === 'active' ? 'bg-cyan-500/20 text-cyan-300' : '' }}
                                                {{ $b->status === 'progress' ? 'bg-amber-500/20 text-amber-300' : '' }}
                                                {{ $b->status === 'next_level' ? 'bg-purple-500/20 text-purple-300' : '' }}
                                                {{ $b->status === 'pending' ? 'bg-zinc-500/20 text-zinc-300' : '' }}
                                                {{ $b->status === 'cancelled' ? 'bg-rose-500/20 text-rose-300' : '' }}
                                            ">
                                                {{ str_replace('_', ' ', $b->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Recent Payments & Inquiries Widget -->
            <div class="space-y-6">
                <!-- Recent Inquiries -->
                <div class="site-card rounded-2xl border border-white/10 p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-bold text-white flex items-center gap-2">
                            <i data-lucide="message-square" class="w-4 h-4 text-violet-400"></i>
                            <span>Recent Inquiries</span>
                        </h3>
                        <button @click="activeTab = 'messages'" class="text-xs text-violet-400 hover:underline">Inbox &rarr;</button>
                    </div>

                    @if($messages->isEmpty())
                        <div class="py-6 text-center text-zinc-500 text-xs">No client inquiries yet.</div>
                    @else
                        <div class="space-y-3">
                            @foreach($messages->take(4) as $msg)
                                <div class="p-3 rounded-xl bg-white/5 border border-white/5 space-y-1 text-xs">
                                    <div class="flex items-center justify-between">
                                        <span class="font-semibold text-white">{{ $msg->name }}</span>
                                        <span class="text-[10px] text-zinc-500">{{ $msg->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-zinc-400 font-medium truncate">{{ $msg->subject }}</p>
                                    <p class="text-zinc-500 line-clamp-1 text-[11px]">{{ $msg->message }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Fast Actions -->
                <div class="site-card rounded-2xl border border-white/10 p-6 space-y-3">
                    <h4 class="text-xs font-bold text-zinc-400 uppercase tracking-wider">Quick Actions</h4>
                    <div class="grid grid-cols-2 gap-2">
                        <button @click="activeTab = 'theme_settings'" class="p-3 rounded-xl bg-white/5 hover:bg-white/10 border border-white/5 text-left transition flex flex-col gap-1">
                            <i data-lucide="palette" class="w-4 h-4 text-pink-400"></i>
                            <span class="text-xs font-semibold text-white">Color Pickers</span>
                        </button>
                        <button @click="activeTab = 'packages'" class="p-3 rounded-xl bg-white/5 hover:bg-white/10 border border-white/5 text-left transition flex flex-col gap-1">
                            <i data-lucide="plus-circle" class="w-4 h-4 text-amber-400"></i>
                            <span class="text-xs font-semibold text-white">New Package</span>
                        </button>
                        <button @click="activeTab = 'gallery'" class="p-3 rounded-xl bg-white/5 hover:bg-white/10 border border-white/5 text-left transition flex flex-col gap-1">
                            <i data-lucide="upload" class="w-4 h-4 text-emerald-400"></i>
                            <span class="text-xs font-semibold text-white">Add Photo</span>
                        </button>
                        <button @click="activeTab = 'blogs'" class="p-3 rounded-xl bg-white/5 hover:bg-white/10 border border-white/5 text-left transition flex flex-col gap-1">
                            <i data-lucide="pen-tool" class="w-4 h-4 text-blue-400"></i>
                            <span class="text-xs font-semibold text-white">Post Article</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 2: DYNAMIC THEME & COLOR CUSTOMIZER -->
    <div x-show="activeTab === 'theme_settings'" class="mt-8 space-y-8">
        <!-- Preset Theme Switcher -->
        <div class="site-card rounded-2xl border border-white/10 p-6 space-y-4">
            <div>
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i data-lucide="sparkles" class="w-5 h-5 text-amber-400"></i>
                    <span>One-Click Aesthetic Presets</span>
                </h3>
                <p class="text-xs text-zinc-400 mt-1">Instantly switch between curated palettes designed for luxury photoshoot studios.</p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 pt-2">
                <!-- Preset 1: Luxury Gold -->
                <form method="POST" action="{{ route('admin.settings.preset') }}">
                    @csrf
                    <input type="hidden" name="preset" value="luxury_gold">
                    <button type="submit" class="w-full p-3 rounded-xl border border-amber-500/30 bg-[#07060a] hover:scale-105 transition-all text-left group">
                        <div class="flex items-center gap-1.5 mb-2">
                            <span class="w-3.5 h-3.5 rounded-full bg-[#E5C158]"></span>
                            <span class="w-3.5 h-3.5 rounded-full bg-[#8B5CF6]"></span>
                            <span class="w-3.5 h-3.5 rounded-full bg-[#07060a] border border-white/20"></span>
                        </div>
                        <span class="text-xs font-bold text-white group-hover:text-amber-400 block">Luxury Gold</span>
                        <span class="text-[10px] text-zinc-500">Default Obsidian</span>
                    </button>
                </form>

                <!-- Preset 2: Obsidian Neon -->
                <form method="POST" action="{{ route('admin.settings.preset') }}">
                    @csrf
                    <input type="hidden" name="preset" value="obsidian_neon">
                    <button type="submit" class="w-full p-3 rounded-xl border border-cyan-500/30 bg-[#05070c] hover:scale-105 transition-all text-left group">
                        <div class="flex items-center gap-1.5 mb-2">
                            <span class="w-3.5 h-3.5 rounded-full bg-[#00F0FF]"></span>
                            <span class="w-3.5 h-3.5 rounded-full bg-[#FF0055]"></span>
                            <span class="w-3.5 h-3.5 rounded-full bg-[#05070c] border border-white/20"></span>
                        </div>
                        <span class="text-xs font-bold text-white group-hover:text-cyan-400 block">Obsidian Neon</span>
                        <span class="text-[10px] text-zinc-500">Cyberpunk Cyan</span>
                    </button>
                </form>

                <!-- Preset 3: Royal Emerald -->
                <form method="POST" action="{{ route('admin.settings.preset') }}">
                    @csrf
                    <input type="hidden" name="preset" value="royal_emerald">
                    <button type="submit" class="w-full p-3 rounded-xl border border-emerald-500/30 bg-[#040d0a] hover:scale-105 transition-all text-left group">
                        <div class="flex items-center gap-1.5 mb-2">
                            <span class="w-3.5 h-3.5 rounded-full bg-[#10B981]"></span>
                            <span class="w-3.5 h-3.5 rounded-full bg-[#F59E0B]"></span>
                            <span class="w-3.5 h-3.5 rounded-full bg-[#040d0a] border border-white/20"></span>
                        </div>
                        <span class="text-xs font-bold text-white group-hover:text-emerald-400 block">Royal Emerald</span>
                        <span class="text-[10px] text-zinc-500">Verve Deep Green</span>
                    </button>
                </form>

                <!-- Preset 4: Rose Champagne -->
                <form method="POST" action="{{ route('admin.settings.preset') }}">
                    @csrf
                    <input type="hidden" name="preset" value="rose_champagne">
                    <button type="submit" class="w-full p-3 rounded-xl border border-pink-500/30 bg-[#0c0709] hover:scale-105 transition-all text-left group">
                        <div class="flex items-center gap-1.5 mb-2">
                            <span class="w-3.5 h-3.5 rounded-full bg-[#F472B6]"></span>
                            <span class="w-3.5 h-3.5 rounded-full bg-[#FBBF24]"></span>
                            <span class="w-3.5 h-3.5 rounded-full bg-[#0c0709] border border-white/20"></span>
                        </div>
                        <span class="text-xs font-bold text-white group-hover:text-pink-400 block">Rose Champagne</span>
                        <span class="text-[10px] text-zinc-500">High Fashion Pink</span>
                    </button>
                </form>

                <!-- Preset 5: Cyberpunk Violet -->
                <form method="POST" action="{{ route('admin.settings.preset') }}">
                    @csrf
                    <input type="hidden" name="preset" value="cyberpunk_violet">
                    <button type="submit" class="w-full p-3 rounded-xl border border-purple-500/30 bg-[#090510] hover:scale-105 transition-all text-left group">
                        <div class="flex items-center gap-1.5 mb-2">
                            <span class="w-3.5 h-3.5 rounded-full bg-[#A855F7]"></span>
                            <span class="w-3.5 h-3.5 rounded-full bg-[#EC4899]"></span>
                            <span class="w-3.5 h-3.5 rounded-full bg-[#090510] border border-white/20"></span>
                        </div>
                        <span class="text-xs font-bold text-white group-hover:text-purple-400 block">Electric Violet</span>
                        <span class="text-[10px] text-zinc-500">Deep Purple Mode</span>
                    </button>
                </form>

                <!-- Preset 6: Minimal Clean Light -->
                <form method="POST" action="{{ route('admin.settings.preset') }}">
                    @csrf
                    <input type="hidden" name="preset" value="minimal_light">
                    <button type="submit" class="w-full p-3 rounded-xl border border-zinc-500/30 bg-[#F8FAFC] hover:scale-105 transition-all text-left group">
                        <div class="flex items-center gap-1.5 mb-2">
                            <span class="w-3.5 h-3.5 rounded-full bg-[#18181B]"></span>
                            <span class="w-3.5 h-3.5 rounded-full bg-[#6366F1]"></span>
                            <span class="w-3.5 h-3.5 rounded-full bg-[#FFFFFF] border border-black/20"></span>
                        </div>
                        <span class="text-xs font-bold text-zinc-900 group-hover:text-indigo-600 block">Clean Light</span>
                        <span class="text-[10px] text-zinc-500">Editorial Monochrome</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Custom Color Pickers Form -->
        <form method="POST" action="{{ route('admin.settings.save') }}" class="site-card rounded-2xl border border-white/10 p-6 md:p-8 space-y-6">
            @csrf
            <div>
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i data-lucide="palette" class="w-5 h-5 text-pink-400"></i>
                    <span>Custom Color Palette & Dynamic Background Engine</span>
                </h3>
                <p class="text-xs text-zinc-400 mt-1">Fine-tune every visual component on your studio website. Changes are immediately applied across all landing pages, booking views, and headers.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Website Background Color -->
                <div class="space-y-2 p-4 rounded-xl bg-white/5 border border-white/5">
                    <label class="text-xs font-semibold text-zinc-300 block">Website Background Color (Entire Site)</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="bg_color" value="{{ $siteSettings['bg_color'] ?? '#07060a' }}" class="w-10 h-10 rounded-lg cursor-pointer bg-transparent border-0">
                        <input type="text" name="bg_color" value="{{ $siteSettings['bg_color'] ?? '#07060a' }}" class="flex-1 px-3 py-2 text-xs rounded-lg bg-black/40 border border-white/10 text-white font-mono uppercase">
                    </div>
                    <span class="text-[10px] text-zinc-500">Controls the global body & canvas background.</span>
                </div>

                <!-- Primary Theme Color -->
                <div class="space-y-2 p-4 rounded-xl bg-white/5 border border-white/5">
                    <label class="text-xs font-semibold text-zinc-300 block">Primary Theme / Brand Color</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="primary_color" value="{{ $siteSettings['primary_color'] ?? '#E5C158' }}" class="w-10 h-10 rounded-lg cursor-pointer bg-transparent border-0">
                        <input type="text" name="primary_color" value="{{ $siteSettings['primary_color'] ?? '#E5C158' }}" class="flex-1 px-3 py-2 text-xs rounded-lg bg-black/40 border border-white/10 text-white font-mono uppercase">
                    </div>
                    <span class="text-[10px] text-zinc-500">Used for primary buttons, highlights, badges & icons.</span>
                </div>

                <!-- Primary Hover Color -->
                <div class="space-y-2 p-4 rounded-xl bg-white/5 border border-white/5">
                    <label class="text-xs font-semibold text-zinc-300 block">Primary Hover Tone</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="primary_hover" value="{{ $siteSettings['primary_hover'] ?? '#F3D88B' }}" class="w-10 h-10 rounded-lg cursor-pointer bg-transparent border-0">
                        <input type="text" name="primary_hover" value="{{ $siteSettings['primary_hover'] ?? '#F3D88B' }}" class="flex-1 px-3 py-2 text-xs rounded-lg bg-black/40 border border-white/10 text-white font-mono uppercase">
                    </div>
                    <span class="text-[10px] text-zinc-500">Tone applied on button hover and link focus states.</span>
                </div>

                <!-- Card / Surface Background Color -->
                <div class="space-y-2 p-4 rounded-xl bg-white/5 border border-white/5">
                    <label class="text-xs font-semibold text-zinc-300 block">Cards & Glass Surface Background</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="card_bg_color" value="{{ $siteSettings['card_bg_color'] ?? '#12101b' }}" class="w-10 h-10 rounded-lg cursor-pointer bg-transparent border-0">
                        <input type="text" name="card_bg_color" value="{{ $siteSettings['card_bg_color'] ?? '#12101b' }}" class="flex-1 px-3 py-2 text-xs rounded-lg bg-black/40 border border-white/10 text-white font-mono uppercase">
                    </div>
                    <span class="text-[10px] text-zinc-500">Background for pricing cards, navbar, and modals.</span>
                </div>

                <!-- Accent Glow Color -->
                <div class="space-y-2 p-4 rounded-xl bg-white/5 border border-white/5">
                    <label class="text-xs font-semibold text-zinc-300 block">Accent / Neon Glow Color</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="accent_color" value="{{ $siteSettings['accent_color'] ?? '#8B5CF6' }}" class="w-10 h-10 rounded-lg cursor-pointer bg-transparent border-0">
                        <input type="text" name="accent_color" value="{{ $siteSettings['accent_color'] ?? '#8B5CF6' }}" class="flex-1 px-3 py-2 text-xs rounded-lg bg-black/40 border border-white/10 text-white font-mono uppercase">
                    </div>
                    <span class="text-[10px] text-zinc-500">Secondary gradient tone & ambient aura effect.</span>
                </div>

                <!-- Text & Typography Color -->
                <div class="space-y-2 p-4 rounded-xl bg-white/5 border border-white/5">
                    <label class="text-xs font-semibold text-zinc-300 block">Main Text & Typography Color</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="text_color" value="{{ $siteSettings['text_color'] ?? '#F3F4F6' }}" class="w-10 h-10 rounded-lg cursor-pointer bg-transparent border-0">
                        <input type="text" name="text_color" value="{{ $siteSettings['text_color'] ?? '#F3F4F6' }}" class="flex-1 px-3 py-2 text-xs rounded-lg bg-black/40 border border-white/10 text-white font-mono uppercase">
                    </div>
                    <span class="text-[10px] text-zinc-500">Headings, paragraph body copy and labels.</span>
                </div>
            </div>

            <!-- Live Color Preview Box -->
            <div class="p-6 rounded-2xl border border-white/10 space-y-3" style="background-color: {{ $siteSettings['card_bg_color'] ?? '#12101b' }}">
                <div class="flex items-center justify-between">
                    <span class="text-xs uppercase tracking-wider text-zinc-400 font-semibold">Live Visual Preview Box</span>
                    <span class="px-3 py-1 text-xs font-bold rounded-full" style="background-color: {{ $siteSettings['primary_color'] ?? '#E5C158' }}; color: #000;">
                        Active Primary
                    </span>
                </div>
                <h4 class="text-xl font-serif font-bold" style="color: {{ $siteSettings['text_color'] ?? '#F3F4F6' }}">
                    Capturing High-Art Photography with Perfection
                </h4>
                <p class="text-xs text-zinc-400 max-w-xl leading-relaxed">
                    This sample card demonstrates how your chosen background, card surface, and primary theme colors interact together on the client's screen.
                </p>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="px-6 py-3 rounded-xl btn-gold-dynamic text-sm font-bold flex items-center gap-2 shadow-xl shadow-[var(--theme-primary)]/20">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Save Dynamic Theme Colors</span>
                </button>
            </div>
        </form>
    </div>

    <!-- TAB 3: SITE & SEO CONFIGURATION -->
    <div x-show="activeTab === 'site_settings'" class="mt-8">
        <form method="POST" action="{{ route('admin.settings.save') }}" class="site-card rounded-2xl border border-white/10 p-6 md:p-8 space-y-8">
            @csrf

            <!-- Studio Identity & Branding -->
            <div class="space-y-4">
                <h3 class="text-base font-bold text-white flex items-center gap-2 border-b border-white/10 pb-3">
                    <i data-lucide="shield-check" class="w-5 h-5 text-theme-primary"></i>
                    <span>Studio Identity & Global Branding</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-zinc-300">Studio Name</label>
                        <input type="text" name="site_name" value="{{ $siteSettings['site_name'] ?? 'Middukhera Studio & Productions' }}" class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:border-theme-primary">
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-zinc-300">Tagline / Mission Quote</label>
                        <input type="text" name="site_tagline" value="{{ $siteSettings['site_tagline'] ?? 'Capturing Eternal Elegance & High-Fashion Artistry' }}" class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:border-theme-primary">
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-zinc-300">Logo Primary Text</label>
                        <input type="text" name="logo_text" value="{{ $siteSettings['logo_text'] ?? 'LUMINA' }}" class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm font-serif tracking-wider focus:border-theme-primary">
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-zinc-300">Logo Subtitle Tag</label>
                        <input type="text" name="logo_sub" value="{{ $siteSettings['logo_sub'] ?? 'STUDIO' }}" class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm tracking-widest uppercase focus:border-theme-primary">
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-zinc-300">Currency Symbol</label>
                        <input type="text" name="currency_symbol" value="{{ $siteSettings['currency_symbol'] ?? '₹' }}" class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:border-theme-primary">
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-zinc-300">Studio Operating Hours</label>
                        <input type="text" name="operating_hours" value="{{ $siteSettings['operating_hours'] ?? 'Mon - Sun: 09:00 AM - 09:00 PM IST' }}" class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:border-theme-primary">
                    </div>
                </div>
            </div>

            <!-- Landing Page Hero Customizer -->
            <div class="space-y-4">
                <h3 class="text-base font-bold text-white flex items-center gap-2 border-b border-white/10 pb-3">
                    <i data-lucide="image" class="w-5 h-5 text-amber-400"></i>
                    <span>Landing Page Hero Banner Config</span>
                </h3>

                <div class="space-y-4">
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-zinc-300">Hero Pill Badge</label>
                        <input type="text" name="hero_badge" value="{{ $siteSettings['hero_badge'] ?? '✨ INDIA’S PREMIER LUXURY PRODUCTION HOUSE' }}" class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:border-theme-primary">
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-zinc-300">Hero Main Title (H1)</label>
                        <input type="text" name="hero_title" value="{{ $siteSettings['hero_title'] ?? 'Transforming Ephemeral Moments Into Timeless High-Art Masterpieces' }}" class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:border-theme-primary">
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-zinc-300">Hero Subtitle Paragraph</label>
                        <textarea name="hero_subtitle" rows="2" class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:border-theme-primary">{{ $siteSettings['hero_subtitle'] ?? 'Bespoke couture portraiture, celebrity fashion editorials, and cinematic wedding archives captured with world-class medium-format clarity.' }}</textarea>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-zinc-300">Hero Background Backdrop Image URL</label>
                        <input type="url" name="hero_bg_image" value="{{ $siteSettings['hero_bg_image'] ?? 'https://images.unsplash.com/photo-1542038784456-1ea8e935640e?q=80&w=1920&auto=format&fit=crop' }}" class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:border-theme-primary">
                    </div>
                </div>
            </div>

            <!-- SEO & Meta Configuration -->
            <div class="space-y-4">
                <h3 class="text-base font-bold text-white flex items-center gap-2 border-b border-white/10 pb-3">
                    <i data-lucide="search" class="w-5 h-5 text-cyan-400"></i>
                    <span>SEO Meta Tags & Google Schema</span>
                </h3>

                <div class="space-y-4">
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-zinc-300">Meta Title</label>
                        <input type="text" name="meta_title" value="{{ $siteSettings['meta_title'] ?? 'Middukhera Studio | Premier Luxury Photoshoot & Production House' }}" class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:border-theme-primary">
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-zinc-300">Meta Description (150-160 characters recommended)</label>
                        <textarea name="meta_description" rows="2" class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:border-theme-primary">{{ $siteSettings['meta_description'] ?? 'Experience India\'s finest photoshoot studio. Luxury wedding cinematography, high-fashion editorials, signature portraits, and commercial campaigns with instant Razorpay booking.' }}</textarea>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-zinc-300">Meta Keywords (Comma separated)</label>
                        <input type="text" name="meta_keywords" value="{{ $siteSettings['meta_keywords'] ?? 'photoshoot studio, luxury photography, wedding photoshoot, fashion editorial, portrait studio, Mumbai photography, Razorpay studio booking' }}" class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:border-theme-primary">
                    </div>
                </div>
            </div>

            <!-- Concierge Contact & Socials -->
            <div class="space-y-4">
                <h3 class="text-base font-bold text-white flex items-center gap-2 border-b border-white/10 pb-3">
                    <i data-lucide="phone-call" class="w-5 h-5 text-emerald-400"></i>
                    <span>Contact Info & Social Networks</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-zinc-300">Concierge Email</label>
                        <input type="email" name="contact_email" value="{{ $siteSettings['contact_email'] ?? 'concierge@luminastudio.com' }}" class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:border-theme-primary">
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-zinc-300">Phone Number</label>
                        <input type="text" name="contact_phone" value="{{ $siteSettings['contact_phone'] ?? '+91 98765 43210' }}" class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:border-theme-primary">
                    </div>

                    <div class="space-y-1 md:col-span-2">
                        <label class="text-xs font-semibold text-zinc-300">Studio Physical Address</label>
                        <input type="text" name="contact_address" value="{{ $siteSettings['contact_address'] ?? 'Suite 402, Signature Art Tower, Fashion Boulevard, Mumbai, MH 400050' }}" class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:border-theme-primary">
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-zinc-300">Instagram Profile Link</label>
                        <input type="url" name="social_instagram" value="{{ $siteSettings['social_instagram'] ?? 'https://instagram.com' }}" class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:border-theme-primary">
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-zinc-300">WhatsApp Number (e.g. 919876543210)</label>
                        <input type="text" name="social_whatsapp" value="{{ $siteSettings['social_whatsapp'] ?? '919876543210' }}" class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:border-theme-primary">
                    </div>
                </div>
            </div>

            <!-- Razorpay Gateway & Simulation Config -->
            <div class="space-y-4">
                <h3 class="text-base font-bold text-white flex items-center gap-2 border-b border-white/10 pb-3">
                    <i data-lucide="credit-card" class="w-5 h-5 text-indigo-400"></i>
                    <span>Razorpay Payment Gateway Integration</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-zinc-300">Razorpay Key ID</label>
                        <input type="text" name="razorpay_key_id" value="{{ $siteSettings['razorpay_key_id'] ?? '' }}" placeholder="rzp_test_..." class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm font-mono focus:border-theme-primary">
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-zinc-300">Razorpay Key Secret</label>
                        <input type="password" name="razorpay_key_secret" value="{{ $siteSettings['razorpay_key_secret'] ?? '' }}" placeholder="Enter secret" class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm font-mono focus:border-theme-primary">
                    </div>

                    <div class="space-y-1 md:col-span-2">
                        <label class="text-xs font-semibold text-zinc-300">Checkout Simulation Mode</label>
                        <select name="razorpay_simulation_mode" class="w-full px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:border-theme-primary">
                            <option value="1" {{ ($siteSettings['razorpay_simulation_mode'] ?? '1') == '1' ? 'selected' : '' }}>Enabled (Allows 1-Click Instant Demo Testing without requiring live keys)</option>
                            <option value="0" {{ ($siteSettings['razorpay_simulation_mode'] ?? '1') == '0' ? 'selected' : '' }}>Strict Razorpay Mode (Requires valid Razorpay credentials)</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="px-6 py-3 rounded-xl btn-gold-dynamic text-sm font-bold flex items-center gap-2 shadow-xl shadow-[var(--theme-primary)]/20">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Save All System Configurations</span>
                </button>
            </div>
        </form>
    </div>

    <!-- TAB 4: BOOKINGS MANAGEMENT -->
    <div x-show="activeTab === 'bookings'" class="mt-8 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i data-lucide="calendar" class="w-5 h-5 text-cyan-400"></i>
                    <span>All Booked Client Sessions</span>
                </h3>
                <p class="text-xs text-zinc-400 mt-0.5">Track and update lifecycle progression (Pending &rarr; Progress &rarr; Active &rarr; Next Level &rarr; Completed).</p>
            </div>
        </div>

        @if($bookings->isEmpty())
            <div class="site-card rounded-2xl border border-white/10 p-12 text-center text-zinc-500">
                <i data-lucide="calendar-x" class="w-12 h-12 mx-auto text-zinc-600 mb-3"></i>
                <p>No client bookings recorded yet.</p>
            </div>
        @else
            <div class="site-card rounded-2xl border border-white/10 overflow-hidden shadow-2xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-zinc-300">
                        <thead class="bg-white/5 text-zinc-400 uppercase tracking-wider font-semibold border-b border-white/10">
                            <tr>
                                <th class="py-3.5 px-4">ID</th>
                                <th class="py-3.5 px-4">Client Contact</th>
                                <th class="py-3.5 px-4">Selected Package</th>
                                <th class="py-3.5 px-4">Shoot Date</th>
                                <th class="py-3.5 px-4">Amount</th>
                                <th class="py-3.5 px-4">Payment</th>
                                <th class="py-3.5 px-4">Workflow Status</th>
                                <th class="py-3.5 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @foreach($bookings as $b)
                                <tr class="hover:bg-white/5 transition">
                                    <td class="py-4 px-4 font-mono text-zinc-400">#{{ $b->id }}</td>
                                    <td class="py-4 px-4">
                                        <div class="font-bold text-white">{{ $b->user->name ?? 'Guest Client' }}</div>
                                        <div class="text-[11px] text-zinc-400">{{ $b->user->email ?? 'No email' }}</div>
                                    </td>
                                    <td class="py-4 px-4 font-medium text-white">{{ $b->package->name ?? 'Custom Package' }}</td>
                                    <td class="py-4 px-4 text-zinc-300">{{ \Carbon\Carbon::parse($b->booking_date)->format('M d, Y') }}</td>
                                    <td class="py-4 px-4 font-bold text-emerald-400">{{ $siteSettings['currency_symbol'] ?? '₹' }}{{ number_format($b->amount) }}</td>
                                    <td class="py-4 px-4">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $b->payment_status === 'completed' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-amber-500/20 text-amber-300' }}">
                                            {{ $b->payment_status }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4">
                                        <form method="POST" action="{{ route('admin.booking.updateStatus', $b) }}" class="flex items-center gap-2">
                                            @csrf
                                            <select name="status" onchange="this.form.submit()" class="px-3 py-1 rounded-lg text-xs bg-black/40 border border-white/15 text-white focus:border-theme-primary cursor-pointer">
                                                @foreach(['pending' => 'Pending', 'progress' => 'In Progress', 'active' => 'Active Shoot', 'next_level' => 'Retouching', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $sVal => $sLabel)
                                                    <option value="{{ $sVal }}" {{ $b->status == $sVal ? 'selected' : '' }}>{{ $sLabel }}</option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </td>
                                    <td class="py-4 px-4 text-right">
                                        <form method="POST" action="{{ route('admin.booking.delete', $b) }}" onsubmit="return confirm('Are you sure you want to delete this booking?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-zinc-500 hover:text-rose-400 hover:bg-rose-500/10 transition">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <!-- TAB 5: PACKAGES MANAGEMENT -->
    <div x-show="activeTab === 'packages'" class="mt-8 space-y-8">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i data-lucide="layers" class="w-5 h-5 text-amber-400"></i>
                    <span>Studio Pricing Packages</span>
                </h3>
                <p class="text-xs text-zinc-400 mt-0.5">Manage luxury photoshoot packages, pricing tiers, and included perks.</p>
            </div>
        </div>

        <!-- Add New Package Form -->
        <form method="POST" action="{{ route('admin.package.store') }}" enctype="multipart/form-data" class="site-card rounded-2xl border border-white/10 p-6 space-y-6">
            @csrf
            <h4 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-4 h-4 text-theme-primary"></i>
                <span>Add New Studio Package</span>
            </h4>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="space-y-1 sm:col-span-3">
                    <label class="text-xs font-semibold text-zinc-300">Package Name</label>
                    <input type="text" name="name" required placeholder="e.g. Royal Cinematic Wedding Archive" class="w-full px-4 py-2 text-xs rounded-xl bg-white/5 border border-white/10 text-white focus:border-theme-primary">
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-zinc-300">Min Price ({{ $siteSettings['currency_symbol'] ?? '₹' }})</label>
                    <input type="number" name="price_min" required placeholder="120000" class="w-full px-4 py-2 text-xs rounded-xl bg-white/5 border border-white/10 text-white focus:border-theme-primary">
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-zinc-300">Max Price ({{ $siteSettings['currency_symbol'] ?? '₹' }})</label>
                    <input type="number" name="price_max" required placeholder="250000" class="w-full px-4 py-2 text-xs rounded-xl bg-white/5 border border-white/10 text-white focus:border-theme-primary">
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-zinc-300">Cover Image URL</label>
                    <input type="url" name="image_url" placeholder="https://images.unsplash.com/photo-..." class="w-full px-4 py-2 text-xs rounded-xl bg-white/5 border border-white/10 text-white focus:border-theme-primary">
                </div>

                <div class="space-y-1 sm:col-span-3">
                    <label class="text-xs font-semibold text-zinc-300">Description</label>
                    <textarea name="description" rows="2" required placeholder="Detailed session brief and creative scope..." class="w-full px-4 py-2 text-xs rounded-xl bg-white/5 border border-white/10 text-white focus:border-theme-primary"></textarea>
                </div>

                <div class="space-y-1 sm:col-span-3">
                    <label class="text-xs font-semibold text-zinc-300">Package Features (Comma separated)</label>
                    <input type="text" name="features" required placeholder="Full-day coverage, 2 Senior Photographers, 150 Retouched Plates, Layflat Leather Album" class="w-full px-4 py-2 text-xs rounded-xl bg-white/5 border border-white/10 text-white focus:border-theme-primary">
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-5 py-2.5 rounded-xl btn-gold-dynamic text-xs font-bold flex items-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Create Package</span>
                </button>
            </div>
        </form>

        <!-- Packages List Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($packages as $pkg)
                <div class="site-card rounded-2xl border border-white/10 overflow-hidden flex flex-col justify-between group hover:border-[var(--theme-primary)] transition duration-300">
                    <div>
                        <div class="h-44 relative overflow-hidden bg-black/50">
                            <img src="{{ $pkg->image_path ?: 'https://images.unsplash.com/photo-1492691527719-9d1e07e534b4?w=800' }}" alt="{{ $pkg->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                            <div class="absolute bottom-3 left-4 right-4 flex items-center justify-between">
                                <span class="text-xs font-bold text-white">{{ $pkg->name }}</span>
                                <span class="text-xs font-bold px-2 py-0.5 rounded bg-black/60 backdrop-blur-md text-theme-primary border border-white/10">
                                    {{ $siteSettings['currency_symbol'] ?? '₹' }}{{ number_format($pkg->price_min) }} - {{ number_format($pkg->price_max) }}
                                </span>
                            </div>
                        </div>

                        <div class="p-5 space-y-3">
                            <p class="text-xs text-zinc-400 line-clamp-2">{{ $pkg->description }}</p>

                            @if(is_array($pkg->features) || is_string($pkg->features))
                                @php
                                    $feats = is_array($pkg->features) ? $pkg->features : json_decode($pkg->features, true) ?? explode(',', $pkg->features);
                                @endphp
                                <ul class="space-y-1.5 pt-2 border-t border-white/5 text-[11px] text-zinc-300">
                                    @foreach(array_slice($feats, 0, 4) as $feat)
                                        <li class="flex items-center gap-2">
                                            <i data-lucide="check" class="w-3.5 h-3.5 text-theme-primary shrink-0"></i>
                                            <span class="truncate">{{ trim($feat) }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>

                    <div class="p-5 pt-0 flex items-center justify-between border-t border-white/5 mt-4">
                        <a href="{{ route('booking.checkout', $pkg->slug) }}" target="_blank" class="text-xs text-theme-primary hover:underline flex items-center gap-1">
                            <span>Checkout Link</span>
                            <i data-lucide="external-link" class="w-3 h-3"></i>
                        </a>
                        <form method="POST" action="{{ route('admin.package.delete', $pkg) }}" onsubmit="return confirm('Delete this package?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-zinc-500 hover:text-rose-400 hover:bg-rose-500/10 rounded-lg transition">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- TAB 6: GALLERY MANAGEMENT -->
    <div x-show="activeTab === 'gallery'" class="mt-8 space-y-8">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i data-lucide="image" class="w-5 h-5 text-emerald-400"></i>
                    <span>Master Portfolio Showcase</span>
                </h3>
                <p class="text-xs text-zinc-400 mt-0.5">Upload photos into curated categories for the dynamic masonry gallery.</p>
            </div>
        </div>

        <!-- Add Gallery Item Form -->
        <form method="POST" action="{{ route('admin.gallery.store') }}" enctype="multipart/form-data" class="site-card rounded-2xl border border-white/10 p-6 space-y-4">
            @csrf
            <h4 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                <i data-lucide="upload-cloud" class="w-4 h-4 text-emerald-400"></i>
                <span>Add Gallery Photograph</span>
            </h4>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-zinc-300">Photo Title</label>
                    <input type="text" name="title" required placeholder="e.g. Celestial Radiance" class="w-full px-4 py-2 text-xs rounded-xl bg-white/5 border border-white/10 text-white focus:border-theme-primary">
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-zinc-300">Category</label>
                    <select name="category" required class="w-full px-4 py-2 text-xs rounded-xl bg-white/5 border border-white/10 text-white focus:border-theme-primary">
                        <option value="Wedding">Wedding</option>
                        <option value="Portrait">Portrait</option>
                        <option value="Fashion">Fashion</option>
                        <option value="Editorial">Editorial</option>
                        <option value="Event">Event</option>
                        <option value="Product">Product / Commercial</option>
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-zinc-300">Image Web URL</label>
                    <input type="url" name="image_url" placeholder="https://images.unsplash.com/photo-..." class="w-full px-4 py-2 text-xs rounded-xl bg-white/5 border border-white/10 text-white focus:border-theme-primary">
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-5 py-2.5 rounded-xl btn-gold-dynamic text-xs font-bold flex items-center gap-2">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Publish to Gallery</span>
                </button>
            </div>
        </form>

        <!-- Gallery Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($gallery as $item)
                <div class="site-card rounded-xl border border-white/10 overflow-hidden relative group aspect-square">
                    <img src="{{ $item->image_path }}" alt="{{ $item->title }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity p-3 flex flex-col justify-between">
                        <div class="flex justify-end">
                            <form method="POST" action="{{ route('admin.gallery.delete', $item) }}" onsubmit="return confirm('Remove photo?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg bg-black/60 text-rose-400 hover:bg-rose-500 hover:text-white transition">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                </button>
                            </form>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-[var(--theme-primary)] text-black">{{ $item->category }}</span>
                            <div class="text-xs font-bold text-white mt-1">{{ $item->title }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- TAB 7: BLOG / JOURNAL MANAGEMENT -->
    <div x-show="activeTab === 'blogs'" class="mt-8 space-y-8">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i data-lucide="book-open" class="w-5 h-5 text-blue-400"></i>
                    <span>Studio Journal & Editorial Stories</span>
                </h3>
                <p class="text-xs text-zinc-400 mt-0.5">Publish SEO-rich photography articles, lighting masterclasses, and behind-the-scenes stories.</p>
            </div>
        </div>

        <!-- Add Blog Form -->
        <form method="POST" action="{{ route('admin.blog.store') }}" class="site-card rounded-2xl border border-white/10 p-6 space-y-4">
            @csrf
            <h4 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                <i data-lucide="pen-tool" class="w-4 h-4 text-blue-400"></i>
                <span>Publish New Journal Article</span>
            </h4>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1 sm:col-span-2">
                    <label class="text-xs font-semibold text-zinc-300">Article Title</label>
                    <input type="text" name="title" required placeholder="e.g. Sculpting Shadows: The Rembrant Lighting Masterclass" class="w-full px-4 py-2 text-xs rounded-xl bg-white/5 border border-white/10 text-white focus:border-theme-primary">
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-zinc-300">Cover Image URL</label>
                    <input type="url" name="image_url" placeholder="https://images.unsplash.com/photo-..." class="w-full px-4 py-2 text-xs rounded-xl bg-white/5 border border-white/10 text-white focus:border-theme-primary">
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-zinc-300">Short Summary / Excerpt</label>
                    <input type="text" name="excerpt" required placeholder="A brief preview summarizing the article..." class="w-full px-4 py-2 text-xs rounded-xl bg-white/5 border border-white/10 text-white focus:border-theme-primary">
                </div>

                <div class="space-y-1 sm:col-span-2">
                    <label class="text-xs font-semibold text-zinc-300">Article Content (Supports HTML paragraphs & styling)</label>
                    <textarea name="content" rows="6" required placeholder="<p>Full article body...</p>" class="w-full px-4 py-2 text-xs rounded-xl bg-white/5 border border-white/10 text-white font-mono focus:border-theme-primary"></textarea>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-5 py-2.5 rounded-xl btn-gold-dynamic text-xs font-bold flex items-center gap-2">
                    <i data-lucide="send" class="w-4 h-4"></i>
                    <span>Publish Article</span>
                </button>
            </div>
        </form>

        <!-- Blogs List -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($blogs as $post)
                <div class="site-card rounded-2xl border border-white/10 overflow-hidden flex flex-col justify-between group hover:border-blue-500/50 transition">
                    <div>
                        <div class="h-40 relative overflow-hidden bg-black/40">
                            <img src="{{ $post->image_path ?: 'https://images.unsplash.com/photo-1542038784456-1ea8e935640e?w=800' }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        </div>
                        <div class="p-5 space-y-2">
                            <div class="text-[10px] text-zinc-500">{{ $post->created_at->format('M d, Y') }}</div>
                            <h4 class="text-sm font-bold text-white line-clamp-1 group-hover:text-blue-400 transition">{{ $post->title }}</h4>
                            <p class="text-xs text-zinc-400 line-clamp-2">{{ $post->excerpt }}</p>
                        </div>
                    </div>

                    <div class="p-5 pt-0 flex items-center justify-between border-t border-white/5 mt-3">
                        <a href="{{ route('blog.single', $post->slug) }}" target="_blank" class="text-xs text-blue-400 hover:underline flex items-center gap-1">
                            <span>Read Article</span>
                            <i data-lucide="external-link" class="w-3 h-3"></i>
                        </a>
                        <form method="POST" action="{{ route('admin.blog.delete', $post) }}" onsubmit="return confirm('Delete article?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-zinc-500 hover:text-rose-400 hover:bg-rose-500/10 rounded-lg transition">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- TAB 8: MESSAGES & INQUIRIES -->
    <div x-show="activeTab === 'messages'" class="mt-8 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i data-lucide="mail" class="w-5 h-5 text-violet-400"></i>
                    <span>Concierge Contact Messages</span>
                </h3>
                <p class="text-xs text-zinc-400 mt-0.5">Inquiries submitted via the public contact form.</p>
            </div>
        </div>

        @if($messages->isEmpty())
            <div class="site-card rounded-2xl border border-white/10 p-12 text-center text-zinc-500">
                <i data-lucide="inbox" class="w-12 h-12 mx-auto text-zinc-600 mb-3"></i>
                <p>No contact messages yet.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($messages as $msg)
                    <div class="site-card rounded-2xl border {{ $msg->status === 'unread' ? 'border-violet-500/50 bg-violet-950/10' : 'border-white/10' }} p-6 flex flex-col md:flex-row md:items-start justify-between gap-4">
                        <div class="space-y-2 flex-1">
                            <div class="flex items-center gap-3">
                                <span class="font-bold text-white text-sm">{{ $msg->name }}</span>
                                <span class="text-xs text-zinc-400">&bull; {{ $msg->email }}</span>
                                @if($msg->status === 'unread')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-violet-500/20 text-violet-300">Unread</span>
                                @endif
                                <span class="text-[10px] text-zinc-500 ml-auto">{{ $msg->created_at->diffForHumans() }}</span>
                            </div>
                            <h4 class="text-xs font-semibold text-zinc-200">{{ $msg->subject }}</h4>
                            <p class="text-xs text-zinc-400 leading-relaxed bg-black/20 p-3 rounded-xl">{{ $msg->message }}</p>
                        </div>

                        <div class="flex items-center gap-2 self-end md:self-start shrink-0">
                            <a href="mailto:{{ $msg->email }}?subject=Re: {{ urlencode($msg->subject) }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-white/5 hover:bg-white/10 text-white border border-white/10 flex items-center gap-1.5 transition">
                                <i data-lucide="reply" class="w-3.5 h-3.5 text-violet-400"></i>
                                <span>Reply Email</span>
                            </a>

                            @if($msg->status === 'unread')
                                <form method="POST" action="{{ route('admin.message.read', $msg) }}">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-300 border border-emerald-500/20 transition">
                                        Mark Read
                                    </button>
                                </form>
                            @endif

                            <form method="POST" action="{{ route('admin.message.delete', $msg) }}" onsubmit="return confirm('Delete message?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg text-zinc-500 hover:text-rose-400 hover:bg-rose-500/10 transition">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- TAB 9: PHOTOGRAPHERS / VENDORS -->
    <div x-show="activeTab === 'vendors'" class="mt-8 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i data-lucide="camera" class="w-5 h-5 text-teal-400"></i>
                    <span>Photographer Partners & Studio Vendors</span>
                </h3>
                <p class="text-xs text-zinc-400 mt-0.5">Approve or suspend creative partners registered to provide studio photoshoot services.</p>
            </div>
        </div>

        @if($vendors->isEmpty())
            <div class="site-card rounded-2xl border border-white/10 p-12 text-center text-zinc-500">
                <i data-lucide="users" class="w-12 h-12 mx-auto text-zinc-600 mb-3"></i>
                <p>No vendor applications submitted yet.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($vendors as $v)
                    <div class="site-card rounded-2xl border border-white/10 p-6 space-y-4 flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-white text-base">{{ $v->name }}</span>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $v->status === 'approved' ? 'bg-emerald-500/20 text-emerald-300' : ($v->status === 'suspended' ? 'bg-rose-500/20 text-rose-300' : 'bg-amber-500/20 text-amber-300') }}">
                                    {{ $v->status }}
                                </span>
                            </div>
                            <p class="text-xs text-zinc-400 line-clamp-3">{{ $v->description ?? 'No bio provided' }}</p>
                            <div class="text-[11px] text-zinc-500">
                                Linked User: <strong class="text-zinc-300">{{ $v->user->name ?? 'None' }}</strong> ({{ $v->user->email ?? '' }})
                            </div>
                        </div>

                        <div class="pt-4 border-t border-white/5 flex items-center gap-2">
                            <form method="POST" action="{{ route('admin.vendor.updateStatus', $v) }}" class="w-full flex items-center gap-2">
                                @csrf
                                <select name="status" onchange="this.form.submit()" class="w-full px-3 py-1.5 rounded-lg text-xs bg-black/40 border border-white/15 text-white focus:border-theme-primary">
                                    <option value="pending" {{ $v->status === 'pending' ? 'selected' : '' }}>Pending Review</option>
                                    <option value="approved" {{ $v->status === 'approved' ? 'selected' : '' }}>Approved (Active)</option>
                                    <option value="suspended" {{ $v->status === 'suspended' ? 'selected' : '' }}>Suspended</option>
                                </select>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection
