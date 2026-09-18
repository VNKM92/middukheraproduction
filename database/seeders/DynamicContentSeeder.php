<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\Page;
use App\Models\TeamMember;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class DynamicContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. DYNAMIC PAGES
        $pages = [
            [
                'title' => 'Behind The Studio Lens',
                'slug' => 'about',
                'subtitle' => 'Our Chronicles & Creative Philosophy',
                'content' => '<p class="text-lg text-gray-300 font-light leading-relaxed">We are visual preservationists, dedicated to capturing couture designs, raw human expressions, and architectural masterpieces with unparalleled fidelity.</p><p class="mt-4 text-gray-400 font-light leading-relaxed">Founded in 2018, our studio emerged from a singular conviction: photography is not simply the recording of a scene; it is the curation of light and emotion to construct a legacy. Every shoot begins with detailed moodboards, artistic concept briefs, and bespoke lighting coordinates.</p>',
                'banner_image' => 'https://images.unsplash.com/photo-1542038784456-1ea8e935640e?q=80&w=1200&auto=format&fit=crop',
                'sections' => [
                    'manifesto_title' => 'Our Creative Manifesto',
                    'manifesto_desc' => 'Founded in 2018, our studio emerged from a singular conviction: photography is not simply the recording of a scene; it is the curation of light and emotion to construct a legacy.',
                    'pillars' => [
                        [
                            'icon' => 'eye',
                            'title' => 'Avante-Garde Vision',
                            'desc' => 'We reject the generic. Every shoot begins with detailed moodboards, artistic concept briefs, and bespoke lighting coordinates.'
                        ],
                        [
                            'icon' => 'award',
                            'title' => 'Impeccable Precision',
                            'desc' => 'Utilizing premium medium-format digital sensors, master-class lighting umbrellas, and expert retouchers to ensure flawless physical prints.'
                        ],
                        [
                            'icon' => 'clock',
                            'title' => 'Timeless Preservation',
                            'desc' => 'We frame archives. Our signature albums are bound in handmade full-grain Italian leather designed to withstand generations.'
                        ]
                    ],
                    'milestones' => [
                        [
                            'year' => '2018',
                            'badge' => '2018 • The Spark',
                            'title' => 'Inception of Luxury Studio',
                            'desc' => 'Opened our boutique physical studio focusing purely on high-contrast portraiture and fine art black-and-white printings.'
                        ],
                        [
                            'year' => '2020',
                            'badge' => '2020 • Going Editorial',
                            'title' => 'First National Fashion Feature',
                            'desc' => 'Commissioned to shoot the summer collection of two premium national design houses, getting featured in mainstream design journals.'
                        ],
                        [
                            'year' => '2023',
                            'badge' => '2023 • Elite Standard',
                            'title' => 'International Expansion & Tech Upgrades',
                            'desc' => 'Upgraded our main systems to Hasselblad medium format equipment and expanded services to cover luxury destination weddings globally.'
                        ]
                    ]
                ],
                'meta_title' => 'About Us | Premier Luxury Photoshoot & Production Studio',
                'meta_description' => 'Discover the chronicles, creative philosophy, and vision behind our luxury photoshoot and media production studio.',
                'meta_keywords' => 'about studio, luxury photography, photoshoot masters, creative team',
                'is_published' => true,
            ],
            [
                'title' => 'Terms and Conditions',
                'slug' => 'terms',
                'subtitle' => 'Legal & Compliance Agreement',
                'content' => '<section class="space-y-4">
                    <h2 class="text-base font-bold text-white flex items-center gap-2"><span class="text-theme-primary font-mono">01.</span><span>Agreement to Terms</span></h2>
                    <p>These Terms and Conditions constitute a legally binding agreement between you (Client) and our Studio regarding access to our website and our photography, cinematography, and production services.</p>
                </section>
                <section class="space-y-4 mt-6">
                    <h2 class="text-base font-bold text-white flex items-center gap-2"><span class="text-theme-primary font-mono">02.</span><span>Booking, Deposits & Payments</span></h2>
                    <p>All photoshoot reservations require payment of a deposit or full package amount at the time of booking. Payments are securely processed via certified online payment gateways (Cashfree & Razorpay).</p>
                    <ul class="list-disc list-inside space-y-1.5 pl-2 text-zinc-400">
                        <li>Prices are displayed and charged in Indian National Rupees (INR / ₹).</li>
                        <li>Your booking is confirmed immediately upon successful capture of the transaction.</li>
                        <li>An automated electronic receipt and booking reference ID will be issued upon transaction completion.</li>
                    </ul>
                </section>
                <section class="space-y-4 mt-6">
                    <h2 class="text-base font-bold text-white flex items-center gap-2"><span class="text-theme-primary font-mono">03.</span><span>Cancellation, Rescheduling & Refunds</span></h2>
                    <p>Cancellations and rescheduling are governed by our official Cancellation & Refund Policy. Rescheduling requests made at least 5 days in advance are accommodated at zero extra charge subject to availability.</p>
                </section>
                <section class="space-y-4 mt-6">
                    <h2 class="text-base font-bold text-white flex items-center gap-2"><span class="text-theme-primary font-mono">04.</span><span>Deliverables & Fulfillment Timelines</span></h2>
                    <p>Digital proofing sheets are delivered within 48 to 72 hours post-session. Final color-graded high-resolution digital galleries are delivered within 7 to 14 business days.</p>
                </section>',
                'meta_title' => 'Terms & Conditions | Studio Booking Agreement',
                'meta_description' => 'Read our terms and conditions regarding photoshoot reservations, payments, deliverables, and service policies.',
                'meta_keywords' => 'terms, conditions, booking policy, legal',
                'is_published' => true,
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy',
                'subtitle' => 'Data Protection & Security Standard',
                'content' => '<section class="space-y-4">
                    <h2 class="text-base font-bold text-white flex items-center gap-2"><span class="text-theme-primary font-mono">01.</span><span>Overview & Commitment</span></h2>
                    <p>We are committed to protecting and respecting your personal privacy. This Privacy Policy outlines the types of information we collect, how it is used, stored, and protected when you visit our website or book our photography and media production services.</p>
                </section>
                <section class="space-y-4 mt-6">
                    <h2 class="text-base font-bold text-white flex items-center gap-2"><span class="text-theme-primary font-mono">02.</span><span>Information We Collect</span></h2>
                    <p>When you interact with our website or reserve a studio session, we may collect Contact Information (name, email, phone, address), Booking Details (dates, preferences), Transaction Info (Order IDs), and Technical Data (IP address, browser logs).</p>
                </section>
                <section class="space-y-4 mt-6">
                    <h2 class="text-base font-bold text-white flex items-center gap-2"><span class="text-theme-primary font-mono">03.</span><span>Payment Processing & Security</span></h2>
                    <p>We do not store, process, or retain sensitive card numbers, CVVs, or banking PINs on our servers. All financial transactions are securely processed through PCI-DSS Level 1 compliant gateways with 256-bit SSL encryption.</p>
                </section>',
                'meta_title' => 'Privacy Policy | Data Protection',
                'meta_description' => 'Our privacy policy details how your personal data and payment security are safeguarded.',
                'meta_keywords' => 'privacy policy, data protection, security',
                'is_published' => true,
            ],
            [
                'title' => 'Cancellation & Refund Policy',
                'slug' => 'refund-policy',
                'subtitle' => 'Transparent Cancellation Terms',
                'content' => '<section class="space-y-4">
                    <h2 class="text-base font-bold text-white flex items-center gap-2"><span class="text-theme-primary font-mono">01.</span><span>Overview</span></h2>
                    <p>We maintain a transparent and balanced cancellation and refund policy designed for fair scheduling and seamless booking operations.</p>
                </section>
                <section class="space-y-4 mt-6">
                    <h2 class="text-base font-bold text-white flex items-center gap-2"><span class="text-theme-primary font-mono">02.</span><span>Cancellation Windows</span></h2>
                    <ul class="list-disc list-inside space-y-2 pl-2 text-zinc-400">
                        <li><strong class="text-white">Cancellation 7 or more days prior:</strong> 100% full refund of the amount paid.</li>
                        <li><strong class="text-white">Cancellation 3 to 6 days prior:</strong> 80% refund (20% slot reservation charge).</li>
                        <li><strong class="text-white">Cancellation within 48 hours:</strong> 50% retainer fee retained for studio prep; 50% refunded.</li>
                    </ul>
                </section>
                <section class="space-y-4 mt-6">
                    <h2 class="text-base font-bold text-white flex items-center gap-2"><span class="text-theme-primary font-mono">03.</span><span>Refund Processing Timeline</span></h2>
                    <p>All approved refunds are credited back to the original payment source (bank account, credit/debit card, UPI) within <strong>5 to 7 working days</strong>.</p>
                </section>',
                'meta_title' => 'Cancellation & Refund Policy',
                'meta_description' => 'Clear guidelines on session cancellations, free rescheduling, and refund timelines.',
                'meta_keywords' => 'refund policy, cancellation, rescheduling',
                'is_published' => true,
            ],
            [
                'title' => 'Shipping & Delivery Policy',
                'slug' => 'shipping-policy',
                'subtitle' => 'Digital Assets & Physical Album Fulfillment',
                'content' => '<section class="space-y-4">
                    <h2 class="text-base font-bold text-white flex items-center gap-2"><span class="text-theme-primary font-mono">01.</span><span>Overview</span></h2>
                    <p>Our photography and production services include digital deliverables (raw proofs, color-graded high-resolution photos, cinematic 4K video reels) and premium physical deliverables (handcrafted leather albums, acrylic prints, wooden keepsake USB boxes).</p>
                </section>
                <section class="space-y-4 mt-6">
                    <h2 class="text-base font-bold text-white flex items-center gap-2"><span class="text-theme-primary font-mono">02.</span><span>Digital Delivery Timelines</span></h2>
                    <ul class="list-disc list-inside space-y-2 pl-2 text-zinc-400">
                        <li><strong class="text-white">Raw Proofing Gallery:</strong> Delivered within 48 to 72 hours via private cloud link.</li>
                        <li><strong class="text-white">Final High-Res Retouched Gallery:</strong> Delivered within 7 to 14 business days post-selection.</li>
                    </ul>
                </section>
                <section class="space-y-4 mt-6">
                    <h2 class="text-base font-bold text-white flex items-center gap-2"><span class="text-theme-primary font-mono">03.</span><span>Physical Album Shipping</span></h2>
                    <p>Handcrafted Italian leather albums and prints are dispatched within 15 to 21 working days after final layout approval, shipped via premium insured couriers with door-to-door tracking.</p>
                </section>',
                'meta_title' => 'Shipping & Delivery Policy',
                'meta_description' => 'Delivery turnaround times for digital proofing links and insured physical album shipping.',
                'meta_keywords' => 'shipping policy, delivery times, photo album dispatch',
                'is_published' => true,
            ],
            [
                'title' => 'Disclaimer',
                'slug' => 'disclaimer',
                'subtitle' => 'General Information & Usage Disclaimer',
                'content' => '<section class="space-y-4">
                    <h2 class="text-base font-bold text-white flex items-center gap-2"><span class="text-theme-primary font-mono">01.</span><span>General Information</span></h2>
                    <p>The information provided on this website is for general informational and booking purposes only. While we make every effort to ensure accurate pricing and portfolio representations, studio bookings are subject to schedule confirmation and formal contract agreement.</p>
                </section>
                <section class="space-y-4 mt-6">
                    <h2 class="text-base font-bold text-white flex items-center gap-2"><span class="text-theme-primary font-mono">02.</span><span>Intellectual Property & Portfolio</span></h2>
                    <p>All imagery, cinematic film excerpts, and brand assets displayed on this website are protected under copyright law and remain the intellectual property of the studio and respective featured clients.</p>
                </section>',
                'meta_title' => 'Disclaimer | Studio Notice',
                'meta_description' => 'Important disclaimer regarding portfolio assets, copyright, and general service notices.',
                'meta_keywords' => 'disclaimer, legal notice, copyright',
                'is_published' => true,
            ],
        ];

        foreach ($pages as $p) {
            Page::updateOrCreate(['slug' => $p['slug']], $p);
        }

        // 2. TESTIMONIALS
        if (Testimonial::count() === 0) {
            $testimonials = [
                [
                    'client_name' => 'Aditi & Kabir Sharma',
                    'client_role' => 'Bride & Groom',
                    'avatar_path' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=300',
                    'rating' => 5,
                    'content' => 'The cinematic vision and attention to detail during our 3-day royal palace wedding in Udaipur exceeded every expectation. Every frame looks straight out of an international luxury magazine.',
                    'event_type' => 'Royal Destination Wedding',
                    'is_featured' => true,
                    'order' => 1,
                ],
                [
                    'client_name' => 'Meera Rathore',
                    'client_role' => 'Fashion Designer & Brand Director',
                    'avatar_path' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=300',
                    'rating' => 5,
                    'content' => 'Shooting our Spring/Summer couture campaign with this studio was effortless. Their medium-format cameras and master-class lighting setup brought our handcrafted silk textures to life.',
                    'event_type' => 'Couture Editorial Campaign',
                    'is_featured' => true,
                    'order' => 2,
                ],
                [
                    'client_name' => 'Rohan & Tanya Singhania',
                    'client_role' => 'Celebrity Couple',
                    'avatar_path' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=300',
                    'rating' => 5,
                    'content' => 'From pre-wedding conceptualization to receiving the Italian leather album, the concierge experience was bespoke and elite. Truly the best production house in the country.',
                    'event_type' => 'Pre-Wedding & Studio Portraits',
                    'is_featured' => true,
                    'order' => 3,
                ],
            ];

            foreach ($testimonials as $t) {
                Testimonial::create($t);
            }
        }

        // 3. FAQS
        if (Faq::count() === 0) {
            $faqs = [
                [
                    'question' => 'How far in advance should we reserve our photoshoot session?',
                    'answer' => 'For wedding cinematography and commercial campaigns, we recommend reserving 4 to 8 weeks in advance to ensure availability. Portrait and boutique studio sessions can typically be scheduled 1 to 2 weeks ahead.',
                    'category' => 'booking',
                    'order' => 1,
                    'is_active' => true,
                ],
                [
                    'question' => 'What happens after I complete my booking payment online?',
                    'answer' => 'Immediately upon successful payment via Cashfree/Razorpay, you receive an instant SMS and email confirmation with your unique booking reference ID. Our senior concierge will contact you within 4 hours to confirm moodboards and call-sheet details.',
                    'category' => 'booking',
                    'order' => 2,
                    'is_active' => true,
                ],
                [
                    'question' => 'Can we reschedule our session if needed?',
                    'answer' => 'Yes! We offer 100% free rescheduling when requested at least 5 days prior to your session date. Rescheduled dates are valid for up to 90 days, subject to studio availability.',
                    'category' => 'pricing',
                    'order' => 3,
                    'is_active' => true,
                ],
                [
                    'question' => 'What camera equipment and resolution do you shoot with?',
                    'answer' => 'We shoot exclusively on 100MP medium-format digital systems, Hasselblad & Sony FX cinema cameras, paired with premium prime lenses and Broncolor studio lighting for world-class clarity.',
                    'category' => 'delivery',
                    'order' => 4,
                    'is_active' => true,
                ],
                [
                    'question' => 'When and how will our digital photos and albums be delivered?',
                    'answer' => 'Raw proofing sheets are shared via private cloud gallery within 48 to 72 hours. Final color-graded high-resolution photos are delivered in 7 to 14 days, and custom handcrafted albums ship within 21 days with insured tracking.',
                    'category' => 'delivery',
                    'order' => 5,
                    'is_active' => true,
                ],
            ];

            foreach ($faqs as $f) {
                Faq::create($f);
            }
        }

        // 4. TEAM MEMBERS
        if (TeamMember::count() === 0) {
            $members = [
                [
                    'name' => 'Arjun Mehta',
                    'role' => 'Founder & Head Photographer',
                    'image_path' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?q=80&w=400&auto=format&fit=crop',
                    'bio' => '12+ years shooting high-fashion editorial campaigns and celebrity portraits with mastery over contrast geometry.',
                    'instagram_url' => 'https://instagram.com',
                    'order' => 1,
                    'is_active' => true,
                ],
                [
                    'name' => 'Priya Sen',
                    'role' => 'Senior Wardrobe & Concept Stylist',
                    'image_path' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=400&auto=format&fit=crop',
                    'bio' => 'Former fashion magazine stylist guiding couture designers and clients on color harmonization and fabric drape.',
                    'instagram_url' => 'https://instagram.com',
                    'order' => 2,
                    'is_active' => true,
                ],
                [
                    'name' => 'Vikram Roy',
                    'role' => 'Cinematic Wedding Director',
                    'image_path' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?q=80&w=400&auto=format&fit=crop',
                    'bio' => 'Specializes in 4K slow-shutter aesthetics and immortalizing raw emotional moments across destination weddings.',
                    'instagram_url' => 'https://instagram.com',
                    'order' => 3,
                    'is_active' => true,
                ],
                [
                    'name' => 'Elena Rostova',
                    'role' => 'Master Colorist & Retoucher',
                    'image_path' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?q=80&w=400&auto=format&fit=crop',
                    'bio' => 'Fine arts graduate ensuring pixel-perfect dynamic range editing, skin tones, and rich analog color curves.',
                    'instagram_url' => 'https://instagram.com',
                    'order' => 4,
                    'is_active' => true,
                ],
            ];

            foreach ($members as $m) {
                TeamMember::create($m);
            }
        }
    }
}
