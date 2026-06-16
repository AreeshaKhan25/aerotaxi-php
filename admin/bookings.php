<?php
// Admin page - requires authentication
// Ported from: admin/bookings.blade.php
$__page_title = 'Unpaid Bookings';

$bookings = Database::fetchAll("SELECT b.*, v.name as vehicle_name FROM bookings b LEFT JOIN vehicles v ON b.vehicle_id = v.id WHERE b.payment_status != 'paid' OR b.payment_status IS NULL ORDER BY b.created_at DESC");

// Format bookings for JSON/AlpineJS consumption
$bookingsJson = [];
foreach ($bookings as $b) {
    $bookingsJson[] = [
        'id' => (int)$b->id,
        'reference' => $b->reference,
        'passenger_name' => $b->passenger_name,
        'from_location' => $b->from_location,
        'to_location' => $b->to_location,
        'depart_date' => $b->depart_date,
        'depart_time' => $b->depart_time,
        'status' => $b->status ?? 'new',
        'payment_status' => $b->payment_status ?? 'unpaid',
        'vehicle_name' => $b->vehicle_name ?? 'N/A',
        'total_price' => (float)$b->total_price,
        'created_at' => $b->created_at,
    ];
}
?>
<?php require BASE_PATH . '/templates/admin/header.php'; ?>
<div x-data="unpaidTable()">

    <div class="flex items-center justify-between mb-5">
        <h1 class="text-lg font-bold text-gray-900">Unpaid Bookings <span class="text-gray-400 font-normal text-sm">(<?= e($bookings->count()) ?>)</span></h1>
    </div>

    
    <div class="bg-white rounded-lg border p-4 mb-4">
        <div class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-[10px] text-gray-400 uppercase tracking-wider mb-1">Search</label>
                <input type="text" x-model="search" placeholder="Ref, name, email..." class="border border-gray-200 rounded-md px-2 py-1.5 text-sm w-48 focus:ring-1 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-[10px] text-gray-400 uppercase tracking-wider mb-1">Car Type</label>
                <select x-model="carType" class="border border-gray-200 rounded-md px-2 py-1.5 text-sm w-32 focus:ring-1 focus:ring-blue-500">
                    <option value="">All</option>
                    <option>Saloon</option><option>Executive</option><option>Estate</option>
                    <option>People Carrier</option><option>Minibus</option><option>16Pax</option>
                </select>
            </div>
        </div>
    </div>

    
    <div class="bg-white rounded-lg border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="px-5 py-4 text-left">Created</th>
                        <th class="px-5 py-4 text-left">ID</th>
                        <th class="px-5 py-4 text-left">Name</th>
                        <th class="px-5 py-4 text-left">Pickup</th>
                        <th class="px-5 py-4 text-left">Route</th>
                        <th class="px-5 py-4 text-left">Car</th>
                        <th class="px-5 py-4 text-left">Price</th>
                        <th class="px-5 py-4 text-left">Status</th>
                        <th class="px-5 py-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <template x-for="b in filtered" :key="b.id">
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-5 py-4 text-gray-500 whitespace-nowrap" x-text="new Date(b.created_at).toLocaleDateString('en-GB',{day:'2-digit',month:'short',year:'numeric'})"></td>
                            <td class="px-5 py-4 font-mono font-semibold text-gray-900" x-text="b.reference"></td>
                            <td class="px-5 py-4 text-gray-700" x-text="b.passenger_name || '—'"></td>
                            <td class="px-5 py-4 text-gray-500 whitespace-nowrap" x-text="b.depart_date || '—'"></td>
                            <td class="px-5 py-4 text-gray-500 max-w-[200px] truncate" x-text="(b.from_location||'') + ' → ' + (b.to_location||'')"></td>
                            <td class="px-5 py-4 text-gray-500 uppercase text-[10px]" x-text="b.vehicle_name || '—'"></td>
                            <td class="px-5 py-4 font-semibold" x-text="'£' + parseFloat(b.total_price||0).toFixed(2)"></td>
                            <td class="px-5 py-4"><span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-100 text-amber-700" x-text="(b.status||'new').charAt(0).toUpperCase()+(b.status||'new').slice(1)"></span></td>
                            <td class="px-5 py-4">
                                <a :href="'/admin/bookings/' + b.id" class="bg-blue-600 hover:bg-blue-700 text-white px-2.5 py-1 rounded text-[10px] font-medium transition">Edit</a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        <div x-show="filtered.length === 0" class="text-center py-8 text-sm text-gray-400">No unpaid bookings</div>
    </div>
</div>

<script>
function unpaidTable() {
    return {
        all: <?= json_encode($bookingsJson) ?>,
        search: '',
        carType: '',
        get filtered() {
            return this.all.filter(b => {
                if (this.carType && b.vehicle_name !== this.carType) return false;
                if (this.search) {
                    const q = this.search.toLowerCase();
                    if (!(b.reference||'').toLowerCase().includes(q) && !(b.passenger_name||'').toLowerCase().includes(q)) return false;
                }
                return true;
            });
        }
    }
}
</script>

<?php require BASE_PATH . '/templates/admin/footer.php'; ?>