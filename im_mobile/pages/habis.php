<section class="mb-8">
    <div class="section-header">
        <div>
            <span class="section-label">Pasar Segar</span>
            <h2>Stok Rendah</h2>
        </div>
        <div class="section-subtitle">HABISNYA BARANG</div>
    </div>
</section>

<section class="mb-6">
    <label class="section-label" for="habis-period">Sudah Habis</label>
    <div class="date-selector" id="habis-period-btn">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px"><polyline points="6 9 12 15 18 9"/></svg>
        <span id="habis-period-display">1 Minggu</span>
        <select id="habis-period" class="date-input">
            <option value="1-week">1 Minggu</option>
            <option value="2-weeks">2 Minggu</option>
            <option value="1-month">1 Bulan</option>
        </select>
    </div>
</section>

<section class="mb-8">
    <div id="habis-list">
        <div class="stock-empty">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:48px;height:48px;color:var(--outline)"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
            <p>Memuat data stok...</p>
        </div>
    </div>
    <div id="habis-actions" class="stok-actions hidden">
        <button class="btn btn-whatsapp" onclick="IM.sendHabisWhatsApp()">
            <svg viewBox="0 0 24 24" fill="currentColor" style="width:24px;height:24px"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.246 2.248 3.484 5.232 3.483 8.413-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.308 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.319 1.592 5.448 0 9.886-4.438 9.889-9.886.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.438-9.889 9.886-.001 2.225.613 3.913 1.592 5.513l-.999 3.651 3.769-.988zm11.387-4.464c-.301-.15-1.781-.879-2.056-.979-.275-.101-.475-.15-.675.15-.199.299-.775.979-.95 1.174-.175.195-.35.219-.65.069-.3-.15-1.265-.465-2.403-1.485-.885-.79-1.481-1.768-1.656-2.068-.175-.3-.019-.463.13-.612.134-.133.301-.35.451-.525.15-.175.199-.299.299-.499.1-.2.05-.374-.025-.524-.075-.15-.675-1.625-.925-2.225-.244-.583-.491-.503-.675-.512-.174-.008-.375-.01-.575-.01-.2 0-.525.075-.8.375-.275.3-1.05 1.025-1.05 2.5 0 1.475 1.075 2.9 1.225 3.1.15.2 2.115 3.23 5.124 4.535.715.311 1.273.497 1.71.636.72.228 1.373.196 1.891.118.577-.087 1.781-.727 2.031-1.427.25-.7.25-1.3.175-1.425-.075-.125-.275-.2-.575-.35z"/></svg>
            Kirim ke WhatsApp
        </button>
        <button class="btn btn-copy" onclick="IM.copyHabisList()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:20px;height:20px"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
            Copy List
        </button>
    </div>
</section>