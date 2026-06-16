<?php
// Ported from: help.blade.php
$faqs = Database::fetchAll("SELECT * FROM faqs ORDER BY sort_order");
?>
<?php require BASE_PATH . '/templates/layouts/header.php'; ?>
<?php $__page_title = 'AeroTAXI - Help'; ?>

    <section class="bg-cream py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-4xl font-bold text-gray-900">Help Centre</h1>
        </div>
    </section>

    
    <section id="my-booking" class="py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl p-8 shadow-sm">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">My Booking</h2>
                <p class="text-gray-600 mb-6">
                    Amend or cancel your reservation online up to 24 hours before the scheduled pick-up/flight arrival time.
                </p>
                <form action="<?= base_url('booking/lookup') ?>" method="GET" class="flex items-center gap-4">
                    <input type="text" name="reference" placeholder="Booking Reference (e.g. ATH-XXXXXXXX)"
                           class="flex-1 border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-yellow-400 uppercase tracking-wider" required>
                    <button type="submit" class="bg-yellow-400 hover:bg-yellow-500 rounded-xl px-6 py-3 font-semibold text-gray-900 transition whitespace-nowrap">
                        Find Booking
                    </button>
                </form>
            </div>
        </div>
    </section>

    
    <section id="contact-us" class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-4 text-center">Contact Us</h2>
            <p class="text-gray-600 text-center max-w-2xl mx-auto mb-10">
                We're here to help! Whether you have a question, need assistance with a reservation, or want to share feedback, don't hesitate to reach out.
            </p>

            <?php if (get_flash('success')): ?>
                <div class="max-w-2xl mx-auto mb-6 bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3">
                    <?= e(get_flash('success')) ?>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">

                
                <div class="space-y-8">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                            <i class="fa-solid fa-envelope text-primary mr-2"></i> Email
                        </h3>
                        <a href="mailto:supportaerotaxi@gmail.com" class="text-primary hover:underline">
                            supportaerotaxi@gmail.com
                        </a>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                            <i class="fa-solid fa-location-dot text-primary mr-2"></i> Address
                        </h3>
                        <p class="text-gray-600">
                            1111B S Governors Ave STE 26937<br>
                            Dover, DE 19904, US
                        </p>
                    </div>
                </div>

                
                <div class="bg-white rounded-xl p-8 shadow-sm">
                    <form action="<?= base_url('contact') ?>" method="POST" class="space-y-4">
                        <?= csrf_field() ?>
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input type="text" id="name" name="name" required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" id="email" name="email" required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        </div>
                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject (optional)</label>
                            <input type="text" id="subject" name="subject"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        </div>
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                            <textarea id="message" name="message" rows="5" required
                                      class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-yellow-400"></textarea>
                        </div>
                        <button type="submit" class="bg-yellow-400 hover:bg-yellow-500 rounded-full px-8 py-3 font-semibold text-gray-900 transition w-full">
                            Send Message
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </section>

    
    <section id="faqs" class="py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">Frequently Asked Questions</h2>

            <div class="divide-y divide-gray-200">
                <?php foreach ($faqs as $index => $faq): ?>
                    <div x-data="{ open: false }" class="py-4">
                        <button @click="open = !open" class="flex items-center justify-between w-full text-left">
                            <span class="font-semibold text-gray-900"><?= e($faq->question) ?></span>
                            <i class="fa-solid fa-chevron-down text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" x-collapse class="mt-3 text-gray-600 text-sm leading-relaxed">
                            <?= e($faq->answer) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>


<?php require BASE_PATH . '/templates/layouts/footer.php'; ?>