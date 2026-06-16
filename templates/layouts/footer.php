    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <img src="<?= base_url('images/logo.png') ?>" alt="AeroTAXI" class="h-10 mb-4">
                    <p class="text-gray-400 text-sm">Reliable airport transfers across the UK. Book your ride with confidence.</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-300 uppercase tracking-wider mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="<?= base_url('/') ?>" class="text-gray-400 hover:text-white text-sm transition">Home</a></li>
                        <li><a href="<?= base_url('coverage') ?>" class="text-gray-400 hover:text-white text-sm transition">Coverage</a></li>
                        <li><a href="<?= base_url('help') ?>" class="text-gray-400 hover:text-white text-sm transition">Help</a></li>
                        <li><a href="<?= base_url('booking/lookup') ?>" class="text-gray-400 hover:text-white text-sm transition">My Booking</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-300 uppercase tracking-wider mb-4">Legal</h3>
                    <ul class="space-y-2">
                        <li><a href="<?= base_url('legal/terms') ?>" class="text-gray-400 hover:text-white text-sm transition">Terms of Service</a></li>
                        <li><a href="<?= base_url('legal/privacy-policy') ?>" class="text-gray-400 hover:text-white text-sm transition">Privacy Policy</a></li>
                        <li><a href="<?= base_url('legal/privacy-statement') ?>" class="text-gray-400 hover:text-white text-sm transition">Privacy Statement</a></li>
                        <li><a href="<?= base_url('legal/cookie-policy') ?>" class="text-gray-400 hover:text-white text-sm transition">Cookie Policy</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-300 uppercase tracking-wider mb-4">Newsletter</h3>
                    <p class="text-gray-400 text-sm mb-4">Subscribe for exclusive deals and updates.</p>
                    <form method="POST" action="<?= base_url('subscribe') ?>" class="flex gap-2">
                        <?= csrf_field() ?>
                        <input type="email" name="email" placeholder="Your email" required
                               class="flex-1 bg-gray-800 text-white border border-gray-700 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 placeholder-gray-500">
                        <button type="submit" class="bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-semibold rounded-lg px-4 py-2 text-sm transition">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                    <?php if ($msg = get_flash('subscribe_success')): ?>
                        <p class="text-green-400 text-xs mt-2"><?= e($msg) ?></p>
                    <?php endif; ?>
                    <?php if ($msg = get_flash('subscribe_error')): ?>
                        <p class="text-red-400 text-xs mt-2"><?= e($msg) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-gray-500 text-sm">&copy; <?= date('Y') ?> AeroTAXI. All rights reserved.</p>
                <div class="flex gap-4">
                    <span class="text-gray-500 text-sm">AeroTAXI LLC — Dover, DE 19904, US</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Tawk.to Live Chat -->
    <script type="text/javascript">
    var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
    (function(){
    var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
    s1.async=true;
    s1.src='https://embed.tawk.to/69cdfcdcbe444a1c3a7ffc6f/1jl6a7g5l';
    s1.charset='UTF-8';
    s1.setAttribute('crossorigin','*');
    s0.parentNode.insertBefore(s1,s0);
    })();
    </script>

</body>
</html>
