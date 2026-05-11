<header class="mb-6">
    <h1 style="font-family:var(--font-display);font-size:1.5rem;font-weight:800;color:var(--on-surface);">Stok Rendah</h1>
    <p style="font-size:0.875rem;color:var(--secondary);font-weight:500;text-transform:uppercase;letter-spacing:0.05em;margin-top:4px;">(Berdasarkan Supplier)</p>
</header>

<section class="stock-dropdown-wrapper mb-8">
    <label for="stok-kategori" style="display:block;font-size:0.6875rem;font-weight:700;color:var(--secondary);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:8px;padding-left:4px;">Kategori</label>
    <div class="stock-select-wrapper">
        <select id="stok-kategori" class="stock-select">
            <option value="">Belum dipilih</option>
        </select>
        <svg class="select-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
    </div>
</section>

<div id="stok-list">
    <div class="stock-empty">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:48px;height:48px;color:var(--outline-variant);margin-bottom:1rem;"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><path d="M3.27 6.96L12 12.01l8.73-5.05M12 22.08V12"/></svg>
        <p>Pilih kategori terlebih dahulu</p>
    </div>
</div>

<div id="stok-actions" class="stock-actions hidden">
    <button class="btn btn-whatsapp" onclick="IM.sendStokWhatsApp()">
        <svg viewBox="0 0 24 24" fill="currentColor" style="width:24px;height:24px"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.246 2.248 3.484 5.232 3.483 8.413-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.308 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.319 1.592 5.448 0 9.886-4.438 9.889-9.886.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.438-9.889 9.886-.001 2.225.613 3.913 1.592 5.513l-.999 3.651 3.769-.988zm11.387-4.464c-.301-.15-1.781-.879-2.056-.979-.275-.101-.475-.15-.675.15-.199.299-.775.979-.95 1.174-.175.195-.35.219-.65.069-.3-.15-1.265-.465-2.403-1.485-.885-.79-1.481-1.768-1.656-2.068-.175-.3-.019-.463.13-.612.134-.133.301-.35.451-.525.15-.175.199-.299.299-.499.1-.2.05-.374-.025-.524-.075-.15-.675-1.625-.925-2.225-.244-.583-.491-.503-.675-.512-.174-.008-.375-.01-.575-.01-.2 0-.525.075-.8.375-.275.3-1.05 1.025-1.05 2.5 0 1.475 1.075 2.9 1.225 3.1.15.2 2.115 3.23 5.124 4.535.715.311 1.273.497 1.71.636.72.228 1.373.196 1.891.118.577-.087 1.781-.727 2.031-1.427.25-.7.25-1.3.175-1.425-.075-.125-.275-.2-.575-.35z"/></svg>
        Kirim ke WhatsApp
    </button>
    <button class="btn btn-copy" onclick="IM.copyStokList()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:20px;height:20px"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
        Copy List
    </button>
</div>