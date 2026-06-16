<?php
// Admin page - requires authentication
// Ported from: admin/zones-map.blade.php
$__page_title = 'Zones Map';
?>
<?php require BASE_PATH . '/templates/admin/header.php'; ?>
<?php ob_start(); ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
<div x-data="zonesMap()" x-init="initMap()" class="flex flex-col" style="height: calc(100vh - 120px);">
    <!-- Controls -->
    <div class="flex items-center justify-between mb-3">
        <h2 class="text-lg font-semibold text-gray-800">Zones Map</h2>
        <div class="flex items-center gap-3">
            <a href="#" x-show="isDrawing" x-cloak @click.prevent="finishDrawing()" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                <i class="fas fa-check mr-1"></i> Finish Drawing
            </a>
            <button @click="startDrawing()" :disabled="isDrawing"
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fas fa-draw-polygon mr-2"></i> Draw Zone
            </button>
        </div>
    </div>

    <!-- Map -->
    <div id="zones-map" class="flex-1 rounded-lg border border-gray-200 shadow-sm z-0"></div>
</div>

<script>
function zonesMap() {
    return {
        map: null,
        drawnItems: null,
        drawControl: null,
        currentDrawHandler: null,
        isDrawing: false,

        initMap() {
            this.map = L.map('zones-map').setView([51.5074, -0.1278], 10);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(this.map);

            this.drawnItems = new L.FeatureGroup();
            this.map.addLayer(this.drawnItems);

            this.map.on(L.Draw.Event.CREATED, (event) => {
                let layer = event.layer;
                layer.setStyle({
                    color: '#f59e0b',
                    fillColor: '#f59e0b',
                    fillOpacity: 0.3,
                    weight: 2
                });
                this.drawnItems.addLayer(layer);
                this.isDrawing = false;
            });

            // Fix map rendering after container is visible
            setTimeout(() => { this.map.invalidateSize(); }, 200);
        },

        startDrawing() {
            this.isDrawing = true;
            this.currentDrawHandler = new L.Draw.Polygon(this.map, {
                shapeOptions: {
                    color: '#f59e0b',
                    fillColor: '#f59e0b',
                    fillOpacity: 0.3,
                    weight: 2
                }
            });
            this.currentDrawHandler.enable();
        },

        finishDrawing() {
            if (this.currentDrawHandler) {
                this.currentDrawHandler.completeShape();
            }
            this.isDrawing = false;
        }
    };
}
</script>

<?php require BASE_PATH . '/templates/admin/footer.php'; ?>