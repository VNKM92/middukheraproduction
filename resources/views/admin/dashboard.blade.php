@extends('layouts.app')

@section('content')
<div x-data="{
    activeTab: 'overview',
    sidebarOpen: true,
    mobileSidebarOpen: false,
    mediaSearch: '',
    mediaFolder: 'all',
    previewMediaUrl: null,
    previewMediaName: '',
    copiedUrlNotice: false,
    
    // Modals state
    editingPackage: null,
    editingGallery: null,
    editingBlog: null,
    editingPage: null,
    editingTestimonial: null,
    editingFaq: null,
    editingTeamMember: null,
    selectedWebhook: null,

    copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            this.copiedUrlNotice = true;
            setTimeout(() => { this.copiedUrlNotice = false; }, 2500);
        });
    }
}" class="min-h-screen bg-[var(--theme-bg,#07060a)] flex relative">

    <!-- Copy Notice Toast -->
    <div x-show="copiedUrlNotice" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed bottom-6 right-6 z-50 bg-emerald-500 text-black font-bold px-4 py-2.5 rounded-xl shadow-2xl flex items-center gap-2 text-xs" style="display: none;">
        <i data-lucide="check-circle" class="w-4 h-4"></i>
        <span>Link copied to clipboard!</span>
    </div>

    <!-- Mobile Backdrop Blur -->
    <div x-show="mobileSidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="mobileSidebarOpen = false" 
         class="fixed inset-0 z-40 bg-black/80 backdrop-blur-sm lg:hidden" 
         style="display: none;"></div>

    <!-- ============================================== -->
    <!-- EXECUTIVE ADMIN SIDEBAR NAVIGATION -->
    <!-- ============================================== -->
    <aside :class="{
            'translate-x-0': mobileSidebarOpen,
            '-translate-x-full': !mobileSidebarOpen,
            'lg:translate-x-0': true,
            'lg:w-72': sidebarOpen,
            'lg:w-0 lg:overflow-hidden lg:border-r-0 lg:p-0 lg:opacity-0': !sidebarOpen
        }" 
        class="fixed inset-y-0 left-0 z-50 w-72 h-screen sticky top-0 bg-[#0c0a14] border-r border-white/10 flex flex-col justify-between transition-all duration-300 ease-in-out lg:z-auto lg:shrink-0">
        
        <!-- Sidebar Brand / Header -->
        <div class="p-5 border-b border-white/10 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[var(--theme-primary)] to-[var(--theme-secondary)] p-0.5 shadow-lg shadow-[var(--theme-primary)]/20">
                    <div class="w-full h-full bg-[#0c0a14] rounded-[10px] flex items-center justify-center">
                        <i data-lucide="shield-check" class="w-5 h-5 text-theme-primary"></i>
                    </div>
                </div>
                <div>
                    <div class="text-xs font-serif font-bold text-white tracking-widest uppercase">{{ $siteSettings['site_name'] ?? 'Middukhera' }}</div>
                    <div class="text-[10px] font-mono text-[var(--theme-primary)] uppercase tracking-wider">Super Admin</div>
                </div>
            </div>

            <button type="button" @click="mobileSidebarOpen = false" class="lg:hidden p-1.5 rounded-lg text-zinc-400 hover:text-white hover:bg-white/10 transition">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Sidebar Navigation Menu Items -->
        <div class="flex-1 overflow-y-auto px-3.5 py-4 space-y-6 scrollbar-thin">
            <!-- GROUP 1: CORE & METRICS -->
            <div class="space-y-1">
                <span class="px-3 text-[10px] font-bold text-zinc-500 uppercase tracking-widest block mb-2">Core Operations</span>
                
                <!-- 1. Overview -->
                <button type="button" @click="activeTab = 'overview'; mobileSidebarOpen = false;" 
                    :class="activeTab === 'overview' ? 'bg-[var(--theme-primary)] text-black font-bold shadow-lg shadow-[var(--theme-primary)]/20' : 'text-zinc-400 hover:text-white hover:bg-white/5'" 
                    class="w-full px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all duration-150 flex items-center justify-between group">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="layout-dashboard" class="w-4 h-4 shrink-0 transition" :class="activeTab === 'overview' ? 'text-black' : 'text-amber-400'"></i>
                        <span>Executive Overview</span>
                    </div>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 opacity-0 group-hover:opacity-100 transition" :class="activeTab === 'overview' ? 'opacity-100 text-black' : ''"></i>
                </button>

                <!-- 2. Bookings -->
                <button type="button" @click="activeTab = 'bookings'; mobileSidebarOpen = false;" 
                    :class="activeTab === 'bookings' ? 'bg-[var(--theme-primary)] text-black font-bold shadow-lg shadow-[var(--theme-primary)]/20' : 'text-zinc-400 hover:text-white hover:bg-white/5'" 
                    class="w-full px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all duration-150 flex items-center justify-between group">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="calendar" class="w-4 h-4 shrink-0 transition" :class="activeTab === 'bookings' ? 'text-black' : 'text-cyan-400'"></i>
                        <span>Bookings</span>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold" :class="activeTab === 'bookings' ? 'bg-black/20 text-black' : 'bg-cyan-500/20 text-cyan-300'">
                        {{ count($bookings) }}
                    </span>
                </button>

                <!-- 3. Transactions -->
                <button type="button" @click="activeTab = 'transactions'; mobileSidebarOpen = false;" 
                    :class="activeTab === 'transactions' ? 'bg-[var(--theme-primary)] text-black font-bold shadow-lg shadow-[var(--theme-primary)]/20' : 'text-zinc-400 hover:text-white hover:bg-white/5'" 
                    class="w-full px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all duration-150 flex items-center justify-between group">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="credit-card" class="w-4 h-4 shrink-0 transition" :class="activeTab === 'transactions' ? 'text-black' : 'text-emerald-400'"></i>
                        <span>Transactions</span>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold" :class="activeTab === 'transactions' ? 'bg-black/20 text-black' : 'bg-emerald-500/20 text-emerald-300'">
                        {{ count($transactions) }}
                    </span>
                </button>
            </div>

            <!-- GROUP 2: STUDIO CATALOG & MEDIA -->
            <div class="space-y-1">
                <span class="px-3 text-[10px] font-bold text-zinc-500 uppercase tracking-widest block mb-2">Catalog & Creative</span>

                <!-- Media & Uploads Library -->
                <button type="button" @click="activeTab = 'media_library'; mobileSidebarOpen = false;" 
                    :class="activeTab === 'media_library' ? 'bg-[var(--theme-primary)] text-black font-bold shadow-lg shadow-[var(--theme-primary)]/20' : 'text-zinc-400 hover:text-white hover:bg-white/5'" 
                    class="w-full px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all duration-150 flex items-center justify-between group">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="upload-cloud" class="w-4 h-4 shrink-0 transition" :class="activeTab === 'media_library' ? 'text-black' : 'text-cyan-400'"></i>
                        <span>Uploads & Media</span>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold" :class="activeTab === 'media_library' ? 'bg-black/20 text-black' : 'bg-cyan-500/20 text-cyan-300'">
                        {{ count($mediaFiles) }}
                    </span>
                </button>

                <!-- Packages -->
                <button type="button" @click="activeTab = 'packages'; mobileSidebarOpen = false;" 
                    :class="activeTab === 'packages' ? 'bg-[var(--theme-primary)] text-black font-bold shadow-lg shadow-[var(--theme-primary)]/20' : 'text-zinc-400 hover:text-white hover:bg-white/5'" 
                    class="w-full px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all duration-150 flex items-center justify-between group">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="layers" class="w-4 h-4 shrink-0 transition" :class="activeTab === 'packages' ? 'text-black' : 'text-amber-400'"></i>
                        <span>Pricing Packages</span>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold" :class="activeTab === 'packages' ? 'bg-black/20 text-black' : 'bg-amber-500/20 text-amber-300'">
                        {{ count($packages) }}
                    </span>
                </button>

                <!-- Gallery -->
                <button type="button" @click="activeTab = 'gallery'; mobileSidebarOpen = false;" 
                    :class="activeTab === 'gallery' ? 'bg-[var(--theme-primary)] text-black font-bold shadow-lg shadow-[var(--theme-primary)]/20' : 'text-zinc-400 hover:text-white hover:bg-white/5'" 
                    class="w-full px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all duration-150 flex items-center justify-between group">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="image" class="w-4 h-4 shrink-0 transition" :class="activeTab === 'gallery' ? 'text-black' : 'text-emerald-400'"></i>
                        <span>Portfolio Gallery</span>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold" :class="activeTab === 'gallery' ? 'bg-black/20 text-black' : 'bg-white/10 text-zinc-400'">
                        {{ count($gallery) }}
                    </span>
                </button>

                <!-- Blog / Journal -->
                <button type="button" @click="activeTab = 'blogs'; mobileSidebarOpen = false;" 
                    :class="activeTab === 'blogs' ? 'bg-[var(--theme-primary)] text-black font-bold shadow-lg shadow-[var(--theme-primary)]/20' : 'text-zinc-400 hover:text-white hover:bg-white/5'" 
                    class="w-full px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all duration-150 flex items-center justify-between group">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="book-open" class="w-4 h-4 shrink-0 transition" :class="activeTab === 'blogs' ? 'text-black' : 'text-blue-400'"></i>
                        <span>Studio Journal</span>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold" :class="activeTab === 'blogs' ? 'bg-black/20 text-black' : 'bg-blue-500/20 text-blue-300'">
                        {{ count($blogs) }}
                    </span>
                </button>
            </div>

            <!-- GROUP 3: DYNAMIC PAGES & CMS CONTENT -->
            <div class="space-y-1">
                <span class="px-3 text-[10px] font-bold text-zinc-500 uppercase tracking-widest block mb-2">Dynamic CMS & Pages</span>

                <!-- Dynamic Pages & Policies -->
                <button type="button" @click="activeTab = 'pages'; mobileSidebarOpen = false;" 
                    :class="activeTab === 'pages' ? 'bg-[var(--theme-primary)] text-black font-bold shadow-lg shadow-[var(--theme-primary)]/20' : 'text-zinc-400 hover:text-white hover:bg-white/5'" 
                    class="w-full px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all duration-150 flex items-center justify-between group">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="file-text" class="w-4 h-4 shrink-0 transition" :class="activeTab === 'pages' ? 'text-black' : 'text-indigo-400'"></i>
                        <span>Pages & Policies</span>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold" :class="activeTab === 'pages' ? 'bg-black/20 text-black' : 'bg-indigo-500/20 text-indigo-300'">
                        {{ count($pages) }}
                    </span>
                </button>

                <!-- Testimonials -->
                <button type="button" @click="activeTab = 'testimonials'; mobileSidebarOpen = false;" 
                    :class="activeTab === 'testimonials' ? 'bg-[var(--theme-primary)] text-black font-bold shadow-lg shadow-[var(--theme-primary)]/20' : 'text-zinc-400 hover:text-white hover:bg-white/5'" 
                    class="w-full px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all duration-150 flex items-center justify-between group">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="star" class="w-4 h-4 shrink-0 transition" :class="activeTab === 'testimonials' ? 'text-black' : 'text-amber-400'"></i>
                        <span>Client Reviews</span>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold" :class="activeTab === 'testimonials' ? 'bg-black/20 text-black' : 'bg-white/10 text-zinc-400'">
                        {{ count($testimonials) }}
                    </span>
                </button>

                <!-- FAQs -->
                <button type="button" @click="activeTab = 'faqs'; mobileSidebarOpen = false;" 
                    :class="activeTab === 'faqs' ? 'bg-[var(--theme-primary)] text-black font-bold shadow-lg shadow-[var(--theme-primary)]/20' : 'text-zinc-400 hover:text-white hover:bg-white/5'" 
                    class="w-full px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all duration-150 flex items-center justify-between group">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="help-circle" class="w-4 h-4 shrink-0 transition" :class="activeTab === 'faqs' ? 'text-black' : 'text-purple-400'"></i>
                        <span>FAQ Manager</span>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold" :class="activeTab === 'faqs' ? 'bg-black/20 text-black' : 'bg-white/10 text-zinc-400'">
                        {{ count($faqs) }}
                    </span>
                </button>

                <!-- Team Members -->
                <button type="button" @click="activeTab = 'team'; mobileSidebarOpen = false;" 
                    :class="activeTab === 'team' ? 'bg-[var(--theme-primary)] text-black font-bold shadow-lg shadow-[var(--theme-primary)]/20' : 'text-zinc-400 hover:text-white hover:bg-white/5'" 
                    class="w-full px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all duration-150 flex items-center justify-between group">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="users" class="w-4 h-4 shrink-0 transition" :class="activeTab === 'team' ? 'text-black' : 'text-emerald-400'"></i>
                        <span>Team & Crew</span>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold" :class="activeTab === 'team' ? 'bg-black/20 text-black' : 'bg-white/10 text-zinc-400'">
                        {{ count($teamMembers) }}
                    </span>
                </button>

                <!-- Contact Inquiries -->
                <button type="button" @click="activeTab = 'messages'; mobileSidebarOpen = false;" 
                    :class="activeTab === 'messages' ? 'bg-[var(--theme-primary)] text-black font-bold shadow-lg shadow-[var(--theme-primary)]/20' : 'text-zinc-400 hover:text-white hover:bg-white/5'" 
                    class="w-full px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all duration-150 flex items-center justify-between group">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="mail" class="w-4 h-4 shrink-0 transition" :class="activeTab === 'messages' ? 'text-black' : 'text-violet-400'"></i>
                        <span>Contact Inquiries</span>
                    </div>
                    @if($unreadMessagesCount > 0)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500 text-white animate-pulse">
                            {{ $unreadMessagesCount }} New
                        </span>
                    @else
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold" :class="activeTab === 'messages' ? 'bg-black/20 text-black' : 'bg-white/10 text-zinc-400'">
                            {{ count($messages) }}
                        </span>
                    @endif
                </button>

                <!-- Photographers / Vendors -->
                <button type="button" @click="activeTab = 'vendors'; mobileSidebarOpen = false;" 
                    :class="activeTab === 'vendors' ? 'bg-[var(--theme-primary)] text-black font-bold shadow-lg shadow-[var(--theme-primary)]/20' : 'text-zinc-400 hover:text-white hover:bg-white/5'" 
                    class="w-full px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all duration-150 flex items-center justify-between group">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="camera" class="w-4 h-4 shrink-0 transition" :class="activeTab === 'vendors' ? 'text-black' : 'text-teal-400'"></i>
                        <span>Photographers</span>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold" :class="activeTab === 'vendors' ? 'bg-black/20 text-black' : 'bg-teal-500/20 text-teal-300'">
                        {{ count($vendors) }}
                    </span>
                </button>
            </div>

            <!-- GROUP 4: GATEWAYS & ENGINE CONFIG -->
            <div class="space-y-1">
                <span class="px-3 text-[10px] font-bold text-zinc-500 uppercase tracking-widest block mb-2">Gateways & System</span>

                <!-- Payment Gateways -->
                <button type="button" @click="activeTab = 'gateways'; mobileSidebarOpen = false;" 
                    :class="activeTab === 'gateways' ? 'bg-[var(--theme-primary)] text-black font-bold shadow-lg shadow-[var(--theme-primary)]/20' : 'text-zinc-400 hover:text-white hover:bg-white/5'" 
                    class="w-full px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all duration-150 flex items-center justify-between group">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="shield-check" class="w-4 h-4 shrink-0 transition" :class="activeTab === 'gateways' ? 'text-black' : 'text-emerald-400'"></i>
                        <span>Payment Gateways</span>
                    </div>
                    <span class="text-[9px] font-bold font-mono px-1.5 py-0.5 rounded uppercase" :class="activeTab === 'gateways' ? 'bg-black/20 text-black' : 'bg-emerald-500/20 text-emerald-300'">
                        {{ ($allSettings['active_payment_gateway'] ?? 'cashfree') === 'cashfree' ? 'Cashfree' : 'Razorpay' }}
                    </span>
                </button>

                <!-- Fast2SMS Engine & Templates -->
                <button type="button" @click="activeTab = 'sms_settings'; mobileSidebarOpen = false;" 
                    :class="activeTab === 'sms_settings' ? 'bg-[var(--theme-primary)] text-black font-bold shadow-lg shadow-[var(--theme-primary)]/20' : 'text-zinc-400 hover:text-white hover:bg-white/5'" 
                    class="w-full px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all duration-150 flex items-center justify-between group">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="message-square" class="w-4 h-4 shrink-0 transition" :class="activeTab === 'sms_settings' ? 'text-black' : 'text-amber-400'"></i>
                        <span>Fast2SMS & Templates</span>
                    </div>
                    <span class="w-2 h-2 rounded-full bg-emerald-400" title="Active Failover"></span>
                </button>

                <!-- Webhooks -->
                <button type="button" @click="activeTab = 'webhooks'; mobileSidebarOpen = false;" 
                    :class="activeTab === 'webhooks' ? 'bg-[var(--theme-primary)] text-black font-bold shadow-lg shadow-[var(--theme-primary)]/20' : 'text-zinc-400 hover:text-white hover:bg-white/5'" 
                    class="w-full px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all duration-150 flex items-center justify-between group">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="webhook" class="w-4 h-4 shrink-0 transition" :class="activeTab === 'webhooks' ? 'text-black' : 'text-indigo-400'"></i>
                        <span>Webhook Event Logs</span>
                    </div>
                </button>

                <!-- Theme & Colors -->
                <button type="button" @click="activeTab = 'theme_settings'; mobileSidebarOpen = false;" 
                    :class="activeTab === 'theme_settings' ? 'bg-[var(--theme-primary)] text-black font-bold shadow-lg shadow-[var(--theme-primary)]/20' : 'text-zinc-400 hover:text-white hover:bg-white/5'" 
                    class="w-full px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all duration-150 flex items-center justify-between group">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="palette" class="w-4 h-4 shrink-0 transition" :class="activeTab === 'theme_settings' ? 'text-black' : 'text-pink-400'"></i>
                        <span>Theme & Live Colors</span>
                    </div>
                </button>

                <!-- Site & SEO Config -->
                <button type="button" @click="activeTab = 'site_settings'; mobileSidebarOpen = false;" 
                    :class="activeTab === 'site_settings' ? 'bg-[var(--theme-primary)] text-black font-bold shadow-lg shadow-[var(--theme-primary)]/20' : 'text-zinc-400 hover:text-white hover:bg-white/5'" 
                    class="w-full px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all duration-150 flex items-center justify-between group">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="settings" class="w-4 h-4 shrink-0 transition" :class="activeTab === 'site_settings' ? 'text-black' : 'text-zinc-300'"></i>
                        <span>Site & SEO Config</span>
                    </div>
                </button>
            </div>
        </div>

        <!-- Sidebar Footer -->
        <div class="p-4 border-t border-white/10 space-y-3 bg-black/40">
            <div class="flex items-center justify-between text-xs text-zinc-400">
                <span class="truncate">{{ auth()->user()->name ?? 'Admin User' }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-rose-400 hover:text-rose-300 text-[11px] font-semibold">Logout</button>
                </form>
            </div>
            <div class="pt-2 border-t border-white/5 flex items-center justify-between text-[10px] text-zinc-500">
                <span>Production Engine</span>
                <span class="font-mono text-theme-primary">v3.0 Dynamic</span>
            </div>
        </div>
    </aside>

    <!-- ============================================== -->
    <!-- MAIN CONTENT AREA -->
    <!-- ============================================== -->
    <div class="flex-1 min-w-0 flex flex-col overflow-hidden">
        <!-- Top Sticky Header Bar -->
        <header class="sticky top-0 z-30 bg-[#0c0a14]/90 backdrop-blur-md border-b border-white/10 px-4 sm:px-6 lg:px-8 py-3.5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button type="button" @click="sidebarOpen = !sidebarOpen" 
                    class="hidden lg:flex px-3 py-1.5 rounded-xl text-xs font-semibold text-zinc-300 hover:text-white bg-white/5 hover:bg-white/10 border border-white/10 transition items-center gap-2 shadow-sm">
                    <i data-lucide="panel-left" class="w-4 h-4 text-theme-primary"></i>
                    <span x-text="sidebarOpen ? 'Collapse' : 'Expand'"></span>
                </button>
                
                <button type="button" @click="mobileSidebarOpen = true" 
                    class="lg:hidden p-2 rounded-xl text-zinc-300 hover:text-white bg-white/5 hover:bg-white/10 border border-white/10 transition flex items-center justify-center">
                    <i data-lucide="menu" class="w-5 h-5 text-theme-primary"></i>
                </button>

                <div class="h-4 w-px bg-white/10 hidden sm:block"></div>

                <div class="flex items-center gap-2">
                    <span class="text-[11px] text-zinc-500 font-mono hidden md:inline">Admin /</span>
                    <h2 class="text-xs sm:text-sm font-bold text-white uppercase tracking-wider flex items-center gap-1.5" x-text="
                        activeTab === 'overview' ? 'Executive Overview' :
                        activeTab === 'media_library' ? 'Uploads & Media Manager' :
                        activeTab === 'pages' ? 'Dynamic Pages & Legal Policies' :
                        activeTab === 'testimonials' ? 'Client Reviews & Testimonials' :
                        activeTab === 'faqs' ? 'FAQ Management' :
                        activeTab === 'team' ? 'Team & Crew' :
                        activeTab === 'theme_settings' ? 'Theme & Live Colors' :
                        activeTab === 'site_settings' ? 'Site & SEO Configuration' :
                        activeTab === 'bookings' ? 'Bookings Management' :
                        activeTab === 'transactions' ? 'Captured Transactions' :
                        activeTab === 'gateways' ? 'Payment Gateways' :
                        activeTab === 'sms_settings' ? 'Fast2SMS & Message Templates' :
                        activeTab === 'webhooks' ? 'Inbound Webhook Logs' :
                        activeTab === 'packages' ? 'Pricing Packages' :
                        activeTab === 'gallery' ? 'Master Portfolio Gallery' :
                        activeTab === 'blogs' ? 'Studio Journal & Articles' :
                        activeTab === 'messages' ? 'Contact Inquiries' :
                        activeTab === 'vendors' ? 'Photographer Partners' : 'Dashboard'
                    "></h2>
                </div>
            </div>

            <div class="flex items-center gap-2 sm:gap-3">
                <a href="{{ route('home') }}" target="_blank" class="px-3 py-1.5 text-xs font-semibold text-zinc-300 hover:text-white bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl flex items-center gap-1.5 transition">
                    <i data-lucide="external-link" class="w-3.5 h-3.5 text-theme-primary"></i>
                    <span class="hidden sm:inline">View Site</span>
                </a>
                <a href="{{ route('sitemap.xml') }}" target="_blank" class="px-3 py-1.5 text-xs font-semibold text-zinc-300 hover:text-white bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl flex items-center gap-1.5 transition">
                    <i data-lucide="globe" class="w-3.5 h-3.5 text-emerald-400"></i>
                    <span class="hidden sm:inline">Sitemap</span>
                </a>
            </div>
        </header>

        <!-- Main Content Scroll Area -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 space-y-8 overflow-y-auto max-w-7xl w-full mx-auto">

            <!-- ============================================== -->
            <!-- TAB 1: OVERVIEW -->
            <!-- ============================================== -->
            <div x-show="activeTab === 'overview'" class="space-y-8">
                <!-- KPI Metrics Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="site-card p-6 rounded-2xl border border-white/10 relative overflow-hidden group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Gross Revenue</span>
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                                <i data-lucide="indian-rupee" class="w-5 h-5"></i>
                            </div>
                        </div>
                        <div class="text-3xl font-bold text-white mt-4">{{ $siteSettings['currency_symbol'] ?? '₹' }}{{ number_format($totalEarnings) }}</div>
                        <div class="text-xs text-emerald-400 flex items-center gap-1 mt-2">
                            <i data-lucide="trending-up" class="w-3.5 h-3.5"></i>
                            <span>Captured Transactions</span>
                        </div>
                    </div>

                    <div class="site-card p-6 rounded-2xl border border-white/10 relative overflow-hidden group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Booked Sessions</span>
                            <div class="w-10 h-10 rounded-xl bg-theme-primary/10 border border-theme-primary/20 flex items-center justify-center text-theme-primary">
                                <i data-lucide="calendar-check-2" class="w-5 h-5"></i>
                            </div>
                        </div>
                        <div class="text-3xl font-bold text-white mt-4">{{ $totalBookings }}</div>
                        <div class="text-xs text-zinc-400 mt-2">{{ $totalPendingBookings }} pending confirmation</div>
                    </div>

                    <div class="site-card p-6 rounded-2xl border border-white/10 relative overflow-hidden group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Unique Visitors</span>
                            <div class="w-10 h-10 rounded-xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center text-cyan-400">
                                <i data-lucide="users" class="w-5 h-5"></i>
                            </div>
                        </div>
                        <div class="text-3xl font-bold text-white mt-4">{{ number_format($totalVisitors) }}</div>
                        <div class="text-xs text-cyan-400 flex items-center gap-1 mt-2">
                            <i data-lucide="activity" class="w-3.5 h-3.5"></i>
                            <span>Traffic tracked</span>
                        </div>
                    </div>

                    <div class="site-card p-6 rounded-2xl border border-white/10 relative overflow-hidden group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Media Assets</span>
                            <div class="w-10 h-10 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400">
                                <i data-lucide="folder-kanban" class="w-5 h-5"></i>
                            </div>
                        </div>
                        <div class="text-3xl font-bold text-white mt-4">{{ count($mediaFiles) }}</div>
                        <div class="text-xs text-zinc-400 mt-2">{{ $totalPackages }} Pkgs &bull; {{ count($gallery) }} Photos</div>
                    </div>
                </div>

                <!-- Fast Actions & Recent Tables -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
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
                                            <th class="py-2.5 font-semibold">Date</th>
                                            <th class="py-2.5 font-semibold">Amount</th>
                                            <th class="py-2.5 font-semibold">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-white/5">
                                        @foreach($bookings->take(6) as $b)
                                            <tr class="hover:bg-white/5 transition">
                                                <td class="py-3 font-medium text-white">{{ $b->user->name ?? 'Guest' }}</td>
                                                <td class="py-3 text-zinc-300">{{ $b->package->name ?? 'Session' }}</td>
                                                <td class="py-3 text-zinc-400">{{ \Carbon\Carbon::parse($b->booking_date)->format('M d, Y') }}</td>
                                                <td class="py-3 font-semibold text-emerald-400">{{ $siteSettings['currency_symbol'] ?? '₹' }}{{ number_format($b->amount) }}</td>
                                                <td class="py-3">
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                                                        {{ $b->status === 'completed' ? 'bg-emerald-500/20 text-emerald-300' : '' }}
                                                        {{ $b->status === 'active' ? 'bg-cyan-500/20 text-cyan-300' : '' }}
                                                        {{ $b->status === 'progress' ? 'bg-amber-500/20 text-amber-300' : '' }}
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

                    <div class="space-y-6">
                        <!-- Fast Navigation Shortcuts -->
                        <div class="site-card rounded-2xl border border-white/10 p-6 space-y-3">
                            <h4 class="text-xs font-bold text-zinc-400 uppercase tracking-wider">Fast CRUD Operations</h4>
                            <div class="grid grid-cols-2 gap-2">
                                <button @click="activeTab = 'media_library'" class="p-3 rounded-xl bg-white/5 hover:bg-white/10 border border-white/5 text-left transition flex flex-col gap-1">
                                    <i data-lucide="upload-cloud" class="w-4 h-4 text-cyan-400"></i>
                                    <span class="text-xs font-semibold text-white">Upload Files</span>
                                </button>
                                <button @click="activeTab = 'pages'" class="p-3 rounded-xl bg-white/5 hover:bg-white/10 border border-white/5 text-left transition flex flex-col gap-1">
                                    <i data-lucide="file-text" class="w-4 h-4 text-indigo-400"></i>
                                    <span class="text-xs font-semibold text-white">Edit Pages</span>
                                </button>
                                <button @click="activeTab = 'packages'" class="p-3 rounded-xl bg-white/5 hover:bg-white/10 border border-white/5 text-left transition flex flex-col gap-1">
                                    <i data-lucide="layers" class="w-4 h-4 text-amber-400"></i>
                                    <span class="text-xs font-semibold text-white">Packages</span>
                                </button>
                                <button @click="activeTab = 'gallery'" class="p-3 rounded-xl bg-white/5 hover:bg-white/10 border border-white/5 text-left transition flex flex-col gap-1">
                                    <i data-lucide="image" class="w-4 h-4 text-emerald-400"></i>
                                    <span class="text-xs font-semibold text-white">Gallery</span>
                                </button>
                                <button @click="activeTab = 'testimonials'" class="p-3 rounded-xl bg-white/5 hover:bg-white/10 border border-white/5 text-left transition flex flex-col gap-1">
                                    <i data-lucide="star" class="w-4 h-4 text-yellow-400"></i>
                                    <span class="text-xs font-semibold text-white">Reviews</span>
                                </button>
                                <button @click="activeTab = 'faqs'" class="p-3 rounded-xl bg-white/5 hover:bg-white/10 border border-white/5 text-left transition flex flex-col gap-1">
                                    <i data-lucide="help-circle" class="w-4 h-4 text-purple-400"></i>
                                    <span class="text-xs font-semibold text-white">FAQs</span>
                                </button>
                            </div>
                        </div>

                        <!-- Inquiries Preview -->
                        <div class="site-card rounded-2xl border border-white/10 p-6 space-y-3">
                            <div class="flex items-center justify-between">
                                <h4 class="text-xs font-bold text-zinc-400 uppercase tracking-wider">Recent Inquiries</h4>
                                <button @click="activeTab = 'messages'" class="text-xs text-theme-primary hover:underline">Inbox</button>
                            </div>
                            @forelse($messages->take(3) as $msg)
                                <div class="p-2.5 rounded-xl bg-white/5 border border-white/5 text-xs space-y-1">
                                    <div class="flex items-center justify-between font-semibold text-white">
                                        <span>{{ $msg->name }}</span>
                                        <span class="text-[10px] text-zinc-500">{{ $msg->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-zinc-400 truncate text-[11px]">{{ $msg->subject }}</p>
                                </div>
                            @empty
                                <div class="text-xs text-zinc-500 py-3 text-center">No messages yet.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- TAB 2: ADVANCED MEDIA & UPLOADS LIBRARY -->
            <!-- ============================================== -->
            <div x-show="activeTab === 'media_library'" class="space-y-8">
                <!-- Direct File Uploader Box -->
                <div class="site-card rounded-2xl border border-white/10 p-6 space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <h3 class="text-base font-bold text-white flex items-center gap-2">
                                <i data-lucide="upload-cloud" class="w-5 h-5 text-cyan-400"></i>
                                <span>Media & Uploads Center</span>
                            </h3>
                            <p class="text-xs text-zinc-400 mt-1">Upload high-resolution images, banners, lookbook photos, or PDF documents to your central production storage.</p>
                        </div>
                    </div>

                    <!-- Upload Form with Drag & Drop styling -->
                    <form method="POST" action="{{ route('admin.media.upload') }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="md:col-span-3">
                                <label class="flex flex-col items-center justify-center p-6 border-2 border-dashed border-white/20 hover:border-[var(--theme-primary)] rounded-2xl cursor-pointer bg-white/5 hover:bg-white/10 transition group">
                                    <i data-lucide="image-plus" class="w-8 h-8 text-zinc-400 group-hover:text-theme-primary mb-2 transition"></i>
                                    <span class="text-xs font-semibold text-white">Choose or Drag & Drop Media Files</span>
                                    <span class="text-[10px] text-zinc-500 mt-1">Supports PNG, JPG, WEBP, GIF, SVG up to 20MB</span>
                                    <input type="file" name="files[]" multiple required class="hidden" @change="$el.closest('form').querySelector('#file-count-label').textContent = $el.files.length + ' file(s) selected'">
                                </label>
                                <div id="file-count-label" class="text-xs text-theme-primary mt-1 font-semibold"></div>
                            </div>
                            <div class="space-y-4 flex flex-col justify-between">
                                <div class="space-y-1.5">
                                    <label class="text-xs font-semibold text-zinc-300">Target Folder / Category</label>
                                    <select name="folder" class="w-full px-3 py-2 text-xs rounded-xl bg-white/5 border border-white/10 text-white">
                                        <option value="general">General Media</option>
                                        <option value="packages">Packages Media</option>
                                        <option value="blogs">Journal & Blogs</option>
                                        <option value="gallery">Portfolio Gallery</option>
                                        <option value="pages">Pages & Banners</option>
                                        <option value="settings">Brand & Logos</option>
                                    </select>
                                </div>
                                <button type="submit" class="w-full py-3 rounded-xl btn-gold-dynamic font-bold text-xs uppercase tracking-wider flex items-center justify-center gap-2 shadow-xl shadow-[var(--theme-primary)]/20">
                                    <i data-lucide="upload" class="w-4 h-4"></i>
                                    <span>Upload Now</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Media Browser & Explorer -->
                <div class="site-card rounded-2xl border border-white/10 p-6 space-y-6">
                    <!-- Filters & Search Bar -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-white/10">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-white uppercase tracking-wider">Filter Folder:</span>
                            <select x-model="mediaFolder" class="px-3 py-1.5 text-xs rounded-xl bg-white/5 border border-white/10 text-white">
                                <option value="all">All Folders ({{ count($mediaFiles) }})</option>
                                <option value="general">General</option>
                                <option value="packages">Packages</option>
                                <option value="blogs">Blogs</option>
                                <option value="gallery">Gallery</option>
                                <option value="pages">Pages</option>
                                <option value="settings">Settings / Brand</option>
                            </select>
                        </div>
                        <div class="relative w-full sm:w-64">
                            <i data-lucide="search" class="w-4 h-4 text-zinc-400 absolute left-3 top-2.5"></i>
                            <input type="text" x-model="mediaSearch" placeholder="Search files..." class="w-full pl-9 pr-3 py-1.5 text-xs rounded-xl bg-white/5 border border-white/10 text-white">
                        </div>
                    </div>

                    @if($mediaFiles->isEmpty())
                        <div class="py-16 text-center text-zinc-500 text-xs">
                            <i data-lucide="folder-open" class="w-12 h-12 mx-auto mb-3 opacity-30"></i>
                            <span>No media files uploaded yet. Use the uploader above to store your high-res photos.</span>
                        </div>
                    @else
                        <!-- Media Grid -->
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                            @foreach($mediaFiles as $media)
                                <div x-show="(mediaFolder === 'all' || mediaFolder === '{{ $media->folder }}') && ('{{ strtolower($media->original_name) }}'.includes(mediaSearch.toLowerCase()) || '{{ strtolower($media->filename) }}'.includes(mediaSearch.toLowerCase()))"
                                     class="site-card rounded-2xl overflow-hidden border border-white/10 group hover:border-[var(--theme-primary)]/50 transition-all flex flex-col justify-between">
                                    <!-- Image Preview Box -->
                                    <div class="aspect-square bg-black/40 relative overflow-hidden flex items-center justify-center cursor-pointer" @click="previewMediaUrl = '{{ $media->url }}'; previewMediaName = '{{ $media->original_name }}'">
                                        @if($media->isImage())
                                            <img src="{{ $media->url }}" alt="{{ $media->original_name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        @else
                                            <i data-lucide="file" class="w-10 h-10 text-zinc-500"></i>
                                        @endif
                                        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                            <span class="p-2 rounded-lg bg-white/20 text-white hover:bg-white/40"><i data-lucide="eye" class="w-4 h-4"></i></span>
                                        </div>
                                    </div>

                                    <!-- File Details -->
                                    <div class="p-3 space-y-1.5 text-left">
                                        <div class="text-[11px] font-semibold text-white truncate" title="{{ $media->original_name }}">{{ $media->original_name }}</div>
                                        <div class="flex items-center justify-between text-[10px] text-zinc-500">
                                            <span>{{ $media->formatted_size }}</span>
                                            <span class="px-1.5 py-0.5 rounded bg-white/5 uppercase text-zinc-400 font-mono">{{ $media->folder }}</span>
                                        </div>
                                        <div class="pt-2 flex items-center justify-between border-t border-white/5">
                                            <!-- Copy URL Button -->
                                            <button type="button" @click="copyToClipboard('{{ $media->url }}')" class="text-[10px] font-semibold text-theme-primary hover:underline flex items-center gap-1" title="Copy public URL">
                                                <i data-lucide="link" class="w-3 h-3"></i>
                                                <span>Copy URL</span>
                                            </button>
                                            <!-- Delete File Form -->
                                            <form method="POST" action="{{ route('admin.media.delete', $media->id) }}" onsubmit="return confirm('Permanently delete this file?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-rose-400 hover:text-rose-300 p-1" title="Delete file">
                                                    <i data-lucide="trash-2" class="w-3 h-3"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- ============================================== -->
            <!-- TAB 3: DYNAMIC PAGES & POLICIES CRUD -->
            <!-- ============================================== -->
            <div x-show="activeTab === 'pages'" class="space-y-8">
                <div class="site-card rounded-2xl border border-white/10 p-6 space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <h3 class="text-base font-bold text-white flex items-center gap-2">
                                <i data-lucide="file-text" class="w-5 h-5 text-indigo-400"></i>
                                <span>Dynamic Pages & Legal Policies</span>
                            </h3>
                            <p class="text-xs text-zinc-400 mt-1">Manage content, banners, and SEO metadata for About Us, Terms, Privacy, Refund, Shipping, and custom pages.</p>
                        </div>
                        <button type="button" @click="editingPage = { id: '', title: '', slug: '', subtitle: '', content: '', banner_image: '', meta_title: '', meta_description: '', meta_keywords: '', is_published: true }" class="px-4 py-2 rounded-xl btn-gold-dynamic text-xs font-bold uppercase tracking-wider flex items-center gap-2 shadow-lg">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            <span>Create New Page</span>
                        </button>
                    </div>

                    <!-- Pages Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-zinc-300">
                            <thead>
                                <tr class="text-zinc-500 border-b border-white/10 pb-2">
                                    <th class="py-3 font-semibold">Page Title</th>
                                    <th class="py-3 font-semibold">Slug / URL</th>
                                    <th class="py-3 font-semibold">Status</th>
                                    <th class="py-3 font-semibold">Last Updated</th>
                                    <th class="py-3 font-semibold text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                @foreach($pages as $p)
                                    <tr class="hover:bg-white/5 transition">
                                        <td class="py-3.5 font-bold text-white">
                                            <div class="flex items-center gap-2">
                                                <i data-lucide="file-code" class="w-4 h-4 text-indigo-400 shrink-0"></i>
                                                <span>{{ $p->title }}</span>
                                            </div>
                                            @if($p->subtitle)
                                                <span class="text-[10px] text-zinc-500 block font-normal">{{ $p->subtitle }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 font-mono text-[11px] text-theme-primary">
                                            @if(in_array($p->slug, ['about', 'terms', 'privacy', 'refund-policy', 'shipping-policy', 'disclaimer']))
                                                <a href="{{ url('/' . ($p->slug === 'refund-policy' ? 'refund-policy' : ($p->slug === 'shipping-policy' ? 'shipping-policy' : $p->slug))) }}" target="_blank" class="hover:underline flex items-center gap-1">
                                                    <span>/{{ $p->slug }}</span>
                                                    <i data-lucide="external-link" class="w-3 h-3"></i>
                                                </a>
                                            @else
                                                <a href="{{ route('custom.page', $p->slug) }}" target="_blank" class="hover:underline flex items-center gap-1">
                                                    <span>/page/{{ $p->slug }}</span>
                                                    <i data-lucide="external-link" class="w-3 h-3"></i>
                                                </a>
                                            @endif
                                        </td>
                                        <td class="py-3.5">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $p->is_published ? 'bg-emerald-500/20 text-emerald-300' : 'bg-zinc-500/20 text-zinc-400' }}">
                                                {{ $p->is_published ? 'Published' : 'Draft' }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 text-zinc-500">{{ $p->updated_at->format('M d, Y') }}</td>
                                        <td class="py-3.5 text-right">
                                            <div class="inline-flex items-center gap-2">
                                                <button type="button" @click="editingPage = {{ json_encode($p) }}" class="px-2.5 py-1 rounded-lg bg-white/5 hover:bg-white/10 text-white font-medium text-[11px] border border-white/10 flex items-center gap-1">
                                                    <i data-lucide="edit-3" class="w-3 h-3 text-theme-primary"></i>
                                                    <span>Edit</span>
                                                </button>
                                                @if(!in_array($p->slug, ['about', 'terms', 'privacy', 'refund-policy', 'shipping-policy', 'disclaimer']))
                                                    <form method="POST" action="{{ route('admin.pages.delete', $p->id) }}" onsubmit="return confirm('Delete page?');" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="p-1.5 rounded-lg text-rose-400 hover:bg-rose-500/10">
                                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- TAB 4: TESTIMONIALS & REVIEWS CRUD -->
            <!-- ============================================== -->
            <div x-show="activeTab === 'testimonials'" class="space-y-8">
                <div class="site-card rounded-2xl border border-white/10 p-6 space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <h3 class="text-base font-bold text-white flex items-center gap-2">
                                <i data-lucide="star" class="w-5 h-5 text-amber-400"></i>
                                <span>Client Reviews & Testimonials</span>
                            </h3>
                            <p class="text-xs text-zinc-400 mt-1">Manage client reviews displayed dynamically on the Homepage and About pages.</p>
                        </div>
                        <button type="button" @click="editingTestimonial = { id: '', client_name: '', client_role: '', rating: 5, content: '', event_type: '', avatar_path: '', is_featured: true, order: 0 }" class="px-4 py-2 rounded-xl btn-gold-dynamic text-xs font-bold uppercase tracking-wider flex items-center gap-2 shadow-lg">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            <span>Add New Review</span>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($testimonials as $testi)
                            <div class="site-card rounded-2xl border border-white/10 p-5 flex flex-col justify-between space-y-4 hover:border-white/30 transition">
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-1 text-amber-400">
                                            @for($i = 0; $i < $testi->rating; $i++)
                                                <i data-lucide="star" class="w-3.5 h-3.5 fill-amber-400"></i>
                                            @endfor
                                        </div>
                                        <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase {{ $testi->is_featured ? 'bg-theme-primary/20 text-theme-primary' : 'bg-white/5 text-zinc-400' }}">
                                            {{ $testi->is_featured ? 'Featured' : 'Standard' }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-zinc-300 italic line-clamp-4 leading-relaxed">&ldquo;{{ $testi->content }}&rdquo;</p>
                                </div>
                                <div class="pt-3 border-t border-white/5 flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <img src="{{ $testi->avatar_path ?? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=200' }}" alt="{{ $testi->client_name }}" class="w-8 h-8 rounded-full object-cover border border-white/20">
                                        <div>
                                            <div class="text-xs font-bold text-white">{{ $testi->client_name }}</div>
                                            <div class="text-[10px] text-zinc-400">{{ $testi->client_role ?? $testi->event_type }}</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <button type="button" @click="editingTestimonial = {{ json_encode($testi) }}" class="p-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-white">
                                            <i data-lucide="edit-3" class="w-3.5 h-3.5 text-theme-primary"></i>
                                        </button>
                                        <form method="POST" action="{{ route('admin.testimonials.delete', $testi->id) }}" onsubmit="return confirm('Delete review?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-rose-400 hover:bg-rose-500/10">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- TAB 5: FAQS MANAGEMENT CRUD -->
            <!-- ============================================== -->
            <div x-show="activeTab === 'faqs'" class="space-y-8">
                <div class="site-card rounded-2xl border border-white/10 p-6 space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <h3 class="text-base font-bold text-white flex items-center gap-2">
                                <i data-lucide="help-circle" class="w-5 h-5 text-purple-400"></i>
                                <span>FAQ Management</span>
                            </h3>
                            <p class="text-xs text-zinc-400 mt-1">Create, reorder and categorize FAQs shown on Home, About, and Contact pages.</p>
                        </div>
                        <button type="button" @click="editingFaq = { id: '', question: '', answer: '', category: 'general', order: 0, is_active: true }" class="px-4 py-2 rounded-xl btn-gold-dynamic text-xs font-bold uppercase tracking-wider flex items-center gap-2 shadow-lg">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            <span>Add New FAQ</span>
                        </button>
                    </div>

                    <div class="space-y-3">
                        @foreach($faqs as $faq)
                            <div class="p-4 rounded-xl bg-white/5 border border-white/5 flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div class="space-y-1 max-w-3xl">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-purple-500/20 text-purple-300">{{ $faq->category }}</span>
                                        <h4 class="text-xs font-bold text-white">{{ $faq->question }}</h4>
                                    </div>
                                    <p class="text-[11px] text-zinc-400 leading-relaxed">{{ $faq->answer }}</p>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <button type="button" @click="editingFaq = {{ json_encode($faq) }}" class="px-3 py-1 rounded-lg bg-white/5 hover:bg-white/10 text-white text-xs border border-white/10 flex items-center gap-1">
                                        <i data-lucide="edit-3" class="w-3 h-3 text-theme-primary"></i>
                                        <span>Edit</span>
                                    </button>
                                    <form method="POST" action="{{ route('admin.faqs.delete', $faq->id) }}" onsubmit="return confirm('Delete FAQ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-rose-400 hover:bg-rose-500/10">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- TAB 6: TEAM & CREW CRUD -->
            <!-- ============================================== -->
            <div x-show="activeTab === 'team'" class="space-y-8">
                <div class="site-card rounded-2xl border border-white/10 p-6 space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <h3 class="text-base font-bold text-white flex items-center gap-2">
                                <i data-lucide="users" class="w-5 h-5 text-emerald-400"></i>
                                <span>Team & Creative Directors</span>
                            </h3>
                            <p class="text-xs text-zinc-400 mt-1">Manage team profiles, roles, biographies and social handles shown on the About Us page.</p>
                        </div>
                        <button type="button" @click="editingTeamMember = { id: '', name: '', role: '', bio: '', image_path: '', instagram_url: '', order: 0, is_active: true }" class="px-4 py-2 rounded-xl btn-gold-dynamic text-xs font-bold uppercase tracking-wider flex items-center gap-2 shadow-lg">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            <span>Add Team Member</span>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($teamMembers as $member)
                            <div class="site-card rounded-2xl overflow-hidden border border-white/10 p-4 text-center space-y-3">
                                <div class="aspect-square w-full rounded-xl overflow-hidden bg-black/40">
                                    <img src="{{ $member->image_path ?? 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400' }}" alt="{{ $member->name }}" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-white">{{ $member->name }}</h4>
                                    <span class="text-[10px] uppercase tracking-wider text-theme-primary font-semibold block">{{ $member->role }}</span>
                                    <p class="text-[11px] text-zinc-400 mt-1 line-clamp-2">{{ $member->bio }}</p>
                                </div>
                                <div class="pt-2 border-t border-white/5 flex items-center justify-center gap-2">
                                    <button type="button" @click="editingTeamMember = {{ json_encode($member) }}" class="px-3 py-1 rounded-lg bg-white/5 hover:bg-white/10 text-white text-xs border border-white/10 flex items-center gap-1">
                                        <i data-lucide="edit-3" class="w-3 h-3 text-theme-primary"></i>
                                        <span>Edit</span>
                                    </button>
                                    <form method="POST" action="{{ route('admin.team.delete', $member->id) }}" onsubmit="return confirm('Delete team member?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-rose-400 hover:bg-rose-500/10">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- TAB 7: PRICING PACKAGES CRUD -->
            <!-- ============================================== -->
            <div x-show="activeTab === 'packages'" class="space-y-8">
                <div class="site-card rounded-2xl border border-white/10 p-6 space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <h3 class="text-base font-bold text-white flex items-center gap-2">
                                <i data-lucide="layers" class="w-5 h-5 text-amber-400"></i>
                                <span>Pricing Packages Management</span>
                            </h3>
                            <p class="text-xs text-zinc-400 mt-1">Create, edit and manage tiered photoshoot pricing packages with custom deliverable lists.</p>
                        </div>
                        <button type="button" @click="editingPackage = { id: '', name: '', price_min: '', price_max: '', description: '', features: '', image_path: '' }" class="px-4 py-2 rounded-xl btn-gold-dynamic text-xs font-bold uppercase tracking-wider flex items-center gap-2 shadow-lg">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            <span>Create Package</span>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($packages as $pkg)
                            @php
                                $features = is_array($pkg->features) ? $pkg->features : json_decode($pkg->features, true) ?? explode(',', $pkg->features);
                            @endphp
                            <div class="site-card rounded-2xl border border-white/10 overflow-hidden flex flex-col justify-between p-5 space-y-4 hover:border-white/30 transition">
                                <div class="space-y-3">
                                    <div class="aspect-video w-full rounded-xl overflow-hidden bg-black/40 relative">
                                        <img src="{{ $pkg->image_path ?: 'https://images.unsplash.com/photo-1492691527719-9d1e07e534b4?w=800' }}" alt="{{ $pkg->name }}" class="w-full h-full object-cover">
                                    </div>
                                    <h4 class="text-base font-bold text-white">{{ $pkg->name }}</h4>
                                    <div class="text-lg font-serif font-bold text-theme-primary">
                                        {{ $siteSettings['currency_symbol'] ?? '₹' }}{{ number_format($pkg->price_min) }} - {{ $siteSettings['currency_symbol'] ?? '₹' }}{{ number_format($pkg->price_max) }}
                                    </div>
                                    <p class="text-xs text-zinc-400 line-clamp-2">{!! strip_tags($pkg->description) !!}</p>
                                </div>
                                <div class="pt-3 border-t border-white/5 flex items-center justify-between">
                                    <a href="{{ route('booking.checkout', $pkg->slug) }}" target="_blank" class="text-[11px] text-zinc-400 hover:text-white flex items-center gap-1">
                                        <span>Checkout</span>
                                        <i data-lucide="external-link" class="w-3 h-3"></i>
                                    </a>
                                    <div class="flex items-center gap-1">
                                        <button type="button" @click="editingPackage = {
                                            id: '{{ $pkg->id }}',
                                            name: '{{ addslashes($pkg->name) }}',
                                            price_min: '{{ $pkg->price_min }}',
                                            price_max: '{{ $pkg->price_max }}',
                                            description: `{{ addslashes($pkg->description) }}`,
                                            features: `{{ is_array($pkg->features) ? implode("\n", $pkg->features) : $pkg->features }}`,
                                            image_path: '{{ $pkg->image_path }}'
                                        }" class="px-3 py-1 rounded-lg bg-white/5 hover:bg-white/10 text-white text-xs border border-white/10 flex items-center gap-1">
                                            <i data-lucide="edit-3" class="w-3 h-3 text-theme-primary"></i>
                                            <span>Edit</span>
                                        </button>
                                        <form method="POST" action="{{ route('admin.package.delete', $pkg->id) }}" onsubmit="return confirm('Delete package?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-rose-400 hover:bg-rose-500/10">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- TAB 8: PORTFOLIO GALLERY CRUD -->
            <!-- ============================================== -->
            <div x-show="activeTab === 'gallery'" class="space-y-8">
                <div class="site-card rounded-2xl border border-white/10 p-6 space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <h3 class="text-base font-bold text-white flex items-center gap-2">
                                <i data-lucide="image" class="w-5 h-5 text-emerald-400"></i>
                                <span>Master Portfolio Gallery</span>
                            </h3>
                            <p class="text-xs text-zinc-400 mt-1">Upload and organize master plates into categories (Wedding, Fashion, Editorial, Portrait, Event).</p>
                        </div>
                        <button type="button" @click="editingGallery = { id: '', title: '', category: 'Wedding', image_path: '' }" class="px-4 py-2 rounded-xl btn-gold-dynamic text-xs font-bold uppercase tracking-wider flex items-center gap-2 shadow-lg">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            <span>Upload Gallery Photo</span>
                        </button>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @foreach($gallery as $item)
                            <div class="site-card rounded-2xl overflow-hidden border border-white/10 group flex flex-col justify-between">
                                <div class="aspect-square relative overflow-hidden bg-black/40">
                                    <img src="{{ $item->image_path }}" alt="{{ $item->title }}" class="w-full h-full object-cover">
                                    <span class="absolute top-2 left-2 px-2 py-0.5 rounded text-[8px] font-bold uppercase bg-black/70 text-theme-primary backdrop-blur-sm">
                                        {{ $item->category }}
                                    </span>
                                </div>
                                <div class="p-3 space-y-2">
                                    <h4 class="text-xs font-bold text-white truncate">{{ $item->title }}</h4>
                                    <div class="flex items-center justify-between pt-1 border-t border-white/5">
                                        <button type="button" @click="editingGallery = {{ json_encode($item) }}" class="text-[10px] text-theme-primary hover:underline">Edit</button>
                                        <form method="POST" action="{{ route('admin.gallery.delete', $item->id) }}" onsubmit="return confirm('Delete photo?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-400 hover:text-rose-300 text-[10px]">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- TAB 9: STUDIO JOURNAL & BLOGS CRUD -->
            <!-- ============================================== -->
            <div x-show="activeTab === 'blogs'" class="space-y-8">
                <div class="site-card rounded-2xl border border-white/10 p-6 space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <h3 class="text-base font-bold text-white flex items-center gap-2">
                                <i data-lucide="book-open" class="w-5 h-5 text-blue-400"></i>
                                <span>Studio Journal & Editorial Articles</span>
                            </h3>
                            <p class="text-xs text-zinc-400 mt-1">Publish photography breakdowns, behind-the-scenes stories, and SEO-optimized masterclasses.</p>
                        </div>
                        <button type="button" @click="editingBlog = { id: '', title: '', excerpt: '', content: '', image_path: '', meta_title: '', meta_description: '', meta_keywords: '' }" class="px-4 py-2 rounded-xl btn-gold-dynamic text-xs font-bold uppercase tracking-wider flex items-center gap-2 shadow-lg">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            <span>Write New Article</span>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($blogs as $article)
                            <div class="site-card rounded-2xl border border-white/10 overflow-hidden flex flex-col justify-between p-5 space-y-4 hover:border-white/30 transition">
                                <div class="space-y-3">
                                    <div class="aspect-[16/10] w-full rounded-xl overflow-hidden bg-black/40">
                                        <img src="{{ $article->image_path ?: 'https://images.unsplash.com/photo-1542038784456-1ea8e935640e?w=800' }}" alt="{{ $article->title }}" class="w-full h-full object-cover">
                                    </div>
                                    <div class="text-[10px] text-zinc-500 uppercase">{{ $article->created_at->format('M d, Y') }}</div>
                                    <h4 class="text-sm font-bold text-white line-clamp-2">{{ $article->title }}</h4>
                                    <p class="text-xs text-zinc-400 line-clamp-2">{{ $article->excerpt }}</p>
                                </div>
                                <div class="pt-3 border-t border-white/5 flex items-center justify-between">
                                    <a href="{{ route('blog.single', $article->slug) }}" target="_blank" class="text-[11px] text-theme-primary hover:underline flex items-center gap-1">
                                        <span>View Live</span>
                                        <i data-lucide="external-link" class="w-3 h-3"></i>
                                    </a>
                                    <div class="flex items-center gap-1">
                                        <button type="button" @click="editingBlog = {
                                            id: '{{ $article->id }}',
                                            title: '{{ addslashes($article->title) }}',
                                            excerpt: `{{ addslashes($article->excerpt) }}`,
                                            content: `{{ addslashes($article->content) }}`,
                                            image_path: '{{ $article->image_path }}',
                                            meta_title: '{{ addslashes($article->meta_title ?? '') }}',
                                            meta_description: `{{ addslashes($article->meta_description ?? '') }}`,
                                            meta_keywords: '{{ addslashes($article->meta_keywords ?? '') }}'
                                        }" class="px-3 py-1 rounded-lg bg-white/5 hover:bg-white/10 text-white text-xs border border-white/10 flex items-center gap-1">
                                            <i data-lucide="edit-3" class="w-3 h-3 text-theme-primary"></i>
                                            <span>Edit</span>
                                        </button>
                                        <form method="POST" action="{{ route('admin.blog.delete', $article->id) }}" onsubmit="return confirm('Delete article?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-rose-400 hover:bg-rose-500/10">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- TAB 10: BOOKINGS MANAGEMENT -->
            <!-- ============================================== -->
            <div x-show="activeTab === 'bookings'" class="space-y-8">
                <div class="site-card rounded-2xl border border-white/10 p-6 space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-bold text-white flex items-center gap-2">
                                <i data-lucide="calendar" class="w-5 h-5 text-cyan-400"></i>
                                <span>All Client Bookings</span>
                            </h3>
                            <p class="text-xs text-zinc-400 mt-1">Manage session statuses and review customer bookings.</p>
                        </div>
                    </div>

                    @if($bookings->isEmpty())
                        <div class="py-12 text-center text-zinc-500 text-xs">No bookings recorded.</div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs text-zinc-300">
                                <thead>
                                    <tr class="text-zinc-500 border-b border-white/10 pb-2">
                                        <th class="py-3 font-semibold">Ref ID</th>
                                        <th class="py-3 font-semibold">Client</th>
                                        <th class="py-3 font-semibold">Package</th>
                                        <th class="py-3 font-semibold">Date</th>
                                        <th class="py-3 font-semibold">Amount</th>
                                        <th class="py-3 font-semibold">Status</th>
                                        <th class="py-3 font-semibold text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/5">
                                    @foreach($bookings as $b)
                                        <tr class="hover:bg-white/5 transition">
                                            <td class="py-3 font-mono text-[11px] text-theme-primary">#{{ $b->id }}</td>
                                            <td class="py-3 font-medium text-white">
                                                <div>{{ $b->user->name ?? 'Guest Client' }}</div>
                                                <div class="text-[10px] text-zinc-500">{{ $b->user->email ?? $b->user->phone ?? '' }}</div>
                                            </td>
                                            <td class="py-3">{{ $b->package->name ?? 'Custom Tier' }}</td>
                                            <td class="py-3 text-zinc-400">{{ \Carbon\Carbon::parse($b->booking_date)->format('M d, Y') }}</td>
                                            <td class="py-3 font-bold text-emerald-400">{{ $siteSettings['currency_symbol'] ?? '₹' }}{{ number_format($b->amount) }}</td>
                                            <td class="py-3">
                                                <form method="POST" action="{{ route('admin.booking.updateStatus', $b->id) }}" class="inline">
                                                    @csrf
                                                    <select name="status" onchange="this.form.submit()" class="px-2 py-1 text-[10px] font-bold uppercase rounded-lg bg-white/5 border border-white/10 text-white cursor-pointer">
                                                        <option value="pending" {{ $b->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                        <option value="progress" {{ $b->status === 'progress' ? 'selected' : '' }}>In Progress</option>
                                                        <option value="active" {{ $b->status === 'active' ? 'selected' : '' }}>Active</option>
                                                        <option value="next_level" {{ $b->status === 'next_level' ? 'selected' : '' }}>Next Level</option>
                                                        <option value="completed" {{ $b->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                                        <option value="cancelled" {{ $b->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                    </select>
                                                </form>
                                            </td>
                                            <td class="py-3 text-right">
                                                <form method="POST" action="{{ route('admin.booking.delete', $b->id) }}" onsubmit="return confirm('Delete booking record?');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-1.5 rounded-lg text-rose-400 hover:bg-rose-500/10">
                                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- ============================================== -->
            <!-- TAB 11: TRANSACTIONS & PAYMENTS -->
            <!-- ============================================== -->
            <div x-show="activeTab === 'transactions'" class="space-y-8">
                <div class="site-card rounded-2xl border border-white/10 p-6 space-y-6">
                    <div>
                        <h3 class="text-base font-bold text-white flex items-center gap-2">
                            <i data-lucide="credit-card" class="w-5 h-5 text-emerald-400"></i>
                            <span>Captured Gateway Transactions</span>
                        </h3>
                        <p class="text-xs text-zinc-400 mt-1">Real-time payment logs captured via Cashfree and Razorpay gateways.</p>
                    </div>

                    @if($transactions->isEmpty() && $payments->isEmpty())
                        <div class="py-12 text-center text-zinc-500 text-xs">No transactions captured yet.</div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs text-zinc-300">
                                <thead>
                                    <tr class="text-zinc-500 border-b border-white/10 pb-2">
                                        <th class="py-3 font-semibold">Gateway Txn ID</th>
                                        <th class="py-3 font-semibold">User</th>
                                        <th class="py-3 font-semibold">Amount</th>
                                        <th class="py-3 font-semibold">Gateway</th>
                                        <th class="py-3 font-semibold">Status</th>
                                        <th class="py-3 font-semibold">Timestamp</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/5">
                                    @foreach($transactions as $txn)
                                        <tr class="hover:bg-white/5 transition">
                                            <td class="py-3 font-mono text-[11px] text-theme-primary">{{ $txn->gateway_transaction_id ?? ($txn->gateway_order_id ?? '#'.$txn->id) }}</td>
                                            <td class="py-3 font-medium text-white">{{ $txn->user->name ?? 'Guest Client' }}</td>
                                            <td class="py-3 font-bold text-emerald-400">{{ $siteSettings['currency_symbol'] ?? '₹' }}{{ number_format($txn->amount) }}</td>
                                            <td class="py-3 uppercase font-mono text-[10px] text-zinc-400">{{ $txn->gateway }}</td>
                                            <td class="py-3">
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $txn->status === 'captured' || $txn->status === 'success' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-rose-500/20 text-rose-300' }}">
                                                    {{ $txn->status }}
                                                </span>
                                            </td>
                                            <td class="py-3 text-zinc-500">{{ $txn->created_at->format('M d, Y h:i A') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- ============================================== -->
            <!-- TAB 12: INQUIRIES INBOX -->
            <!-- ============================================== -->
            <div x-show="activeTab === 'messages'" class="space-y-8">
                <div class="site-card rounded-2xl border border-white/10 p-6 space-y-6">
                    <div>
                        <h3 class="text-base font-bold text-white flex items-center gap-2">
                            <i data-lucide="mail" class="w-5 h-5 text-violet-400"></i>
                            <span>Contact Inquiries Inbox</span>
                        </h3>
                        <p class="text-xs text-zinc-400 mt-1">Customer inquiries submitted through the website contact concierge.</p>
                    </div>

                    @if($messages->isEmpty())
                        <div class="py-12 text-center text-zinc-500 text-xs">No contact inquiries received.</div>
                    @else
                        <div class="space-y-4">
                            @foreach($messages as $msg)
                                <div class="p-5 rounded-2xl border border-white/10 {{ $msg->status === 'unread' ? 'bg-white/10 border-theme-primary/40' : 'bg-white/5' }} space-y-3">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl bg-violet-500/10 border border-violet-500/20 flex items-center justify-center text-violet-400 font-bold text-sm">
                                                {{ strtoupper(substr($msg->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-white text-sm flex items-center gap-2">
                                                    <span>{{ $msg->name }}</span>
                                                    @if($msg->status === 'unread')
                                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-rose-500 text-white">NEW</span>
                                                    @endif
                                                </div>
                                                <a href="mailto:{{ $msg->email }}" class="text-xs text-theme-primary hover:underline">{{ $msg->email }}</a>
                                            </div>
                                        </div>
                                        <div class="text-[11px] text-zinc-500">{{ $msg->created_at->format('M d, Y h:i A') }}</div>
                                    </div>
                                    <div class="pt-2 border-t border-white/5 space-y-1">
                                        <div class="text-xs font-semibold text-white">{{ $msg->subject }}</div>
                                        <p class="text-xs text-zinc-300 leading-relaxed">{{ $msg->message }}</p>
                                    </div>
                                    <div class="pt-3 border-t border-white/5 flex items-center justify-between">
                                        <a href="mailto:{{ $msg->email }}?subject=Re: {{ urlencode($msg->subject) }}" class="px-3 py-1.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-white text-xs font-semibold flex items-center gap-1.5">
                                            <i data-lucide="reply" class="w-3.5 h-3.5 text-theme-primary"></i>
                                            <span>Reply via Email</span>
                                        </a>
                                        <div class="flex items-center gap-2">
                                            @if($msg->status === 'unread')
                                                <form method="POST" action="{{ route('admin.message.read', $msg->id) }}">
                                                    @csrf
                                                    <button type="submit" class="text-xs text-zinc-400 hover:text-white">Mark as Read</button>
                                                </form>
                                            @endif
                                            <form method="POST" action="{{ route('admin.message.delete', $msg->id) }}" onsubmit="return confirm('Delete message?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg text-rose-400 hover:bg-rose-500/10">
                                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- ============================================== -->
            <!-- TAB 13: PAYMENT GATEWAYS CONFIG -->
            <!-- ============================================== -->
            <div x-show="activeTab === 'gateways'" class="space-y-8">
                <form method="POST" action="{{ route('admin.settings.save') }}" class="site-card rounded-2xl border border-white/10 p-6 md:p-8 space-y-6">
                    @csrf
                    <div>
                        <h3 class="text-base font-bold text-white flex items-center gap-2">
                            <i data-lucide="shield-check" class="w-5 h-5 text-emerald-400"></i>
                            <span>Multi-Gateway Payment Config</span>
                        </h3>
                        <p class="text-xs text-zinc-400 mt-1">Toggle between Cashfree PG and Razorpay with instant failover.</p>
                    </div>

                    <div class="space-y-2 p-4 rounded-xl bg-white/5 border border-white/5">
                        <label class="text-xs font-semibold text-zinc-300 block">Active Primary Payment Gateway</label>
                        <select name="active_payment_gateway" class="w-full px-3 py-2 text-xs rounded-xl bg-black/40 border border-white/10 text-white font-semibold">
                            <option value="cashfree" {{ ($allSettings['active_payment_gateway'] ?? 'cashfree') === 'cashfree' ? 'selected' : '' }}>Cashfree PG (Instant UPI & QR)</option>
                            <option value="razorpay" {{ ($allSettings['active_payment_gateway'] ?? '') === 'razorpay' ? 'selected' : '' }}>Razorpay Software Gateway</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4">
                        <!-- Cashfree Keys -->
                        <div class="space-y-4 p-5 rounded-xl bg-white/5 border border-white/5">
                            <h4 class="text-xs font-bold text-emerald-400 uppercase tracking-wider flex items-center gap-1.5">
                                <i data-lucide="credit-card" class="w-4 h-4"></i>
                                <span>Cashfree API Credentials</span>
                            </h4>
                            <div class="space-y-1">
                                <label class="text-[11px] font-semibold text-zinc-400">Cashfree App ID</label>
                                <input type="text" name="cashfree_app_id" value="{{ $allSettings['cashfree_app_id'] ?? '' }}" class="w-full px-3 py-2 text-xs rounded-lg bg-black/40 border border-white/10 text-white font-mono">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[11px] font-semibold text-zinc-400">Cashfree Secret Key</label>
                                <input type="password" name="cashfree_secret_key" value="{{ $allSettings['cashfree_secret_key'] ?? '' }}" class="w-full px-3 py-2 text-xs rounded-lg bg-black/40 border border-white/10 text-white font-mono">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[11px] font-semibold text-zinc-400">Environment</label>
                                <select name="cashfree_environment" class="w-full px-3 py-2 text-xs rounded-lg bg-black/40 border border-white/10 text-white font-mono">
                                    <option value="TEST" {{ ($allSettings['cashfree_environment'] ?? 'TEST') === 'TEST' ? 'selected' : '' }}>Sandbox / TEST</option>
                                    <option value="PRODUCTION" {{ ($allSettings['cashfree_environment'] ?? '') === 'PRODUCTION' ? 'selected' : '' }}>Live PRODUCTION</option>
                                </select>
                            </div>
                        </div>

                        <!-- Razorpay Keys -->
                        <div class="space-y-4 p-5 rounded-xl bg-white/5 border border-white/5">
                            <h4 class="text-xs font-bold text-cyan-400 uppercase tracking-wider flex items-center gap-1.5">
                                <i data-lucide="zap" class="w-4 h-4"></i>
                                <span>Razorpay API Credentials</span>
                            </h4>
                            <div class="space-y-1">
                                <label class="text-[11px] font-semibold text-zinc-400">Razorpay Key ID</label>
                                <input type="text" name="razorpay_key_id" value="{{ $allSettings['razorpay_key_id'] ?? '' }}" class="w-full px-3 py-2 text-xs rounded-lg bg-black/40 border border-white/10 text-white font-mono">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[11px] font-semibold text-zinc-400">Razorpay Key Secret</label>
                                <input type="password" name="razorpay_key_secret" value="{{ $allSettings['razorpay_key_secret'] ?? '' }}" class="w-full px-3 py-2 text-xs rounded-lg bg-black/40 border border-white/10 text-white font-mono">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[11px] font-semibold text-zinc-400">Simulation Mode</label>
                                <select name="razorpay_simulation_mode" class="w-full px-3 py-2 text-xs rounded-lg bg-black/40 border border-white/10 text-white font-mono">
                                    <option value="1" {{ ($allSettings['razorpay_simulation_mode'] ?? '1') === '1' ? 'selected' : '' }}>Test / Simulation (Auto-Approve)</option>
                                    <option value="0" {{ ($allSettings['razorpay_simulation_mode'] ?? '') === '0' ? 'selected' : '' }}>Live Gateway</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="px-6 py-3 rounded-xl btn-gold-dynamic text-xs font-bold uppercase tracking-wider flex items-center gap-2 shadow-xl shadow-[var(--theme-primary)]/20">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            <span>Save Gateway Settings</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- ============================================== -->
            <!-- TAB 14: FAST2SMS & MULTI-GATEWAY SMS ENGINE -->
            <!-- ============================================== -->
            <div x-show="activeTab === 'sms_settings'" class="space-y-8">
                <div>
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <i data-lucide="message-square" class="w-5 h-5 text-amber-400"></i>
                        <span>Fast2SMS Quick SMS Pack & Multi-Gateway SMS Engine</span>
                    </h3>
                    <p class="text-xs text-zinc-400 mt-1">Configure Fast2SMS Quick SMS pack (Route 'q'), Twilio, MSG91, Custom HTTP Gateways, dynamic message templates, and live testing.</p>
                </div>

                <!-- Dynamic Variables Reference Card -->
                <div class="site-card rounded-2xl border border-amber-500/30 p-5 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-amber-300 flex items-center gap-1.5">
                            <i data-lucide="tags" class="w-4 h-4"></i> Available Template Placeholders
                        </span>
                        <span class="text-[10px] text-zinc-400">Auto-replaced during dispatch</span>
                    </div>
                    <div class="flex flex-wrap gap-2 text-[11px] font-mono">
                        <span class="px-2.5 py-1 rounded-lg bg-white/5 border border-white/10 text-theme-primary font-bold">{name} <span class="font-sans font-normal text-zinc-400 text-[10px]">(Client Name)</span></span>
                        <span class="px-2.5 py-1 rounded-lg bg-white/5 border border-white/10 text-emerald-400 font-bold">{amount} <span class="font-sans font-normal text-zinc-400 text-[10px]">(Amount Paid)</span></span>
                        <span class="px-2.5 py-1 rounded-lg bg-white/5 border border-white/10 text-cyan-400 font-bold">{booking_id} <span class="font-sans font-normal text-zinc-400 text-[10px]">(Booking #)</span></span>
                        <span class="px-2.5 py-1 rounded-lg bg-white/5 border border-white/10 text-amber-400 font-bold">{package} <span class="font-sans font-normal text-zinc-400 text-[10px]">(Package Title)</span></span>
                        <span class="px-2.5 py-1 rounded-lg bg-white/5 border border-white/10 text-indigo-400 font-bold">{gateway} <span class="font-sans font-normal text-zinc-400 text-[10px]">(Cashfree / Razorpay)</span></span>
                        <span class="px-2.5 py-1 rounded-lg bg-white/5 border border-white/10 text-pink-400 font-bold">{payment_id} <span class="font-sans font-normal text-zinc-400 text-[10px]">(Transaction ID)</span></span>
                        <span class="px-2.5 py-1 rounded-lg bg-white/5 border border-white/10 text-zinc-300 font-bold">{site_name} <span class="font-sans font-normal text-zinc-400 text-[10px]">(Studio Brand)</span></span>
                        <span class="px-2.5 py-1 rounded-lg bg-white/5 border border-white/10 text-emerald-300 font-bold">{currency} <span class="font-sans font-normal text-zinc-400 text-[10px]">(Currency Symbol)</span></span>
                        <span class="px-2.5 py-1 rounded-lg bg-white/5 border border-white/10 text-cyan-300 font-bold">{otp} <span class="font-sans font-normal text-zinc-400 text-[10px]">(Verification Code)</span></span>
                        <span class="px-2.5 py-1 rounded-lg bg-white/5 border border-white/10 text-zinc-400 font-bold">{datetime} <span class="font-sans font-normal text-zinc-400 text-[10px]">(Date & Time)</span></span>
                        <span class="px-2.5 py-1 rounded-lg bg-white/5 border border-white/10 text-rose-400 font-bold">{reason} <span class="font-sans font-normal text-zinc-400 text-[10px]">(Failure Reason)</span></span>
                        <span class="px-2.5 py-1 rounded-lg bg-white/5 border border-white/10 text-blue-400 font-bold">{retry_url} <span class="font-sans font-normal text-zinc-400 text-[10px]">(Payment Retry Link)</span></span>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    <!-- Left 7 cols: Settings, Gateway Providers & Templates -->
                    <div class="lg:col-span-7 space-y-6">
                        <form method="POST" action="{{ route('admin.settings.save') }}" class="site-card rounded-2xl border border-white/10 p-6 space-y-6">
                            @csrf
                            
                            <!-- Master Gateway Switch & Driver Picker -->
                            <div class="space-y-4 pb-6 border-b border-white/10">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-bold text-white flex items-center gap-2">
                                        <i data-lucide="sliders" class="w-4 h-4 text-theme-primary"></i>
                                        <span>Primary SMS Gateway Driver</span>
                                    </span>
                                    
                                    <!-- Enable / Disable Toggle -->
                                    <div class="flex items-center gap-2">
                                        <label class="text-xs text-zinc-400">SMS Transmission</label>
                                        <select name="sms_enabled" class="px-2.5 py-1 rounded-lg text-xs bg-black/40 border border-white/10 text-white focus:border-theme-primary">
                                            <option value="1" {{ ($allSettings['sms_enabled'] ?? '1') == '1' ? 'selected' : '' }}>Enabled</option>
                                            <option value="0" {{ ($allSettings['sms_enabled'] ?? '1') == '0' ? 'selected' : '' }}>Disabled (Log Only)</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="space-y-1">
                                    <label class="text-xs font-semibold text-zinc-300">Active Gateway Pack</label>
                                    <select name="sms_driver" class="w-full px-4 py-2.5 rounded-xl bg-black/40 border border-white/10 text-white text-xs focus:border-theme-primary font-medium">
                                        <option value="fast2sms" {{ ($allSettings['sms_driver'] ?? 'fast2sms') == 'fast2sms' ? 'selected' : '' }}>★ Fast2SMS (Quick SMS Pack - Instant India Delivery)</option>
                                        <option value="auto" {{ ($allSettings['sms_driver'] ?? '') == 'auto' ? 'selected' : '' }}>Multi-Gateway Auto Failover (Fast2SMS → Twilio → MSG91)</option>
                                        <option value="twilio" {{ ($allSettings['sms_driver'] ?? '') == 'twilio' ? 'selected' : '' }}>Twilio (Global International SMS)</option>
                                        <option value="msg91" {{ ($allSettings['sms_driver'] ?? '') == 'msg91' ? 'selected' : '' }}>MSG91 (Enterprise Flow API & DLT)</option>
                                        <option value="custom_http" {{ ($allSettings['sms_driver'] ?? '') == 'custom_http' ? 'selected' : '' }}>Custom HTTP Generic SMS URL Gateway</option>
                                        <option value="simulation" {{ ($allSettings['sms_driver'] ?? '') == 'simulation' ? 'selected' : '' }}>Log & Simulation Mode (Dev / Test without live credits)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- 1. Fast2SMS Quick SMS Pack Credentials -->
                            <div class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/20 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-amber-300 flex items-center gap-1.5">
                                        <i data-lucide="zap" class="w-3.5 h-3.5 text-amber-400"></i> Fast2SMS Quick SMS Pack Credentials
                                    </span>
                                    <span class="text-[10px] text-zinc-400 font-mono">Route: Quick SMS (q)</span>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div class="sm:col-span-2 space-y-1">
                                        <label class="text-[11px] text-zinc-300 font-semibold">Fast2SMS Authorization API Key</label>
                                        <input type="password" name="fast2sms_api_key" value="{{ $allSettings['fast2sms_api_key'] ?? '' }}" placeholder="Paste Fast2SMS API Key from fast2sms.com" class="w-full px-3 py-2 rounded-lg bg-black/40 border border-white/10 text-white text-xs focus:border-theme-primary font-mono">
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[11px] text-zinc-300 font-semibold">SMS Route</label>
                                        <select name="fast2sms_route" class="w-full px-3 py-2 rounded-lg bg-black/40 border border-white/10 text-white text-xs focus:border-theme-primary">
                                            <option value="q" {{ ($allSettings['fast2sms_route'] ?? 'q') == 'q' ? 'selected' : '' }}>Quick SMS (q) - Recommended</option>
                                            <option value="otp" {{ ($allSettings['fast2sms_route'] ?? '') == 'otp' ? 'selected' : '' }}>OTP Route (otp)</option>
                                            <option value="v3" {{ ($allSettings['fast2sms_route'] ?? '') == 'v3' ? 'selected' : '' }}>Promotional (v3)</option>
                                            <option value="dlt" {{ ($allSettings['fast2sms_route'] ?? '') == 'dlt' ? 'selected' : '' }}>DLT Manual (dlt)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                                    <div class="space-y-1">
                                        <label class="text-[11px] text-zinc-400">Sender ID (Optional for DLT)</label>
                                        <input type="text" name="fast2sms_sender_id" value="{{ $allSettings['fast2sms_sender_id'] ?? '' }}" placeholder="e.g. FSTSMS or MIDDUK" class="w-full px-3 py-2 rounded-lg bg-black/40 border border-white/10 text-white text-xs focus:border-theme-primary font-mono">
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[11px] text-zinc-400">Entity ID (Optional for DLT)</label>
                                        <input type="text" name="fast2sms_entity_id" value="{{ $allSettings['fast2sms_entity_id'] ?? '' }}" placeholder="e.g. 120115..." class="w-full px-3 py-2 rounded-lg bg-black/40 border border-white/10 text-white text-xs focus:border-theme-primary font-mono">
                                    </div>
                                </div>
                            </div>

                            <!-- 2. Twilio Pack Credentials -->
                            <div class="p-4 rounded-xl bg-indigo-500/10 border border-indigo-500/20 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-indigo-300 flex items-center gap-1.5">
                                        <i data-lucide="globe" class="w-3.5 h-3.5 text-indigo-400"></i> Twilio Global Gateway Credentials
                                    </span>
                                    <span class="text-[10px] text-zinc-400 font-mono">International</span>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div class="space-y-1">
                                        <label class="text-[11px] text-zinc-300 font-semibold">Twilio Account SID</label>
                                        <input type="text" name="twilio_sid" value="{{ $allSettings['twilio_sid'] ?? '' }}" placeholder="ACXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX" class="w-full px-3 py-2 rounded-lg bg-black/40 border border-white/10 text-white text-xs focus:border-theme-primary font-mono">
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[11px] text-zinc-300 font-semibold">Twilio Auth Token</label>
                                        <input type="password" name="twilio_token" value="{{ $allSettings['twilio_token'] ?? '' }}" placeholder="Your Twilio Auth Token" class="w-full px-3 py-2 rounded-lg bg-black/40 border border-white/10 text-white text-xs focus:border-theme-primary font-mono">
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[11px] text-zinc-400">Twilio From Phone Number</label>
                                    <input type="text" name="twilio_from_number" value="{{ $allSettings['twilio_from_number'] ?? '' }}" placeholder="+1234567890" class="w-full px-3 py-2 rounded-lg bg-black/40 border border-white/10 text-white text-xs focus:border-theme-primary font-mono">
                                </div>
                            </div>

                            <!-- 3. MSG91 Pack Credentials -->
                            <div class="p-4 rounded-xl bg-cyan-500/10 border border-cyan-500/20 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-cyan-300 flex items-center gap-1.5">
                                        <i data-lucide="radio" class="w-3.5 h-3.5 text-cyan-400"></i> MSG91 Enterprise Gateway Credentials
                                    </span>
                                    <span class="text-[10px] text-zinc-400 font-mono">Flow / DLT</span>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div class="space-y-1 sm:col-span-2">
                                        <label class="text-[11px] text-zinc-300 font-semibold">MSG91 Auth Key</label>
                                        <input type="password" name="msg91_auth_key" value="{{ $allSettings['msg91_auth_key'] ?? '' }}" placeholder="Enter MSG91 Auth Key" class="w-full px-3 py-2 rounded-lg bg-black/40 border border-white/10 text-white text-xs focus:border-theme-primary font-mono">
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[11px] text-zinc-300 font-semibold">Sender ID</label>
                                        <input type="text" name="msg91_sender_id" value="{{ $allSettings['msg91_sender_id'] ?? 'MIDDUK' }}" placeholder="MIDDUK" class="w-full px-3 py-2 rounded-lg bg-black/40 border border-white/10 text-white text-xs focus:border-theme-primary font-mono">
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[11px] text-zinc-400">MSG91 DLT / Template ID (Optional)</label>
                                    <input type="text" name="msg91_dlt_template_id" value="{{ $allSettings['msg91_dlt_template_id'] ?? '' }}" placeholder="e.g. 6423..." class="w-full px-3 py-2 rounded-lg bg-black/40 border border-white/10 text-white text-xs focus:border-theme-primary font-mono">
                                </div>
                            </div>

                            <!-- 4. Custom HTTP SMS Pack -->
                            <div class="p-4 rounded-xl bg-purple-500/10 border border-purple-500/20 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-purple-300 flex items-center gap-1.5">
                                        <i data-lucide="link" class="w-3.5 h-3.5 text-purple-400"></i> Custom HTTP SMS Gateway URL
                                    </span>
                                    <span class="text-[10px] text-zinc-400 font-mono">Generic REST</span>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div class="sm:col-span-2 space-y-1">
                                        <label class="text-[11px] text-zinc-300 font-semibold">Custom API Endpoint URL</label>
                                        <input type="text" name="custom_sms_url" value="{{ $allSettings['custom_sms_url'] ?? '' }}" placeholder="https://api.sms-provider.com/send?to={phone}&msg={message}&api_key=XYZ" class="w-full px-3 py-2 rounded-lg bg-black/40 border border-white/10 text-white text-xs focus:border-theme-primary font-mono">
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[11px] text-zinc-300 font-semibold">HTTP Method</label>
                                        <select name="custom_sms_method" class="w-full px-3 py-2 rounded-lg bg-black/40 border border-white/10 text-white text-xs focus:border-theme-primary">
                                            <option value="GET" {{ ($allSettings['custom_sms_method'] ?? 'GET') == 'GET' ? 'selected' : '' }}>GET</option>
                                            <option value="POST" {{ ($allSettings['custom_sms_method'] ?? '') == 'POST' ? 'selected' : '' }}>POST (Form Urlencoded)</option>
                                            <option value="JSON" {{ ($allSettings['custom_sms_method'] ?? '') == 'JSON' ? 'selected' : '' }}>POST (JSON Body)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[11px] text-zinc-400">Custom HTTP Headers (JSON format, optional)</label>
                                    <input type="text" name="custom_sms_headers" value="{{ $allSettings['custom_sms_headers'] ?? '' }}" placeholder='{"Authorization": "Bearer YOUR_TOKEN"}' class="w-full px-3 py-2 rounded-lg bg-black/40 border border-white/10 text-white text-xs focus:border-theme-primary font-mono">
                                </div>
                            </div>

                            <!-- Custom Message Templates Section -->
                            <div class="space-y-5 pt-4 border-t border-white/10">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-white uppercase tracking-wider block flex items-center gap-1.5">
                                        <i data-lucide="file-text" class="w-4 h-4 text-theme-primary"></i> Customizable Message Templates
                                    </span>
                                    <span class="text-[10px] text-emerald-400">Fast2SMS Quick Pack Ready</span>
                                </div>
                                
                                <!-- 1. Transaction Initiated / Bank OTP Notice SMS -->
                                <div class="space-y-1.5 p-4 rounded-xl bg-white/5 border border-white/5">
                                    <label class="text-xs font-bold text-white flex items-center justify-between">
                                        <span class="flex items-center gap-1.5 text-cyan-300">
                                            <i data-lucide="send" class="w-3.5 h-3.5"></i> 1. Payment Initiated / Bank OTP Notice SMS Template
                                        </span>
                                        <span class="text-[10px] text-zinc-400">Dispatched when checkout begins</span>
                                    </label>
                                    <p class="text-[11px] text-zinc-400">Sent to customer when transaction starts so they know to authorize the bank OTP.</p>
                                    <textarea name="sms_template_payment_initiated" rows="2" class="w-full px-3 py-2 rounded-xl bg-black/40 border border-white/10 text-white text-xs font-mono focus:border-theme-primary">{{ $allSettings['sms_template_payment_initiated'] ?? "Dear {name}, transaction of {currency}{amount} for booking #{booking_id} ({package}) has been initiated via {gateway}. Please enter the OTP sent by your bank to authorize the payment. - {site_name}" }}</textarea>
                                </div>

                                <!-- 2. Payment Success Custom SMS -->
                                <div class="space-y-1.5 p-4 rounded-xl bg-white/5 border border-white/5">
                                    <label class="text-xs font-bold text-white flex items-center justify-between">
                                        <span class="flex items-center gap-1.5 text-emerald-300">
                                            <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> 2. Payment Success SMS Template
                                        </span>
                                        <span class="text-[10px] text-zinc-400">Dispatched upon payment capture</span>
                                    </label>
                                    <p class="text-[11px] text-zinc-400">Sent immediately after successful Cashfree or Razorpay payment capture.</p>
                                    <textarea name="sms_template_payment_success" rows="2" class="w-full px-3 py-2 rounded-xl bg-black/40 border border-white/10 text-white text-xs font-mono focus:border-theme-primary">{{ $allSettings['sms_template_payment_success'] ?? "Dear {name}, payment of {currency}{amount} for booking #{booking_id} ({package}) was successful! Txn ID: {payment_id} via {gateway}. Thank you - {site_name}." }}</textarea>
                                </div>

                                <!-- 3. Payment Failed SMS -->
                                <div class="space-y-1.5 p-4 rounded-xl bg-white/5 border border-white/5">
                                    <label class="text-xs font-bold text-white flex items-center justify-between">
                                        <span class="flex items-center gap-1.5 text-rose-300">
                                            <i data-lucide="alert-triangle" class="w-3.5 h-3.5"></i> 3. Payment Failed SMS Template
                                        </span>
                                        <span class="text-[10px] text-zinc-400">Dispatched if transaction drops</span>
                                    </label>
                                    <textarea name="sms_template_payment_failed" rows="2" class="w-full px-3 py-2 rounded-xl bg-black/40 border border-white/10 text-white text-xs font-mono focus:border-theme-primary">{{ $allSettings['sms_template_payment_failed'] ?? "Dear {name}, your payment of {currency}{amount} for booking #{booking_id} could not be completed. Reason: {reason}. Please retry at: {retry_url}" }}</textarea>
                                </div>

                                <!-- 4. Client OTP Verification SMS -->
                                <div class="space-y-1.5 p-4 rounded-xl bg-white/5 border border-white/5">
                                    <label class="text-xs font-bold text-white flex items-center justify-between">
                                        <span class="flex items-center gap-1.5 text-theme-primary">
                                            <i data-lucide="key" class="w-3.5 h-3.5"></i> 4. Client Mobile Verification OTP SMS Template
                                        </span>
                                        <span class="text-[10px] text-zinc-400">Step 1 OTP verification</span>
                                    </label>
                                    <textarea name="sms_template_otp" rows="2" class="w-full px-3 py-2 rounded-xl bg-black/40 border border-white/10 text-white text-xs font-mono focus:border-theme-primary">{{ $allSettings['sms_template_otp'] ?? "Your {site_name} verification code is: {otp} for {package} booking of {currency}{amount}. Valid for 10 minutes. Please do not share this code." }}</textarea>
                                </div>

                                <!-- 5. Admin Notification SMS Alert Template -->
                                <div class="space-y-1.5 p-4 rounded-xl bg-white/5 border border-white/5">
                                    <label class="text-xs font-bold text-white flex items-center justify-between">
                                        <span class="flex items-center gap-1.5 text-amber-300">
                                            <i data-lucide="bell" class="w-3.5 h-3.5"></i> 5. Admin Booking Alert SMS Template
                                        </span>
                                        <span class="text-[10px] text-zinc-400">Dispatched to admin mobile</span>
                                    </label>
                                    <textarea name="sms_template_admin_alert" rows="2" class="w-full px-3 py-2 rounded-xl bg-black/40 border border-white/10 text-white text-xs font-mono focus:border-theme-primary">{{ $allSettings['sms_template_admin_alert'] ?? "[ALERT] New booking #{booking_id} confirmed by {name} for {package}. Amount: {currency}{amount} via {gateway}." }}</textarea>
                                </div>

                                <!-- 6. Admin Alert Phone Number -->
                                <div class="space-y-1">
                                    <label class="text-xs font-semibold text-zinc-300">Admin Notification Mobile Phone Number</label>
                                    <input type="text" name="sms_admin_phone" value="{{ $allSettings['sms_admin_phone'] ?? '' }}" placeholder="+91 98765 43210 (To receive SMS alerts on new confirmed bookings)" class="w-full px-3 py-2.5 rounded-lg bg-black/40 border border-white/10 text-white text-xs focus:border-theme-primary font-mono">
                                </div>
                            </div>

                            <button type="submit" class="w-full py-3.5 rounded-xl font-bold text-xs uppercase tracking-wider btn-gold-dynamic shadow-xl shadow-[var(--theme-primary)]/20 flex items-center justify-center gap-2 hover:scale-[1.01] transition">
                                <i data-lucide="save" class="w-4 h-4"></i>
                                <span>Save SMS Gateways & Message Templates</span>
                            </button>
                        </form>
                    </div>

                    <!-- Right 5 cols: Test SMS Dispatcher & Live Delivery Logs -->
                    <div class="lg:col-span-5 space-y-6">
                        <!-- Test SMS Tool Box -->
                        <div class="site-card rounded-2xl border border-amber-500/30 p-6 space-y-4 shadow-xl">
                            <h4 class="text-sm font-bold text-white flex items-center gap-2">
                                <i data-lucide="send" class="w-4 h-4 text-amber-400"></i>
                                <span>Send Live Test SMS</span>
                            </h4>
                            <p class="text-xs text-zinc-400">Dispatch a live test SMS to any mobile number using your preferred SMS pack.</p>

                            <form method="POST" action="{{ route('admin.sms.test') }}" class="space-y-3">
                                @csrf
                                <div class="space-y-1">
                                    <label class="text-[11px] text-zinc-300 font-semibold">Gateway / Pack to Test</label>
                                    <select name="test_driver" class="w-full px-3 py-2 rounded-lg bg-black/40 border border-white/10 text-white text-xs focus:border-theme-primary font-medium">
                                        <option value="fast2sms">★ Fast2SMS (Quick SMS Pack)</option>
                                        <option value="auto">Auto Multi-Gateway Failover</option>
                                        <option value="twilio">Twilio Gateway Direct</option>
                                        <option value="msg91">MSG91 Gateway Direct</option>
                                        <option value="custom_http">Custom HTTP Gateway</option>
                                        <option value="simulation">Simulation / Log Driver</option>
                                    </select>
                                </div>

                                <div class="space-y-1">
                                    <label class="text-[11px] text-zinc-300 font-semibold">Recipient Mobile Number</label>
                                    <input type="text" name="test_phone" required placeholder="9876543210 or +919876543210" class="w-full px-3 py-2 rounded-lg bg-black/40 border border-white/10 text-white text-xs focus:border-theme-primary font-mono">
                                </div>

                                <div class="space-y-1">
                                    <label class="text-[11px] text-zinc-300 font-semibold">Message Body</label>
                                    <textarea name="test_message" rows="2" required class="w-full px-3 py-2 rounded-lg bg-black/40 border border-white/10 text-white text-xs focus:border-theme-primary">Hello from Middukhera Production! This is a live test SMS dispatch from your SMS engine.</textarea>
                                </div>

                                <button type="submit" class="w-full py-2.5 rounded-xl font-bold text-xs bg-amber-500 hover:bg-amber-400 text-black shadow-md flex items-center justify-center gap-1.5 transition">
                                    <i data-lucide="send" class="w-3.5 h-3.5"></i>
                                    <span>Dispatch Test SMS</span>
                                </button>
                            </form>
                        </div>

                        <!-- Live SMS Delivery Logs -->
                        <div class="site-card rounded-2xl border border-white/10 p-6 space-y-4">
                            <div class="flex items-center justify-between border-b border-white/10 pb-3">
                                <span class="text-xs font-bold uppercase tracking-wider text-white flex items-center gap-1.5">
                                    <i data-lucide="history" class="w-4 h-4 text-theme-primary"></i>
                                    <span>Recent SMS Delivery Logs</span>
                                </span>
                                <span class="text-[10px] text-zinc-400 font-mono">{{ count($smsLogs) }} Logged</span>
                            </div>

                            @if($smsLogs->isEmpty())
                                <div class="py-8 text-center text-xs text-zinc-500">
                                    No SMS transmissions dispatched yet.
                                </div>
                            @else
                                <div class="space-y-2.5 max-h-96 overflow-y-auto pr-1 scrollbar-thin">
                                    @foreach($smsLogs as $log)
                                        <div class="p-3 rounded-xl bg-white/5 border border-white/5 space-y-1.5 text-xs">
                                            <div class="flex items-center justify-between">
                                                <span class="font-mono font-bold text-white">{{ $log->recipient }}</span>
                                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase {{ $log->status === 'sent' ? 'bg-emerald-500/20 text-emerald-300' : ($log->status === 'simulated' ? 'bg-cyan-500/20 text-cyan-300' : 'bg-rose-500/20 text-rose-300') }}">
                                                    {{ $log->status }}
                                                </span>
                                            </div>
                                            <p class="text-[11px] text-zinc-300 line-clamp-2">{{ $log->message }}</p>
                                            <div class="flex items-center justify-between text-[10px] text-zinc-500 pt-1">
                                                <span>Driver: <strong class="text-zinc-400">{{ $log->driver }}</strong></span>
                                                <span>{{ $log->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- TAB 15: THEME & LIVE COLORS -->
            <!-- ============================================== -->
            <div x-show="activeTab === 'theme_settings'" class="space-y-8">
                <!-- Preset Switcher -->
                <div class="site-card rounded-2xl border border-white/10 p-6 space-y-4">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <i data-lucide="sparkles" class="w-5 h-5 text-amber-400"></i>
                        <span>One-Click Luxury Color Presets</span>
                    </h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                        @foreach([
                            'luxury_gold' => ['name' => 'Luxury Gold', 'c1' => '#E5C158', 'c2' => '#8B5CF6'],
                            'obsidian_neon' => ['name' => 'Obsidian Neon', 'c1' => '#00F0FF', 'c2' => '#FF0055'],
                            'royal_emerald' => ['name' => 'Royal Emerald', 'c1' => '#10B981', 'c2' => '#F59E0B'],
                            'rose_champagne' => ['name' => 'Rose Champagne', 'c1' => '#F472B6', 'c2' => '#FBBF24'],
                            'cyberpunk_violet' => ['name' => 'Electric Violet', 'c1' => '#A855F7', 'c2' => '#EC4899'],
                            'minimal_light' => ['name' => 'Clean Light', 'c1' => '#18181B', 'c2' => '#6366F1'],
                        ] as $pkey => $preset)
                            <form method="POST" action="{{ route('admin.settings.preset') }}">
                                @csrf
                                <input type="hidden" name="preset" value="{{ $pkey }}">
                                <button type="submit" class="w-full p-3 rounded-xl border border-white/10 bg-black/40 hover:scale-105 transition text-left">
                                    <div class="flex items-center gap-1.5 mb-2">
                                        <span class="w-3.5 h-3.5 rounded-full" style="background-color: {{ $preset['c1'] }}"></span>
                                        <span class="w-3.5 h-3.5 rounded-full" style="background-color: {{ $preset['c2'] }}"></span>
                                    </div>
                                    <span class="text-xs font-bold text-white block">{{ $preset['name'] }}</span>
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>

                <!-- Custom Color Pickers -->
                <form method="POST" action="{{ route('admin.settings.save') }}" class="site-card rounded-2xl border border-white/10 p-6 md:p-8 space-y-6">
                    @csrf
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <i data-lucide="palette" class="w-5 h-5 text-pink-400"></i>
                        <span>Custom Hex Color Controls</span>
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach([
                            'bg_color' => ['label' => 'Global Background Color', 'val' => $siteSettings['bg_color'] ?? '#07060a'],
                            'primary_color' => ['label' => 'Primary Brand Color', 'val' => $siteSettings['primary_color'] ?? '#E5C158'],
                            'primary_hover' => ['label' => 'Primary Hover Tone', 'val' => $siteSettings['primary_hover'] ?? '#F3D88B'],
                            'card_bg_color' => ['label' => 'Card Surface Background', 'val' => $siteSettings['card_bg_color'] ?? '#12101b'],
                            'accent_color' => ['label' => 'Accent / Glow Tone', 'val' => $siteSettings['accent_color'] ?? '#8B5CF6'],
                            'text_color' => ['label' => 'Main Text Color', 'val' => $siteSettings['text_color'] ?? '#F3F4F6'],
                        ] as $ckey => $cdata)
                            <div class="space-y-2 p-4 rounded-xl bg-white/5 border border-white/5">
                                <label class="text-xs font-semibold text-zinc-300 block">{{ $cdata['label'] }}</label>
                                <div class="flex items-center gap-3">
                                    <input type="color" name="{{ $ckey }}" value="{{ $cdata['val'] }}" class="w-10 h-10 rounded-lg cursor-pointer bg-transparent border-0">
                                    <input type="text" name="{{ $ckey }}" value="{{ $cdata['val'] }}" class="flex-1 px-3 py-2 text-xs rounded-lg bg-black/40 border border-white/10 text-white font-mono uppercase">
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="px-6 py-3 rounded-xl btn-gold-dynamic text-xs font-bold uppercase tracking-wider flex items-center gap-2 shadow-xl shadow-[var(--theme-primary)]/20">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            <span>Save Live Theme Colors</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- ============================================== -->
            <!-- TAB 16: SITE & SEO CONFIGURATION -->
            <!-- ============================================== -->
            <div x-show="activeTab === 'site_settings'" class="space-y-8">
                <form method="POST" action="{{ route('admin.settings.save') }}" enctype="multipart/form-data" class="site-card rounded-2xl border border-white/10 p-6 md:p-8 space-y-8">
                    @csrf

                    <!-- Brand Identity with Direct File Uploads -->
                    <div class="space-y-4">
                        <h3 class="text-base font-bold text-white flex items-center gap-2 border-b border-white/10 pb-3">
                            <i data-lucide="globe" class="w-5 h-5 text-theme-primary"></i>
                            <span>Brand Identity & Direct Image Uploads</span>
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-zinc-300">Website Name</label>
                                <input type="text" name="site_name" value="{{ $siteSettings['site_name'] ?? 'Middukhera Production' }}" class="w-full px-3 py-2 text-xs rounded-xl bg-black/40 border border-white/10 text-white">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-zinc-300">Currency Symbol</label>
                                <input type="text" name="currency_symbol" value="{{ $siteSettings['currency_symbol'] ?? '₹' }}" class="w-full px-3 py-2 text-xs rounded-xl bg-black/40 border border-white/10 text-white font-mono">
                            </div>

                            <!-- Site Logo Upload -->
                            <div class="space-y-1.5 p-4 rounded-xl bg-white/5 border border-white/5">
                                <label class="text-xs font-semibold text-zinc-300">Site Logo Upload</label>
                                <input type="file" name="site_logo_file" accept="image/*" class="w-full text-xs text-zinc-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-white/10 file:text-white hover:file:bg-white/20">
                                <input type="text" name="site_logo" value="{{ $siteSettings['site_logo'] ?? '' }}" placeholder="Or paste Logo URL..." class="w-full mt-2 px-3 py-1.5 text-xs rounded-lg bg-black/40 border border-white/10 text-white">
                            </div>

                            <!-- Hero Background Image Upload -->
                            <div class="space-y-1.5 p-4 rounded-xl bg-white/5 border border-white/5">
                                <label class="text-xs font-semibold text-zinc-300">Hero Section Background Image</label>
                                <input type="file" name="hero_bg_image_file" accept="image/*" class="w-full text-xs text-zinc-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-white/10 file:text-white hover:file:bg-white/20">
                                <input type="text" name="hero_bg_image" value="{{ $siteSettings['hero_bg_image'] ?? '' }}" placeholder="Or paste Hero Image URL..." class="w-full mt-2 px-3 py-1.5 text-xs rounded-lg bg-black/40 border border-white/10 text-white">
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Hero Headlines -->
                    <div class="space-y-4">
                        <h3 class="text-base font-bold text-white flex items-center gap-2 border-b border-white/10 pb-3">
                            <i data-lucide="sparkles" class="w-5 h-5 text-amber-400"></i>
                            <span>Homepage Hero Section Content</span>
                        </h3>
                        <div class="space-y-4">
                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-zinc-300">Hero Badge Pill</label>
                                <input type="text" name="hero_badge" value="{{ $siteSettings['hero_badge'] ?? '✨ INDIA’S PREMIER LUXURY PRODUCTION HOUSE' }}" class="w-full px-3 py-2 text-xs rounded-xl bg-black/40 border border-white/10 text-white">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-zinc-300">Main Hero Title (H1)</label>
                                <input type="text" name="hero_title" value="{{ $siteSettings['hero_title'] ?? 'Transforming Ephemeral Moments Into Timeless High-Art Masterpieces' }}" class="w-full px-3 py-2 text-xs rounded-xl bg-black/40 border border-white/10 text-white">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-zinc-300">Hero Subtitle</label>
                                <textarea name="hero_subtitle" rows="2" class="w-full px-3 py-2 text-xs rounded-xl bg-black/40 border border-white/10 text-white leading-relaxed">{{ $siteSettings['hero_subtitle'] ?? 'Bespoke couture portraiture, celebrity fashion editorials, and cinematic wedding archives captured with world-class medium-format clarity.' }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Contact & Atelier Coordinates -->
                    <div class="space-y-4">
                        <h3 class="text-base font-bold text-white flex items-center gap-2 border-b border-white/10 pb-3">
                            <i data-lucide="map-pin" class="w-5 h-5 text-emerald-400"></i>
                            <span>Concierge & Studio Atelier Contact</span>
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-zinc-300">Concierge Phone Number</label>
                                <input type="text" name="contact_phone" value="{{ $siteSettings['contact_phone'] ?? '+91 98765 43210' }}" class="w-full px-3 py-2 text-xs rounded-xl bg-black/40 border border-white/10 text-white">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-zinc-300">Concierge Email</label>
                                <input type="email" name="contact_email" value="{{ $siteSettings['contact_email'] ?? 'contact@middukheraproduction.in' }}" class="w-full px-3 py-2 text-xs rounded-xl bg-black/40 border border-white/10 text-white">
                            </div>
                            <div class="space-y-1.5 md:col-span-2">
                                <label class="text-xs font-semibold text-zinc-300">Studio Address</label>
                                <input type="text" name="contact_address" value="{{ $siteSettings['contact_address'] ?? 'Middukhera Production Studio, Mumbai, India' }}" class="w-full px-3 py-2 text-xs rounded-xl bg-black/40 border border-white/10 text-white">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-zinc-300">Operating Hours</label>
                                <input type="text" name="operating_hours" value="{{ $siteSettings['operating_hours'] ?? 'Mon - Sun: 09:00 AM - 09:00 PM IST' }}" class="w-full px-3 py-2 text-xs rounded-xl bg-black/40 border border-white/10 text-white">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-zinc-300">WhatsApp Contact Number</label>
                                <input type="text" name="social_whatsapp" value="{{ $siteSettings['social_whatsapp'] ?? '919876543210' }}" class="w-full px-3 py-2 text-xs rounded-xl bg-black/40 border border-white/10 text-white font-mono">
                            </div>
                        </div>
                    </div>

                    <!-- SEO & Metadata -->
                    <div class="space-y-4">
                        <h3 class="text-base font-bold text-white flex items-center gap-2 border-b border-white/10 pb-3">
                            <i data-lucide="tag" class="w-5 h-5 text-indigo-400"></i>
                            <span>Global SEO & OpenGraph Meta</span>
                        </h3>
                        <div class="space-y-4">
                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-zinc-300">Global Meta Title</label>
                                <input type="text" name="meta_title" value="{{ $siteSettings['meta_title'] ?? '' }}" class="w-full px-3 py-2 text-xs rounded-xl bg-black/40 border border-white/10 text-white">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-zinc-300">Global Meta Description</label>
                                <textarea name="meta_description" rows="2" class="w-full px-3 py-2 text-xs rounded-xl bg-black/40 border border-white/10 text-white">{{ $siteSettings['meta_description'] ?? '' }}</textarea>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-zinc-300">Global Meta Keywords</label>
                                <input type="text" name="meta_keywords" value="{{ $siteSettings['meta_keywords'] ?? '' }}" class="w-full px-3 py-2 text-xs rounded-xl bg-black/40 border border-white/10 text-white">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="px-8 py-3.5 rounded-xl btn-gold-dynamic text-xs font-bold uppercase tracking-wider flex items-center gap-2 shadow-2xl shadow-[var(--theme-primary)]/25">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            <span>Save All Production Settings</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- ============================================== -->
            <!-- TAB 17: WEBHOOKS -->
            <!-- ============================================== -->
            <div x-show="activeTab === 'webhooks'" class="space-y-8">
                <div class="site-card rounded-2xl border border-white/10 p-6 space-y-6">
                    <div>
                        <h3 class="text-base font-bold text-white flex items-center gap-2">
                            <i data-lucide="webhook" class="w-5 h-5 text-indigo-400"></i>
                            <span>Inbound Webhook Payload Logs</span>
                        </h3>
                        <p class="text-xs text-zinc-400 mt-1">Audit trail of asynchronous webhook events received from Cashfree and Razorpay.</p>
                    </div>

                    @if($webhookLogs->isEmpty())
                        <div class="py-12 text-center text-zinc-500 text-xs">No webhook events logged yet.</div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs text-zinc-300">
                                <thead>
                                    <tr class="text-zinc-500 border-b border-white/10 pb-2">
                                        <th class="py-3 font-semibold">Gateway</th>
                                        <th class="py-3 font-semibold">Event Name</th>
                                        <th class="py-3 font-semibold">IP Address</th>
                                        <th class="py-3 font-semibold">Status</th>
                                        <th class="py-3 font-semibold">Timestamp</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/5">
                                    @foreach($webhookLogs as $wlog)
                                        <tr class="hover:bg-white/5 transition">
                                            <td class="py-3 font-bold uppercase text-theme-primary">{{ $wlog->gateway }}</td>
                                            <td class="py-3 font-mono text-[11px] text-white">{{ $wlog->event_name ?? 'PAYMENT_CAPTURE' }}</td>
                                            <td class="py-3 text-zinc-400">{{ $wlog->ip_address }}</td>
                                            <td class="py-3">
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $wlog->status === 'processed' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-rose-500/20 text-rose-300' }}">
                                                    {{ $wlog->status }}
                                                </span>
                                            </td>
                                            <td class="py-3 text-zinc-500">{{ $wlog->created_at->format('M d, Y h:i A') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- ============================================== -->
            <!-- TAB 18: PHOTOGRAPHERS / VENDORS -->
            <!-- ============================================== -->
            <div x-show="activeTab === 'vendors'" class="space-y-8">
                <div class="site-card rounded-2xl border border-white/10 p-6 space-y-6">
                    <div>
                        <h3 class="text-base font-bold text-white flex items-center gap-2">
                            <i data-lucide="camera" class="w-5 h-5 text-teal-400"></i>
                            <span>Photographer & Studio Partners</span>
                        </h3>
                        <p class="text-xs text-zinc-400 mt-1">Review vendor registration applications and grant dashboard catalog access.</p>
                    </div>

                    @if($vendors->isEmpty())
                        <div class="py-12 text-center text-zinc-500 text-xs">No vendor applications recorded.</div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs text-zinc-300">
                                <thead>
                                    <tr class="text-zinc-500 border-b border-white/10 pb-2">
                                        <th class="py-3 font-semibold">Studio / Vendor</th>
                                        <th class="py-3 font-semibold">Contact</th>
                                        <th class="py-3 font-semibold">City</th>
                                        <th class="py-3 font-semibold">Status</th>
                                        <th class="py-3 font-semibold text-right">Approval</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/5">
                                    @foreach($vendors as $v)
                                        <tr class="hover:bg-white/5 transition">
                                            <td class="py-3 font-bold text-white">{{ $v->business_name ?? $v->user->name }}</td>
                                            <td class="py-3 text-zinc-400">{{ $v->user->email ?? '' }}</td>
                                            <td class="py-3 text-zinc-400">{{ $v->city ?? 'India' }}</td>
                                            <td class="py-3">
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $v->status === 'approved' ? 'bg-emerald-500/20 text-emerald-300' : ($v->status === 'suspended' ? 'bg-rose-500/20 text-rose-300' : 'bg-amber-500/20 text-amber-300') }}">
                                                    {{ $v->status }}
                                                </span>
                                            </td>
                                            <td class="py-3 text-right">
                                                <form method="POST" action="{{ route('admin.vendor.updateStatus', $v->id) }}" class="inline">
                                                    @csrf
                                                    <select name="status" onchange="this.form.submit()" class="px-2 py-1 text-[10px] font-bold uppercase rounded-lg bg-white/5 border border-white/10 text-white cursor-pointer">
                                                        <option value="pending" {{ $v->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                        <option value="approved" {{ $v->status === 'approved' ? 'selected' : '' }}>Approved</option>
                                                        <option value="suspended" {{ $v->status === 'suspended' ? 'selected' : '' }}>Suspended</option>
                                                    </select>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

        </main>
    </div>

    <!-- ============================================== -->
    <!-- EDIT / CREATE MODALS (ALPINE.JS DRIVEN) -->
    <!-- ============================================== -->

    <!-- 1. PACKAGE MODAL -->
    <div x-show="editingPackage !== null" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" style="display: none;">
        <div class="site-card rounded-3xl border border-white/15 p-6 md:p-8 max-w-2xl w-full max-h-[90vh] overflow-y-auto space-y-6 shadow-2xl" @click.away="editingPackage = null">
            <div class="flex items-center justify-between border-b border-white/10 pb-4">
                <h3 class="text-lg font-bold text-white" x-text="editingPackage && editingPackage.id ? 'Edit Pricing Package' : 'Create Pricing Package'"></h3>
                <button type="button" @click="editingPackage = null" class="p-1 rounded-lg text-zinc-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <form method="POST" :action="editingPackage && editingPackage.id ? '{{ url('/admin/package') }}/' + editingPackage.id : '{{ route('admin.package.store') }}'" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <template x-if="editingPackage && editingPackage.id">
                    <input type="hidden" name="_method" value="PATCH">
                </template>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-zinc-300">Package Name</label>
                    <input type="text" name="name" x-model="editingPackage.name" required class="w-full px-3 py-2 text-xs rounded-xl bg-black/40 border border-white/10 text-white">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-zinc-300">Price Min (INR)</label>
                        <input type="number" name="price_min" x-model="editingPackage.price_min" required class="w-full px-3 py-2 text-xs rounded-xl bg-black/40 border border-white/10 text-white">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-zinc-300">Price Max (INR)</label>
                        <input type="number" name="price_max" x-model="editingPackage.price_max" required class="w-full px-3 py-2 text-xs rounded-xl bg-black/40 border border-white/10 text-white">
                    </div>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-zinc-300">Description</label>
                    <textarea name="description" x-model="editingPackage.description" rows="3" required class="w-full px-3 py-2 text-xs rounded-xl bg-black/40 border border-white/10 text-white"></textarea>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-zinc-300">Deliverables / Features (1 per line or comma separated)</label>
                    <textarea name="features" x-model="editingPackage.features" rows="4" required class="w-full px-3 py-2 text-xs rounded-xl bg-black/40 border border-white/10 text-white"></textarea>
                </div>
                <div class="space-y-1 p-3 rounded-xl bg-white/5 border border-white/5">
                    <label class="text-xs font-semibold text-zinc-300">Package Image</label>
                    <input type="file" name="image" accept="image/*" class="w-full text-xs text-zinc-400 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-white/10 file:text-white">
                    <input type="text" name="image_url" x-model="editingPackage.image_path" placeholder="Or paste Image URL..." class="w-full mt-2 px-3 py-1.5 text-xs rounded-lg bg-black/40 border border-white/10 text-white">
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t border-white/10">
                    <button type="button" @click="editingPackage = null" class="px-4 py-2 rounded-xl bg-white/5 text-zinc-300 text-xs font-semibold">Cancel</button>
                    <button type="submit" class="px-6 py-2 rounded-xl btn-gold-dynamic text-xs font-bold uppercase tracking-wider">Save Package</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 2. GALLERY MODAL -->
    <div x-show="editingGallery !== null" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" style="display: none;">
        <div class="site-card rounded-3xl border border-white/15 p-6 md:p-8 max-w-lg w-full space-y-6 shadow-2xl" @click.away="editingGallery = null">
            <div class="flex items-center justify-between border-b border-white/10 pb-4">
                <h3 class="text-lg font-bold text-white" x-text="editingGallery && editingGallery.id ? 'Edit Portfolio Photo' : 'Upload Portfolio Photo'"></h3>
                <button type="button" @click="editingGallery = null" class="p-1 rounded-lg text-zinc-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <form method="POST" :action="editingGallery && editingGallery.id ? '{{ url('/admin/gallery') }}/' + editingGallery.id : '{{ route('admin.gallery.store') }}'" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <template x-if="editingGallery && editingGallery.id">
                    <input type="hidden" name="_method" value="PATCH">
                </template>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-zinc-300">Photo Title</label>
                    <input type="text" name="title" x-model="editingGallery.title" required class="w-full px-3 py-2 text-xs rounded-xl bg-black/40 border border-white/10 text-white">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-zinc-300">Category</label>
                    <select name="category" x-model="editingGallery.category" required class="w-full px-3 py-2 text-xs rounded-xl bg-black/40 border border-white/10 text-white">
                        <option value="Wedding">Wedding</option>
                        <option value="Fashion">Fashion</option>
                        <option value="Editorial">Editorial</option>
                        <option value="Portrait">Portrait</option>
                        <option value="Event">Event</option>
                    </select>
                </div>
                <div class="space-y-1 p-3 rounded-xl bg-white/5 border border-white/5">
                    <label class="text-xs font-semibold text-zinc-300">Photo Image File</label>
                    <input type="file" name="image" accept="image/*" class="w-full text-xs text-zinc-400 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-white/10 file:text-white">
                    <input type="text" name="image_url" x-model="editingGallery.image_path" placeholder="Or paste Image URL..." class="w-full mt-2 px-3 py-1.5 text-xs rounded-lg bg-black/40 border border-white/10 text-white">
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t border-white/10">
                    <button type="button" @click="editingGallery = null" class="px-4 py-2 rounded-xl bg-white/5 text-zinc-300 text-xs font-semibold">Cancel</button>
                    <button type="submit" class="px-6 py-2 rounded-xl btn-gold-dynamic text-xs font-bold uppercase tracking-wider">Save Photo</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 3. BLOG MODAL -->
    <div x-show="editingBlog !== null" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" style="display: none;">
        <div class="site-card rounded-3xl border border-white/15 p-6 md:p-8 max-w-3xl w-full max-h-[90vh] overflow-y-auto space-y-6 shadow-2xl" @click.away="editingBlog = null">
            <div class="flex items-center justify-between border-b border-white/10 pb-4">
                <h3 class="text-lg font-bold text-white" x-text="editingBlog && editingBlog.id ? 'Edit Journal Article' : 'Write Journal Article'"></h3>
                <button type="button" @click="editingBlog = null" class="p-1 rounded-lg text-zinc-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <form method="POST" :action="editingBlog && editingBlog.id ? '{{ url('/admin/blog') }}/' + editingBlog.id : '{{ route('admin.blog.store') }}'" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <template x-if="editingBlog && editingBlog.id">
                    <input type="hidden" name="_method" value="PATCH">
                </template>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-zinc-300">Article Title</label>
                    <input type="text" name="title" x-model="editingBlog.title" required class="w-full px-3 py-2 text-xs rounded-xl bg-black/40 border border-white/10 text-white">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-zinc-300">Short Excerpt</label>
                    <textarea name="excerpt" x-model="editingBlog.excerpt" rows="2" required class="w-full px-3 py-2 text-xs rounded-xl bg-black/40 border border-white/10 text-white"></textarea>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-zinc-300">Full Article Content (HTML allowed)</label>
                    <textarea name="content" x-model="editingBlog.content" rows="8" required class="w-full px-3 py-2 text-xs rounded-xl bg-black/40 border border-white/10 text-white font-mono"></textarea>
                </div>
                <div class="space-y-1 p-3 rounded-xl bg-white/5 border border-white/5">
                    <label class="text-xs font-semibold text-zinc-300">Featured Thumbnail Image</label>
                    <input type="file" name="image" accept="image/*" class="w-full text-xs text-zinc-400 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-white/10 file:text-white">
                    <input type="text" name="image_url" x-model="editingBlog.image_path" placeholder="Or paste Image URL..." class="w-full mt-2 px-3 py-1.5 text-xs rounded-lg bg-black/40 border border-white/10 text-white">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-zinc-300">SEO Meta Title</label>
                        <input type="text" name="meta_title" x-model="editingBlog.meta_title" class="w-full px-3 py-2 text-xs rounded-xl bg-black/40 border border-white/10 text-white">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-zinc-300">SEO Keywords</label>
                        <input type="text" name="meta_keywords" x-model="editingBlog.meta_keywords" class="w-full px-3 py-2 text-xs rounded-xl bg-black/40 border border-white/10 text-white">
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t border-white/10">
                    <button type="button" @click="editingBlog = null" class="px-4 py-2 rounded-xl bg-white/5 text-zinc-300 text-xs font-semibold">Cancel</button>
                    <button type="submit" class="px-6 py-2 rounded-xl btn-gold-dynamic text-xs font-bold uppercase tracking-wider">Save Article</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 4. PAGE & POLICY MODAL -->
    <div x-show="editingPage !== null" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" style="display: none;">
        <div class="site-card rounded-3xl border border-white/15 p-6 md:p-8 max-w-3xl w-full max-h-[90vh] overflow-y-auto space-y-6 shadow-2xl" @click.away="editingPage = null">
            <div class="flex items-center justify-between border-b border-white/10 pb-4">
                <h3 class="text-lg font-bold text-white" x-text="editingPage && editingPage.id ? 'Edit Page Content' : 'Create New Page'"></h3>
                <button type="button" @click="editingPage = null" class="p-1 rounded-lg text-zinc-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <form method="POST" :action="editingPage && editingPage.id ? '{{ url('/admin/pages') }}/' + editingPage.id : '{{ route('admin.pages.store') }}'" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <template x-if="editingPage && editingPage.id">
                    <input type="hidden" name="_method" value="PATCH">
                </template>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-zinc-300">Page Title</label>
                        <input type="text" name="title" x-model="editingPage.title" required class="w-full px-3 py-2 text-xs rounded-xl bg-black/40 border border-white/10 text-white">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-zinc-300">URL Slug</label>
                        <input type="text" name="slug" x-model="editingPage.slug" required class="w-full px-3 py-2 text-xs rounded-xl bg-black/40 border border-white/10 text-white font-mono">
                    </div>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-zinc-300">Subtitle / Tagline</label>
                    <input type="text" name="subtitle" x-model="editingPage.subtitle" class="w-full px-3 py-2 text-xs rounded-xl bg-black/40 border border-white/10 text-white">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-zinc-300">Body Content (Formatted HTML)</label>
                    <textarea name="content" x-model="editingPage.content" rows="10" class="w-full px-3 py-2 text-xs rounded-xl bg-black/40 border border-white/10 text-white font-mono leading-relaxed"></textarea>
                </div>
                <div class="space-y-1 p-3 rounded-xl bg-white/5 border border-white/5">
                    <label class="text-xs font-semibold text-zinc-300">Banner Image</label>
                    <input type="file" name="banner_image" accept="image/*" class="w-full text-xs text-zinc-400 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-white/10 file:text-white">
                    <input type="text" name="banner_image_url" x-model="editingPage.banner_image" placeholder="Or paste Banner URL..." class="w-full mt-2 px-3 py-1.5 text-xs rounded-lg bg-black/40 border border-white/10 text-white">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-zinc-300">SEO Meta Title</label>
                        <input type="text" name="meta_title" x-model="editingPage.meta_title" class="w-full px-3 py-2 text-xs rounded-xl bg-black/40 border border-white/10 text-white">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-zinc-300">SEO Keywords</label>
                        <input type="text" name="meta_keywords" x-model="editingPage.meta_keywords" class="w-full px-3 py-2 text-xs rounded-xl bg-black/40 border border-white/10 text-white">
                    </div>
                </div>
                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" name="is_published" value="1" :checked="editingPage.is_published" id="is_published_chk" class="rounded bg-black/40 border-white/20 text-theme-primary">
                    <label for="is_published_chk" class="text-xs font-semibold text-white">Publish Page Live</label>
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t border-white/10">
                    <button type="button" @click="editingPage = null" class="px-4 py-2 rounded-xl bg-white/5 text-zinc-300 text-xs font-semibold">Cancel</button>
                    <button type="submit" class="px-6 py-2 rounded-xl btn-gold-dynamic text-xs font-bold uppercase tracking-wider">Save Page</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 5. TESTIMONIAL MODAL -->
    <div x-show="editingTestimonial !== null" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" style="display: none;">
        <div class="site-card rounded-3xl border border-white/15 p-6 md:p-8 max-w-lg w-full space-y-6 shadow-2xl" @click.away="editingTestimonial = null">
            <div class="flex items-center justify-between border-b border-white/10 pb-4">
                <h3 class="text-lg font-bold text-white" x-text="editingTestimonial && editingTestimonial.id ? 'Edit Client Review' : 'Add Client Review'"></h3>
                <button type="button" @click="editingTestimonial = null" class="p-1 rounded-lg text-zinc-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <form method="POST" :action="editingTestimonial && editingTestimonial.id ? '{{ url('/admin/testimonials') }}/' + editingTestimonial.id : '{{ route('admin.testimonials.store') }}'" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <template x-if="editingTestimonial && editingTestimonial.id">
                    <input type="hidden" name="_method" value="PATCH">
                </template>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-zinc-300">Client Name</label>
                    <input type="text" name="client_name" x-model="editingTestimonial.client_name" required class="w-full px-3 py-2 text-xs rounded-xl bg-black/40 border border-white/10 text-white">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-zinc-300">Client Role / Title</label>
                        <input type="text" name="client_role" x-model="editingTestimonial.client_role" placeholder="e.g. Bride & Groom" class="w-full px-3 py-2 text-xs rounded-xl bg-black/40 border border-white/10 text-white">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-zinc-300">Star Rating (1 - 5)</label>
                        <select name="rating" x-model="editingTestimonial.rating" class="w-full px-3 py-2 text-xs rounded-xl bg-black/40 border border-white/10 text-white">
                            <option value="5">⭐⭐⭐⭐⭐ (5 Stars)</option>
                            <option value="4">⭐⭐⭐⭐ (4 Stars)</option>
                            <option value="3">⭐⭐⭐ (3 Stars)</option>
                        </select>
                    </div>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-zinc-300">Review Quote</label>
                    <textarea name="content" x-model="editingTestimonial.content" rows="4" required class="w-full px-3 py-2 text-xs rounded-xl bg-black/40 border border-white/10 text-white"></textarea>
                </div>
                <div class="space-y-1 p-3 rounded-xl bg-white/5 border border-white/5">
                    <label class="text-xs font-semibold text-zinc-300">Client Avatar / Photo</label>
                    <input type="file" name="avatar" accept="image/*" class="w-full text-xs text-zinc-400 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-white/10 file:text-white">
                    <input type="text" name="avatar_url" x-model="editingTestimonial.avatar_path" placeholder="Or paste Avatar URL..." class="w-full mt-2 px-3 py-1.5 text-xs rounded-lg bg-black/40 border border-white/10 text-white">
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t border-white/10">
                    <button type="button" @click="editingTestimonial = null" class="px-4 py-2 rounded-xl bg-white/5 text-zinc-300 text-xs font-semibold">Cancel</button>
                    <button type="submit" class="px-6 py-2 rounded-xl btn-gold-dynamic text-xs font-bold uppercase tracking-wider">Save Review</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 6. FAQ MODAL -->
    <div x-show="editingFaq !== null" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" style="display: none;">
        <div class="site-card rounded-3xl border border-white/15 p-6 md:p-8 max-w-lg w-full space-y-6 shadow-2xl" @click.away="editingFaq = null">
            <div class="flex items-center justify-between border-b border-white/10 pb-4">
                <h3 class="text-lg font-bold text-white" x-text="editingFaq && editingFaq.id ? 'Edit FAQ' : 'Add FAQ'"></h3>
                <button type="button" @click="editingFaq = null" class="p-1 rounded-lg text-zinc-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <form method="POST" :action="editingFaq && editingFaq.id ? '{{ url('/admin/faqs') }}/' + editingFaq.id : '{{ route('admin.faqs.store') }}'" class="space-y-4">
                @csrf
                <template x-if="editingFaq && editingFaq.id">
                    <input type="hidden" name="_method" value="PATCH">
                </template>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-zinc-300">Question</label>
                    <input type="text" name="question" x-model="editingFaq.question" required class="w-full px-3 py-2 text-xs rounded-xl bg-black/40 border border-white/10 text-white">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-zinc-300">Category</label>
                    <select name="category" x-model="editingFaq.category" class="w-full px-3 py-2 text-xs rounded-xl bg-black/40 border border-white/10 text-white">
                        <option value="general">General</option>
                        <option value="booking">Booking & Sessions</option>
                        <option value="pricing">Pricing & Payments</option>
                        <option value="delivery">Delivery & Fulfillment</option>
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-zinc-300">Answer</label>
                    <textarea name="answer" x-model="editingFaq.answer" rows="4" required class="w-full px-3 py-2 text-xs rounded-xl bg-black/40 border border-white/10 text-white"></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t border-white/10">
                    <button type="button" @click="editingFaq = null" class="px-4 py-2 rounded-xl bg-white/5 text-zinc-300 text-xs font-semibold">Cancel</button>
                    <button type="submit" class="px-6 py-2 rounded-xl btn-gold-dynamic text-xs font-bold uppercase tracking-wider">Save FAQ</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 7. TEAM MEMBER MODAL -->
    <div x-show="editingTeamMember !== null" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" style="display: none;">
        <div class="site-card rounded-3xl border border-white/15 p-6 md:p-8 max-w-lg w-full space-y-6 shadow-2xl" @click.away="editingTeamMember = null">
            <div class="flex items-center justify-between border-b border-white/10 pb-4">
                <h3 class="text-lg font-bold text-white" x-text="editingTeamMember && editingTeamMember.id ? 'Edit Team Member' : 'Add Team Member'"></h3>
                <button type="button" @click="editingTeamMember = null" class="p-1 rounded-lg text-zinc-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <form method="POST" :action="editingTeamMember && editingTeamMember.id ? '{{ url('/admin/team') }}/' + editingTeamMember.id : '{{ route('admin.team.store') }}'" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <template x-if="editingTeamMember && editingTeamMember.id">
                    <input type="hidden" name="_method" value="PATCH">
                </template>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-zinc-300">Member Name</label>
                    <input type="text" name="name" x-model="editingTeamMember.name" required class="w-full px-3 py-2 text-xs rounded-xl bg-black/40 border border-white/10 text-white">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-zinc-300">Designation / Role</label>
                    <input type="text" name="role" x-model="editingTeamMember.role" required placeholder="e.g. Head Cinematographer" class="w-full px-3 py-2 text-xs rounded-xl bg-black/40 border border-white/10 text-white">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-zinc-300">Short Bio</label>
                    <textarea name="bio" x-model="editingTeamMember.bio" rows="3" class="w-full px-3 py-2 text-xs rounded-xl bg-black/40 border border-white/10 text-white"></textarea>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-zinc-300">Instagram Profile URL</label>
                    <input type="url" name="instagram_url" x-model="editingTeamMember.instagram_url" placeholder="https://instagram.com/..." class="w-full px-3 py-2 text-xs rounded-xl bg-black/40 border border-white/10 text-white">
                </div>
                <div class="space-y-1 p-3 rounded-xl bg-white/5 border border-white/5">
                    <label class="text-xs font-semibold text-zinc-300">Profile Photo</label>
                    <input type="file" name="image" accept="image/*" class="w-full text-xs text-zinc-400 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-white/10 file:text-white">
                    <input type="text" name="image_url" x-model="editingTeamMember.image_path" placeholder="Or paste Photo URL..." class="w-full mt-2 px-3 py-1.5 text-xs rounded-lg bg-black/40 border border-white/10 text-white">
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t border-white/10">
                    <button type="button" @click="editingTeamMember = null" class="px-4 py-2 rounded-xl bg-white/5 text-zinc-300 text-xs font-semibold">Cancel</button>
                    <button type="submit" class="px-6 py-2 rounded-xl btn-gold-dynamic text-xs font-bold uppercase tracking-wider">Save Member</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 8. MEDIA FULL-RES PREVIEW MODAL -->
    <div x-show="previewMediaUrl !== null" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/90 backdrop-blur-md" style="display: none;" @keydown.escape.window="previewMediaUrl = null">
        <div class="max-w-4xl w-full flex flex-col space-y-4" @click.away="previewMediaUrl = null">
            <div class="flex items-center justify-between px-2">
                <span class="text-xs font-bold text-white uppercase tracking-wider truncate" x-text="previewMediaName"></span>
                <button type="button" @click="previewMediaUrl = null" class="p-2 rounded-full bg-white/10 hover:bg-white/20 text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <div class="aspect-auto max-h-[75vh] w-full rounded-2xl overflow-hidden border border-white/15 bg-black flex items-center justify-center">
                <img :src="previewMediaUrl" class="max-h-[75vh] max-w-full object-contain">
            </div>
            <div class="flex items-center justify-between px-2">
                <button type="button" @click="copyToClipboard(previewMediaUrl)" class="px-4 py-2 rounded-xl btn-gold-dynamic text-xs font-bold uppercase flex items-center gap-1.5">
                    <i data-lucide="copy" class="w-4 h-4"></i>
                    <span>Copy File URL</span>
                </button>
                <a :href="previewMediaUrl" target="_blank" download class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-semibold flex items-center gap-1.5">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    <span>Download</span>
                </a>
            </div>
        </div>
    </div>

</div>
@endsection
