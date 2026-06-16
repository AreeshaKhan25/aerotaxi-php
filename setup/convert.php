<?php
/**
 * Batch convert Laravel Blade templates to classical PHP
 * Copies views from Laravel project, transforms Blade syntax
 */

$laravelViews = 'd:/006/web/aerotaxi/resources/views';
$phpProject   = 'd:/006/web/aerotaxi-php';

// File mapping: [source blade] => [destination php]
$files = [
    'home.blade.php'                        => 'pages/home.php',
    'coverage.blade.php'                    => 'pages/coverage.php',
    'help.blade.php'                        => 'pages/help.php',
    'legal/terms-of-service.blade.php'      => 'pages/legal/terms.php',
    'legal/privacy-policy.blade.php'        => 'pages/legal/privacy-policy.php',
    'legal/privacy-statement.blade.php'     => 'pages/legal/privacy-statement.php',
    'legal/cookie-policy.blade.php'         => 'pages/legal/cookie-policy.php',
    'booking/check-prices.blade.php'        => 'pages/booking/check-prices.php',
    'booking/transfer-details.blade.php'    => 'pages/booking/transfer-details.php',
    'booking/payment.blade.php'             => 'pages/booking/payment.php',
    'booking/confirmation.blade.php'        => 'pages/booking/confirmation.php',
    'booking/lookup.blade.php'              => 'pages/booking/lookup.php',
    'admin/stats.blade.php'                 => 'admin/stats.php',
    'admin/dashboard.blade.php'             => 'admin/dashboard.php',
    'admin/bookings.blade.php'              => 'admin/bookings.php',
    'admin/booking-detail.blade.php'        => 'admin/booking-detail.php',
    'admin/fleet.blade.php'                 => 'admin/fleet.php',
    'admin/zones-map.blade.php'             => 'admin/zones-map.php',
    'admin/contact-messages.blade.php'      => 'admin/contact-messages.php',
    'admin/promotions.blade.php'            => 'admin/promotions.php',
    'admin/subscribers.blade.php'           => 'admin/subscribers.php',
    'admin/auth/login.blade.php'            => 'admin/login.php',
];

// Route name -> URL mapping
$routes = [
    "{{ route('home') }}"                       => "<?= base_url('/') ?>",
    "{{ route('coverage') }}"                    => "<?= base_url('coverage') ?>",
    "{{ route('help') }}"                        => "<?= base_url('help') ?>",
    "{{ route('booking.check-prices') }}"        => "<?= base_url('your-ride') ?>",
    "{{ route('booking.transfer-details') }}"    => "<?= base_url('transfer-details') ?>",
    "{{ route('booking.payment') }}"             => "<?= base_url('payment') ?>",
    "{{ route('booking.lookup') }}"              => "<?= base_url('booking/lookup') ?>",
    "{{ route('booking.store') }}"               => "<?= base_url('booking') ?>",
    "{{ route('contact.store') }}"               => "<?= base_url('contact') ?>",
    "{{ route('legal.terms') }}"                 => "<?= base_url('terms-of-service') ?>",
    "{{ route('legal.privacy-policy') }}"        => "<?= base_url('privacy-policy') ?>",
    "{{ route('legal.privacy-statement') }}"     => "<?= base_url('privacy-statement') ?>",
    "{{ route('legal.cookie-policy') }}"         => "<?= base_url('cookie-policy') ?>",
    "{{ route('admin.login') }}"                 => "<?= base_url('admin/login') ?>",
    "{{ route('admin.login.submit') }}"          => "<?= base_url('admin/login') ?>",
    "{{ route('admin.logout') }}"                => "<?= base_url('admin/logout') ?>",
    "{{ route('admin.stats') }}"                 => "<?= base_url('admin') ?>",
    "{{ route('admin.dashboard') }}"             => "<?= base_url('admin/jobs') ?>",
    "{{ route('admin.bookings') }}"              => "<?= base_url('admin/bookings') ?>",
    "{{ route('admin.fleet') }}"                 => "<?= base_url('admin/fleet') ?>",
    "{{ route('admin.zones-map') }}"             => "<?= base_url('admin/zones-map') ?>",
    "{{ route('admin.contact-messages') }}"      => "<?= base_url('admin/contact-messages') ?>",
    "{{ route('admin.promotions') }}"            => "<?= base_url('admin/promotions') ?>",
    "{{ route('admin.subscribers') }}"           => "<?= base_url('admin/subscribers') ?>",
    "{{ route('admin.promotions.send') }}"       => "<?= base_url('admin/promotions/send') ?>",
    "{{ route('admin.notifications.mark-read') }}" => "<?= base_url('admin/notifications/mark-read') ?>",
];

function convertBlade(string $content, array $routes, bool $isAdmin, bool $isPublic): string
{
    // Replace route() calls
    $content = str_replace(array_keys($routes), array_values($routes), $content);

    // Remove @extends and @section/@endsection wrappers (we'll add includes manually)
    $content = preg_replace("/@extends\('.*?'\)\s*/", '', $content);
    $content = preg_replace("/@section\('title',\s*'(.*?)'\)\s*/", '<?php $__page_title = \'$1\'; ?>' . "\n", $content);
    $content = preg_replace("/@section\('title',\s*(.+?)\)\s*/", '<?php $__page_title = $1; ?>' . "\n", $content);
    $content = preg_replace("/@section\('content'\)\s*/", '', $content);
    $content = preg_replace("/@section\('head'\)\s*/", '<?php ob_start(); ?>' . "\n", $content);
    $content = preg_replace("/@endsection\s*/", '', $content);
    $content = preg_replace("/@yield\('title',\s*'(.*?)'\)/", '$1', $content);
    $content = preg_replace("/@yield\('head'\)/", '<?php $__extra_head = ob_get_clean(); ?>', $content);
    $content = preg_replace("/@yield\('content'\)/", '', $content);

    // @csrf
    $content = str_replace('@csrf', '<?= csrf_field() ?>', $content);

    // {{ csrf_token() }}
    $content = str_replace('{{ csrf_token() }}', '<?= csrf_token() ?>', $content);

    // Blade comments {{-- ... --}}
    $content = preg_replace('/\{\{--.*?--\}\}/s', '', $content);

    // @json($var)
    $content = preg_replace('/@json\((.+?)\)/', '<?= json_encode($1) ?>', $content);

    // Carbon::parse(...) -> format_date
    $content = preg_replace('/\\\?Carbon\\\Carbon::parse\((.+?)\)->format\((.+?)\)/', 'format_date($1, $2)', $content);

    // {!! $var !!} (unescaped)
    $content = preg_replace('/\{!!\s*(.+?)\s*!!\}/', '<?= $1 ?>', $content);

    // {{ $var }} -> PHP echo e($var) but NOT inside <script> tags
    // First, protect script blocks
    $scriptBlocks = [];
    $content = preg_replace_callback('/<script\b[^>]*>(.*?)<\/script>/s', function($m) use (&$scriptBlocks) {
        $key = '___SCRIPT_' . count($scriptBlocks) . '___';
        $scriptBlocks[$key] = $m[0];
        return $key;
    }, $content);

    // Convert {{ expr }} outside scripts
    $content = preg_replace_callback('/\{\{\s*(.+?)\s*\}\}/', function($m) {
        $expr = $m[1];
        // If it contains -> or ( or complex expression, wrap in e()
        if (preg_match('/[\$]/', $expr)) {
            return '<?= e(' . $expr . ') ?>';
        }
        return '<?= e(' . $expr . ') ?>';
    }, $content);

    // Restore script blocks - convert {{ }} inside scripts to raw output
    foreach ($scriptBlocks as $key => $block) {
        // In scripts, {{ }} should output raw (it's JS template or JSON)
        $block = preg_replace('/\{\{\s*(.+?)\s*\}\}/', '<?= $1 ?>', $block);
        $content = str_replace($key, $block, $content);
    }

    // @if / @elseif / @else / @endif
    $content = preg_replace('/@if\s*\((.+?)\)\s*$/m', '<?php if ($1): ?>', $content);
    $content = preg_replace('/@elseif\s*\((.+?)\)\s*$/m', '<?php elseif ($1): ?>', $content);
    $content = str_replace('@else', '<?php else: ?>', $content);
    $content = str_replace('@endif', '<?php endif; ?>', $content);

    // @foreach / @endforeach
    $content = preg_replace('/@foreach\s*\((.+?)\)\s*$/m', '<?php foreach ($1): ?>', $content);
    $content = str_replace('@endforeach', '<?php endforeach; ?>', $content);

    // @forelse / @empty / @endforelse
    $content = preg_replace('/@forelse\s*\((.+?) as (.+?)\)\s*$/m', '<?php if (!empty($1)): foreach ($1 as $2): ?>', $content);
    $content = str_replace('@empty', '<?php endforeach; else: ?>', $content);
    $content = str_replace('@endforelse', '<?php endif; ?>', $content);

    // @php / @endphp
    $content = str_replace('@php', '<?php', $content);
    $content = str_replace('@endphp', '?>', $content);

    // @unless / @endunless
    $content = preg_replace('/@unless\s*\((.+?)\)/', '<?php if (!($1)): ?>', $content);
    $content = str_replace('@endunless', '<?php endif; ?>', $content);

    // Clean up some Laravel-specific patterns
    // old('field') already works in our helpers
    // session('key') -> get_flash('key')
    $content = str_replace("session('success')", "get_flash('success')", $content);
    $content = str_replace("session('error')", "get_flash('error')", $content);
    $content = str_replace("session('promo_success')", "get_flash('promo_success')", $content);
    $content = str_replace("session('promo_error')", "get_flash('promo_error')", $content);

    // Fix route patterns with dynamic params
    $content = preg_replace(
        "/\\\$b->depart_date\?->format\('(.+?)'\)/",
        "format_date(\$b->depart_date, '$1')",
        $content
    );
    $content = preg_replace(
        "/\\\$booking->depart_date\?->format\('(.+?)'\)/",
        "format_date(\$booking->depart_date, '$1')",
        $content
    );
    $content = preg_replace(
        "/\\\$([a-z_]+)->created_at\?->format\('(.+?)'\)/",
        "format_date(\$$1->created_at, '$2')",
        $content
    );
    $content = preg_replace(
        "/\\\$([a-z_]+)->updated_at\?->format\('(.+?)'\)/",
        "format_date(\$$1->updated_at, '$2')",
        $content
    );
    $content = preg_replace(
        "/\\\$notif->created_at\?->diffForHumans\(\)/",
        "time_ago(\$notif->created_at)",
        $content
    );

    // Str::limit()
    $content = preg_replace(
        "/Str::limit\((.+?),\s*(\d+)\)/",
        "str_limit($1, $2)",
        $content
    );

    // url()->previous()
    $content = str_replace("url()->previous()", "(\$_SERVER['HTTP_REFERER'] ?? base_url('/'))", $content);

    // Auth::guard('admin')->user()->name
    $content = str_replace(
        "Auth::guard('admin')->user()->name ?? 'Admin'",
        "(admin_user()->name ?? 'Admin')",
        $content
    );

    // request()->routeIs() - replace with is_current_prefix checks
    $content = preg_replace(
        "/request\(\)->routeIs\(\.\.\.(.+?)\)/",
        "false /* TODO: active nav check */",
        $content
    );

    // number_format already works in PHP
    // date('Y') already works

    return $content;
}

echo "Starting conversion...\n";

foreach ($files as $src => $dest) {
    $srcPath = "$laravelViews/$src";
    $destPath = "$phpProject/$dest";

    if (!file_exists($srcPath)) {
        echo "SKIP: $src (not found)\n";
        continue;
    }

    $content = file_get_contents($srcPath);
    $isAdmin = str_starts_with($dest, 'admin/');
    $isPublic = !$isAdmin;

    $content = convertBlade($content, $routes, $isAdmin, $isPublic);

    // Add PHP header with includes
    $header = "<?php\n";
    if ($isAdmin && $dest !== 'admin/login.php') {
        $header .= "// Admin page - requires authentication\n";
    }
    $header .= "// Ported from: $src\n?>\n";

    // Determine which layout to include
    $layoutHeader = '';
    $layoutFooter = '';

    if ($isPublic) {
        $layoutHeader = '<?php require BASE_PATH . \'/templates/layouts/header.php\'; ?>';
        $layoutFooter = '<?php require BASE_PATH . \'/templates/layouts/footer.php\'; ?>';
    } elseif ($isAdmin && $dest !== 'admin/login.php') {
        $layoutHeader = '<?php require BASE_PATH . \'/templates/admin/header.php\'; ?>';
        $layoutFooter = '<?php require BASE_PATH . \'/templates/admin/footer.php\'; ?>';
    }

    $content = $header . $layoutHeader . "\n" . $content . "\n" . $layoutFooter;

    // Ensure directory exists
    $dir = dirname($destPath);
    if (!is_dir($dir)) mkdir($dir, 0777, true);

    file_put_contents($destPath, $content);
    echo "OK: $src -> $dest\n";
}

echo "\nConversion complete!\n";
