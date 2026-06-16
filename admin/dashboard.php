<?php
// Admin page - requires authentication
// Ported from: admin/dashboard.blade.php
$__page_title = 'Jobs';

$bookings = Database::fetchAll("SELECT b.*, v.name as vehicle_name FROM bookings b LEFT JOIN vehicles v ON b.vehicle_id = v.id ORDER BY b.created_at DESC");

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
<div x-data="jobsTable()">

    
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
        <div class="bg-white rounded-lg border px-4 py-3">
            <p class="text-xs text-gray-400 uppercase tracking-wider">Total</p>
            <p class="text-xl font-bold text-gray-900"><?= e($bookings->count()) ?></p>
        </div>
        <div class="bg-white rounded-lg border px-4 py-3">
            <p class="text-xs text-gray-400 uppercase tracking-wider">Confirmed</p>
            <p class="text-xl font-bold text-green-600"><?= e($bookings->where('status','confirmed')->count()) ?></p>
        </div>
        <div class="bg-white rounded-lg border px-4 py-3">
            <p class="text-xs text-gray-400 uppercase tracking-wider">Pending</p>
            <p class="text-xl font-bold text-amber-600"><?= e($bookings->where('status','pending')->count()) ?></p>
        </div>
        <div class="bg-white rounded-lg border px-4 py-3">
            <p class="text-xs text-gray-400 uppercase tracking-wider">Revenue</p>
            <p class="text-xl font-bold text-gray-900">&pound;<?= e(number_format($bookings->where('payment_status','paid')->sum('total_price'), 0)) ?></p>
        </div>
    </div>

    
    <div class="bg-white rounded-lg border p-4 mb-4">
        <div class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs text-gray-400 uppercase tracking-wider mb-1">Created From</label>
                <input type="date" x-model="filters.createdFrom" class="border border-gray-200 rounded-md px-2 py-1.5 text-sm w-36 focus:ring-1 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs text-gray-400 uppercase tracking-wider mb-1">Created To</label>
                <input type="date" x-model="filters.createdTo" class="border border-gray-200 rounded-md px-2 py-1.5 text-sm w-36 focus:ring-1 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs text-gray-400 uppercase tracking-wider mb-1">Pickup From</label>
                <input type="date" x-model="filters.pickupFrom" class="border border-gray-200 rounded-md px-2 py-1.5 text-sm w-36 focus:ring-1 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs text-gray-400 uppercase tracking-wider mb-1">Pickup To</label>
                <input type="date" x-model="filters.pickupTo" class="border border-gray-200 rounded-md px-2 py-1.5 text-sm w-36 focus:ring-1 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs text-gray-400 uppercase tracking-wider mb-1">Car Type</label>
                <select x-model="filters.carType" class="border border-gray-200 rounded-md px-2 py-1.5 text-sm w-32 focus:ring-1 focus:ring-blue-500">
                    <option value="">All</option>
                    <option>Saloon</option><option>Executive</option><option>Estate</option>
                    <option>People Carrier</option><option>Executive People Carrier</option>
                    <option>Minibus</option><option>16Pax</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-400 uppercase tracking-wider mb-1">Status</label>
                <select x-model="filters.status" class="border border-gray-200 rounded-md px-2 py-1.5 text-sm w-28 focus:ring-1 focus:ring-blue-500">
                    <option value="">All</option>
                    <option value="new">New</option><option value="confirmed">Confirmed</option>
                    <option value="pending">Pending</option><option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option><option value="assigned">Assigned</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-400 uppercase tracking-wider mb-1">Search</label>
                <input type="text" x-model="filters.search" placeholder="Ref, name, email..." class="border border-gray-200 rounded-md px-2 py-1.5 text-sm w-40 focus:ring-1 focus:ring-blue-500">
            </div>
            <button @click="resetFilters()" class="text-sm text-gray-400 hover:text-gray-600 pb-1">Clear</button>
        </div>
        <p class="text-xs text-gray-400 mt-2"><span x-text="filteredBookings.length"></span> bookings found</p>
    </div>

    
    <div class="bg-white rounded-lg border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="px-5 py-4 text-left">Created</th>
                        <th class="px-5 py-4 text-left">ID</th>
                        <th class="px-5 py-4 text-left">Name</th>
                        <th class="px-5 py-4 text-left">Pickup</th>
                        <th class="px-5 py-4 text-left">Route</th>
                        <th class="px-5 py-4 text-left">Car</th>
                        <th class="px-5 py-4 text-left">Price</th>
                        <th class="px-5 py-4 text-left">Status</th>
                        <th class="px-5 py-4 text-left">Payment</th>
                        <th class="px-5 py-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <template x-for="b in filteredBookings" :key="b.id">
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-5 py-4 text-gray-500 whitespace-nowrap" x-text="formatDate(b.created_at)"></td>
                            <td class="px-5 py-4 font-mono font-semibold text-gray-900" x-text="b.reference"></td>
                            <td class="px-5 py-4 text-gray-700" x-text="b.passenger_name || '—'"></td>
                            <td class="px-5 py-4 text-gray-500 whitespace-nowrap" x-text="b.depart_date ? b.depart_date + (b.depart_time ? ' ' + b.depart_time : '') : '—'"></td>
                            <td class="px-5 py-4 text-gray-500 max-w-[200px] truncate" x-text="(b.from_location || '') + ' → ' + (b.to_location || '')"></td>
                            <td class="px-5 py-4 text-gray-500 uppercase text-xs" x-text="b.vehicle_name || '—'"></td>
                            <td class="px-5 py-4 font-semibold text-gray-900" x-text="'£' + parseFloat(b.total_price || 0).toFixed(2)"></td>
                            <td class="px-5 py-4">
                                <span class="px-1.5 py-0.5 rounded text-xs font-semibold"
                                      :class="{
                                          'bg-blue-100 text-blue-700': b.status === 'new',
                                          'bg-green-100 text-green-700': b.status === 'confirmed' || b.status === 'completed',
                                          'bg-amber-100 text-amber-700': b.status === 'pending' || b.status === 'bidding',
                                          'bg-red-100 text-red-700': b.status === 'cancelled',
                                          'bg-sky-100 text-sky-700': b.status === 'assigned',
                                      }"
                                      x-text="(b.status || 'new').charAt(0).toUpperCase() + (b.status || 'new').slice(1)"></span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-1.5 py-0.5 rounded text-xs font-semibold"
                                      :class="b.payment_status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700'"
                                      x-text="(b.payment_status || 'unpaid').charAt(0).toUpperCase() + (b.payment_status || 'unpaid').slice(1)"></span>
                            </td>
                            <td class="px-5 py-4">
                                <a :href="'/admin/bookings/' + b.id" class="bg-blue-600 hover:bg-blue-700 text-white px-2.5 py-1 rounded text-xs font-medium transition">Edit</a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        <div x-show="filteredBookings.length === 0" class="text-center py-8 text-sm text-gray-400">No bookings found</div>
    </div>
</div>

<script>
function jobsTable() {
    return {
        allBookings: <?= json_encode($bookingsJson) ?>,
        filters: { createdFrom:'', createdTo:'', pickupFrom:'', pickupTo:'', carType:'', status:'', search:'' },
        resetFilters() { this.filters = { createdFrom:'',createdTo:'',pickupFrom:'',pickupTo:'',carType:'',status:'',search:'' }; },
        formatDate(iso) {
            if (!iso) return '—';
            const d = new Date(iso);
            return d.toLocaleDateString('en-GB', { day:'2-digit', month:'short', year:'numeric' }) + ' ' + d.toLocaleTimeString('en-GB', { hour:'2-digit', minute:'2-digit' });
        },
        get filteredBookings() {
            return this.allBookings.filter(b => {
                if (this.filters.createdFrom && new Date(b.created_at) < new Date(this.filters.createdFrom)) return false;
                if (this.filters.createdTo && new Date(b.created_at) > new Date(this.filters.createdTo + 'T23:59:59')) return false;
                if (this.filters.pickupFrom && b.depart_date && b.depart_date < this.filters.pickupFrom) return false;
                if (this.filters.pickupTo && b.depart_date && b.depart_date > this.filters.pickupTo) return false;
                if (this.filters.carType && b.vehicle_name !== this.filters.carType) return false;
                if (this.filters.status && b.status !== this.filters.status) return false;
                if (this.filters.search) {
                    const q = this.filters.search.toLowerCase();
                    if (!(b.reference||'').toLowerCase().includes(q) && !(b.passenger_name||'').toLowerCase().includes(q) && !(b.email||'').toLowerCase().includes(q)) return false;
                }
                return true;
            });
        }
    }
}
</script>

<?php require BASE_PATH . '/templates/admin/footer.php'; ?>