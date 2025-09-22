<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net" />
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Tailwind (CDN for quick prototyping; replace with compiled CSS for production) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* Smooth scroll for modern browsers */
        html { scroll-behavior: smooth; }

        /* Slightly larger focus ring for accessibility */
        :focus { outline: 3px solid rgba(99,102,241,0.15); outline-offset: 2px; }

        /* Make sure sections snap nicely on short screens (optional) */
        @media (min-width: 640px) {
            body { scroll-padding-top: 96px; } /* offset for fixed header on larger screens */
        }
    </style>
</head>
<body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#e9e7e3] antialiased">

    <!-- Header / Nav -->
    <header class="fixed top-0 left-0 right-0 z-50 bg-white/80 dark:bg-[#080808]/80 backdrop-blur-sm border-b border-gray-100 dark:border-[#191919]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Brand -->
                <a href="#hero" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-[#fff6f6] to-[#ffecec] dark:from-[#1b0e0e] dark:to-[#2b0f0f] flex items-center justify-center border border-[#ffecec] dark:border-[#3a0b0b]">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none"><path d="M3 12h18M12 3v18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <span class="font-semibold text-lg text-[#111827] dark:text-[#f8f7f5]"></span>
                </a>

                <!-- Desktop nav -->
                <nav class="hidden lg:flex items-center gap-6">
                    <a href="#hero" class="nav-link text-sm">Home</a>
                    <a href="#features" class="nav-link text-sm">Features</a>
                    <a href="#upload" class="nav-link text-sm">CSV Upload</a>
                    <a href="#precision" class="nav-link text-sm">Precision</a>
                    <a href="#contact" class="nav-link text-sm">Contact</a>
                </nav>

                <!-- Auth + Mobile toggle -->
                <div class="flex items-center gap-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="hidden sm:inline-block px-4 py-2 rounded-md bg-[#111827] text-white text-sm shadow-sm">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="hidden sm:inline-block px-4 py-2 rounded-md border border-transparent hover:border-gray-200 text-sm">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="hidden sm:inline-block px-4 py-2 rounded-md bg-[#b91c1c] text-white text-sm shadow-sm">Register</a>
                            @endif
                        @endauth
                    @endif

                    <!-- Mobile menu button -->
                    <button id="mobileToggle" aria-expanded="false" class="lg:hidden px-2 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-white/5">
                        <svg id="mobileOpenIcon" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/></svg>
                        <svg id="mobileCloseIcon" class="hidden w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile nav panel -->
        <div id="mobileMenu" class="lg:hidden max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 hidden">
            <div class="py-4 border-t border-gray-100 dark:border-[#151515] flex flex-col gap-2">
                <a href="#hero" class="mobile-nav-link py-2 rounded-md">Home</a>
                <a href="#features" class="mobile-nav-link py-2 rounded-md">Features</a>
                <a href="#upload" class="mobile-nav-link py-2 rounded-md">CSV Upload</a>
                <a href="#precision" class="mobile-nav-link py-2 rounded-md">Precision</a>
                <a href="#contact" class="mobile-nav-link py-2 rounded-md">Contact</a>

                <div class="pt-2 border-t border-gray-50 dark:border-[#111]">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="block py-2 rounded-md">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="block py-2 rounded-md">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="block py-2 rounded-md">Register</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </header>

    <!-- Page content -->
    <main class="pt-24">
        <!-- HERO -->
        <section id="hero" class="min-h-[70vh] flex items-center">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-[#111827] dark:text-[#f8f7f5]">
                            Automatic Expense Tracker
                        </h1>
                        <p class="mt-6 text-lg text-[#55524f] dark:text-[#cfcfc9] max-w-2xl">
                            Upload receipts as <strong class="text-[#b91c1c] dark:text-[#ffb3b3]">CSV</strong> files and have expenses auto-populated for fast reconciliation. Accurate monetary handling to avoid precision bugs.
                        </p>

                        <div class="mt-8 flex flex-wrap gap-3">
                            <a href="#upload" class="inline-block px-6 py-3 rounded-md bg-[#b91c1c] text-white font-medium shadow hover:shadow-md">Upload CSV</a>
                            <a href="#features" class="inline-block px-6 py-3 rounded-md border border-gray-200">See features</a>
                        </div>

                        <div class="mt-6 flex flex-wrap gap-3">
                            <span class="inline-flex items-center gap-2 bg-[#fff1f1] text-[#8a1000] px-3 py-1.5 rounded-full text-sm font-medium border border-[#ffecec]">
                                CSV upload
                            </span>
                            <span class="inline-flex items-center gap-2 bg-[#f0fdf4] text-[#065f46] px-3 py-1.5 rounded-full text-sm font-medium border border-[#ecfdf3]">
                                Auto-populate entries
                            </span>
                            <span class="inline-flex items-center gap-2 bg-[#eef2ff] text-[#3730a3] px-3 py-1.5 rounded-full text-sm font-medium border border-[#eef2ff]">
                                Accurate decimals
                            </span>
                        </div>
                    </div>

                    <div>
                        <div class="rounded-3xl p-8 shadow-lg bg-gradient-to-br from-white to-[#fff6f6] dark:from-[#0f0f0f] dark:to-[#1b1a19] border border-gray-100 dark:border-[#202022]">
                            <h3 class="text-xl font-semibold dark:text-[#f4f4f3]">Quick demo CSV</h3>
                            <p class="mt-2 text-sm text-[#6b6b67] dark:text-[#bdbdb7]">
                                Paste a sample three-line CSV below (date,merchant,amount,currency,category) to preview parsed rows.
                            </p>

                            <pre class="mt-4 p-4 bg-gray-50 dark:bg-[#0b0b0b] rounded-md text-sm overflow-auto border border-gray-100 dark:border-[#171717]">
2025-09-01,Starbucks,4.50,USD,Meals
2025-09-02,Stripe,19.99,USD,Subscriptions
2025-09-03,Uber,12.30,USD,Travel
                            </pre>

                            <div class="mt-4 text-sm text-[#55524f] dark:text-[#cfcfc9]">
                                Tip: prefer amounts in cents or use <code class="bg-gray-100 dark:bg-[#111111] px-2 py-0.5 rounded">decimal(13,4)</code> in DB.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FEATURES -->
        <section id="features" class="py-16">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto">
                    <h2 class="text-3xl font-semibold dark:text-[#f4f4f3]">Features</h2>
                    <p class="mt-3 text-sm text-[#6b6b67] dark:text-[#bdbdb7]">A modern workflow for ingesting receipts and keeping your books accurate.</p>
                </div>

                <div class="mt-10 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="p-6 rounded-2xl bg-white dark:bg-[#0b0b0b] border border-gray-100 dark:border-[#171717] shadow-sm">
                        <h3 class="font-semibold">CSV Receipt Upload</h3>
                        <p class="mt-2 text-sm text-[#6b6b67] dark:text-[#bdbdb7]">Drop CSVs; the parser extracts date, merchant, amount, currency and category automatically.</p>
                    </div>

                    <div class="p-6 rounded-2xl bg-white dark:bg-[#0b0b0b] border border-gray-100 dark:border-[#171717] shadow-sm">
                        <h3 class="font-semibold">Auto-categorization</h3>
                        <p class="mt-2 text-sm text-[#6b6b67] dark:text-[#bdbdb7]">Apply rules and suggestion engine to classify expenses (travel, meals, supplies).</p>
                    </div>

                    <div class="p-6 rounded-2xl bg-white dark:bg-[#0b0b0b] border border-gray-100 dark:border-[#171717] shadow-sm">
                        <h3 class="font-semibold">Precision & Storage</h3>
                        <p class="mt-2 text-sm text-[#6b6b67] dark:text-[#bdbdb7]">Store as integer cents or decimal columns to avoid rounding errors in calculations and reports.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- UPLOAD -->
        <section id="upload" class="py-16 bg-gray-50 dark:bg-[#070707]">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-[#0b0b0b] rounded-2xl p-8 md:p-10 border border-gray-100 dark:border-[#1f1f1f] shadow-md">
                    <h2 class="text-xl font-semibold dark:text-[#f4f4f3]">CSV Upload</h2>
                    <p class="mt-2 text-sm text-[#6b6b67] dark:text-[#bdbdb7]">Upload or drop CSV files and we'll parse them into expense records.</p>

                    <!-- Simple non-functional upload form placeholder - adapt to your backend -->
                    <form class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4" action="#" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-1">Select CSV</label>
                            <input type="file" disabled name="csv" accept=".csv" class="block w-full text-sm file:px-4 file:py-2 file:rounded-md file:border file:border-gray-200 file:bg-white file:text-sm file:font-medium cursor-pointer" />
                            <p class="mt-2 text-xs text-[#737373] dark:text-[#bdbdb7]">Accepted: .csv — Make sure the file includes headers: <code>date,merchant,amount,currency,category</code></p>
                        </div>

                        <div class="md:col-span-1 flex items-end">
                            <button type="submit" class="w-full px-4 py-3 rounded-md bg-[#b91c1c] text-white font-medium" disabled>Upload</button>
                        </div>
                    </form>

                    <div class="mt-6 text-sm text-[#6b6b67] dark:text-[#cfcfc9]">
                        After processing, parsed rows will appear in the Expense list for review and tagging.
                    </div>
                </div>
            </div>
        </section>

        <!-- PRECISION -->
        <section id="precision" class="py-16">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                    <div>
                        <h3 class="text-2xl font-semibold dark:text-[#f4f4f3]">Why correct decimal handling matters</h3>
                        <p class="mt-3 text-sm text-[#6b6b67] dark:text-[#bdbdb7]">Using integer cents or well-defined decimals prevents subtle errors when summing or converting currencies. This avoids reconciliation issues and accounting discrepancies.</p>

                        <ul class="mt-4 space-y-3 text-sm text-[#55524f] dark:text-[#cfcfc9]">
                            <li>Store monetary values as integer cents for guaranteed accuracy.</li>
                            <li>Use `DECIMAL(13,4)` for stock prices or fractional currencies when needed.</li>
                            <li>Display values formatted for the user's locale at render time.</li>
                        </ul>
                    </div>

                    <div class="rounded-xl p-6 bg-white dark:bg-[#0b0b0b] border border-gray-100 dark:border-[#171717]">
                        <h4 class="font-medium dark:text-[#fff]">Example (recommended)</h4>
                        <pre class="mt-3 p-4 bg-gray-50 dark:bg-[#070707] rounded text-sm overflow-auto">-- migrations
$table->unsignedBigInteger('amount_cents'); // store 450 for $4.50
$table->string('currency', 3);</pre>
                    </div>
                </div>
            </div>
        </section>

        <!-- CONTACT / FOOTER -->
        <section id="contact" class="py-12 bg-gray-100 dark:bg-[#070707]">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h3 class="text-xl font-semibold dark:text-[#f4f4f3]">Get in touch</h3>
                <p class="mt-2 text-sm text-[#6b6b67] dark:text-[#bdbdb7]">Questions, feature requests, or help integrating — reach out and we'll reply.</p>

                <div class="mt-6 flex flex-col sm:flex-row sm:justify-center gap-3">
                    <!-- <a href="mailto:hello@example.com" class="inline-block px-5 py-3 rounded-md border border-gray-200">Email us</a> -->
                    <a href="#hero" class="inline-block px-5 py-3 rounded-md bg-[#111827] text-white">Back to top</a>
                </div>

                <div class="mt-8 text-sm text-[#6b6b67] dark:text-[#bdbdb7]">
                    &copy; {{ date('Y') }} — Built with care.
                </div>
            </div>
        </section>
    </main>

    <!-- Scripts -->
    <script>
        // Mobile menu toggle
        (function () {
            const toggle = document.getElementById('mobileToggle');
            const menu = document.getElementById('mobileMenu');
            const openIcon = document.getElementById('mobileOpenIcon');
            const closeIcon = document.getElementById('mobileCloseIcon');

            toggle.addEventListener('click', () => {
                const expanded = toggle.getAttribute('aria-expanded') === 'true';
                toggle.setAttribute('aria-expanded', String(!expanded));
                menu.classList.toggle('hidden');
                openIcon.classList.toggle('hidden');
                closeIcon.classList.toggle('hidden');
            });

            // Close mobile menu when clicking a mobile nav link
            document.querySelectorAll('.mobile-nav-link').forEach(a => {
                a.addEventListener('click', () => {
                    if (!menu.classList.contains('hidden')) {
                        menu.classList.add('hidden');
                        openIcon.classList.remove('hidden');
                        closeIcon.classList.add('hidden');
                        toggle.setAttribute('aria-expanded', 'false');
                    }
                });
            });
        })();

        // Smooth scrolling with offset for fixed header (to ensure section top isn't hidden)
        (function () {
            const headerHeight = () => {
                const h = document.querySelector('header');
                return h ? h.offsetHeight + 12 : 80;
            };

            function scrollToHash(hash) {
                if (!hash) return;
                const el = document.querySelector(hash);
                if (!el) return;
                const top = el.getBoundingClientRect().top + window.pageYOffset - headerHeight();
                window.scrollTo({ top, behavior: 'smooth' });
            }

            // Intercept anchor clicks that target page sections
            document.querySelectorAll('a[href^="#"]').forEach(a => {
                a.addEventListener('click', function (e) {
                    const href = this.getAttribute('href');
                    // If it's exactly "#" let default behavior happen
                    if (href === '#') return;
                    const target = document.querySelector(href);
                    if (target) {
                        e.preventDefault();
                        scrollToHash(href);
                        // update URL hash without jumping
                        history.pushState(null, '', href);
                    }
                });
            });

            // If page loads with a hash, scroll offset it (useful when linking directly)
            if (window.location.hash) {
                setTimeout(() => scrollToHash(window.location.hash), 50);
            }
        })();

        // Active nav link highlighting using IntersectionObserver
        (function () {
            const sections = document.querySelectorAll('section[id]');
            const navLinks = document.querySelectorAll('.nav-link');

            if (!sections.length || !navLinks.length) return;

            const opts = { root: null, rootMargin: '-40% 0px -40% 0px', threshold: 0 };
            const io = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    const id = entry.target.getAttribute('id');
                    const matching = document.querySelectorAll('.nav-link[href="#' + id + '"]');
                    if (entry.isIntersecting) {
                        matching.forEach(m => m.classList.add('text-[#b91c1c]','font-semibold'));
                    } else {
                        matching.forEach(m => m.classList.remove('text-[#b91c1c]','font-semibold'));
                    }
                });
            }, opts);

            sections.forEach(s => io.observe(s));
        })();
    </script>
</body>
</html>
