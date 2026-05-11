<section class="mb-8">
    <div class="section-header">
        <div>
            <span class="section-label">Cek Transfer</span>
            <h2>Transaksi Transfer</h2>
        </div>
        <div class="date-selector" id="transfer-date-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <span id="transfer-date-display">Pilih tanggal</span>
            <input type="date" id="transfer-date" class="date-input">
        </div>
    </div>
</section>

<section class="transfer-summary" id="transfer-summary">
    <div class="transfer-summary-card">
        <label class="transfer-summary-label">Total Transfer</label>
        <div class="transfer-summary-value" id="transfer-total-value">Rp 0</div>
        <svg class="transfer-summary-icon" viewBox="0 0 24 24" fill="var(--on-primary)"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.246 2.248 3.484 5.232 3.483 8.413-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.308 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.319 1.592 5.448 0 9.886-4.438 9.889-9.886.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.438-9.889 9.886-.001 2.225.613 3.913 1.592 5.513l-.999 3.651 3.769-.988z"/></svg>
    </div>
</section>

<section class="mb-8">
    <div id="transfer-list">
        <div class="stock-empty">
            <p>Memuat data transfer...</p>
        </div>
    </div>
</section>