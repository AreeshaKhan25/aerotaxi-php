<?php
// Ported from: coverage.blade.php
$airports = Database::fetchAll("SELECT * FROM airports ORDER BY sort_order");
?>
<?php require BASE_PATH . '/templates/layouts/header.php'; ?>
<?php $__page_title = 'AeroTAXI - Transfer coverage'; ?>

    <section class="bg-cream py-12 lg:py-16">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 mb-3">Airports</h1>
            <p class="text-gray-500 text-base sm:text-lg max-w-2xl mx-auto">Book reliable airport transfers to and from all major UK airports</p>
        </div>
    </section>

    
    <section class="py-12 lg:py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex items-center gap-3 mb-10">
                <span class="text-3xl">🇬🇧</span>
                <h2 class="text-2xl font-bold text-gray-900">United Kingdom</h2>
                <span class="text-sm text-gray-400 ml-auto"><?= e($airports->count()) ?> airports</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                <?php foreach ($airports as $airport): ?>
                <div class="bg-white rounded-2xl overflow-hidden border border-gray-100 hover:shadow-xl transition-all duration-300 group">
                    
                    <div class="h-36 bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center p-6 relative overflow-hidden">
                        <div class="absolute inset-0 bg-primary/[0.03] group-hover:bg-primary/[0.06] transition-colors"></div>
                        <?php if ($airport->image): ?>
                        <img src="<?= e($airport->image) ?>" alt="<?= e($airport->name) ?>"
                             class="max-h-full max-w-[80%] object-contain relative z-10 group-hover:scale-110 transition-transform duration-300">
                        <?php else: ?>
                        <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center relative z-10">
                            <i class="fa-solid fa-plane text-2xl text-primary"></i>
                        </div>
                        <?php endif; ?>
                    </div>

                    
                    <div class="p-5">
                        <div class="flex items-start justify-between gap-2 mb-1">
                            <h3 class="font-bold text-gray-900 text-base leading-tight"><?= e($airport->name) ?></h3>
                            <span class="flex-shrink-0 bg-gray-100 text-gray-500 text-xs font-bold px-2 py-1 rounded-md"><?= e($airport->code) ?></span>
                        </div>
                        <p class="text-sm text-gray-400 mb-3 flex items-center gap-1">
                            <i class="fa-solid fa-location-dot text-[10px]"></i>
                            <?= e($airport->city) ?>, <?= e($airport->country) ?>
                        </p>
                        <p class="text-sm text-gray-500 leading-relaxed"><?= e($airport->description) ?></p>

                        
                        <a href="<?= base_url('/') ?>" class="mt-4 flex items-center gap-2 text-sm font-semibold text-primary hover:text-primary/80 transition-colors group/link">
                            Book a transfer
                            <i class="fa-solid fa-arrow-right text-xs group-hover/link:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

        </div>
    </section>

    
    <section class="py-14 bg-lightgreen">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-2xl font-bold text-gray-900 mb-3">Can't find your airport?</h2>
            <p class="text-gray-600 mb-6">Contact our support team and we'll do our best to arrange a transfer for you.</p>
            <a href="<?= base_url('help') ?>#contact-us"
               class="inline-flex items-center gap-2 bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-semibold rounded-xl px-8 py-3.5 transition-all hover:shadow-md active:scale-[0.98]">
                <i class="fa-solid fa-envelope text-sm"></i>
                Contact Us
            </a>
        </div>
    </section>


<?php require BASE_PATH . '/templates/layouts/footer.php'; ?>