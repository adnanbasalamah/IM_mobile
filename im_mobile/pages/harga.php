<section class="mb-8">
    <div class="section-header">
        <div>
            <span class="section-label">Pasar Segar</span>
            <h2>Update Harga</h2>
        </div>
    </div>
</section>

<section class="mb-6">
    <div class="harga-search-box">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="harga-search-icon"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" id="harga-search" class="harga-search-input" placeholder="Cari produk Pasar Segar...">
        <button id="harga-search-clear" class="harga-search-clear" style="display:none;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px;"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>
</section>

<section class="mb-8">
    <div class="harga-results-header">
        <h3>Hasil Pencarian</h3>
        <span class="harga-count" id="harga-count">0 Produk</span>
    </div>
    <div id="harga-list" class="harga-list">
        <div class="stock-empty">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:48px;height:48px;color:var(--outline)"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <p>Ketik nama produk untuk mencari</p>
        </div>
    </div>
</section>

<!-- Edit Modal -->
<div id="harga-edit-modal" class="harga-modal" style="display:none;">
    <div class="harga-modal-overlay" id="harga-modal-overlay"></div>
    <div class="harga-modal-content">
        <div class="harga-modal-header">
            <h3 id="harga-edit-name" class="harga-edit-title">Nama Produk</h3>
            <span id="harga-edit-sku" class="harga-edit-sku"></span>
        </div>
        <div class="harga-edit-body">
            <div class="harga-edit-group">
                <label class="harga-edit-label">Harga Modal</label>
                <div class="harga-edit-input-wrap">
                    <span class="harga-input-prefix">Rp</span>
                    <input type="number" id="harga-edit-cost" class="harga-edit-input" placeholder="0">
                </div>
            </div>
            <div class="harga-edit-group">
                <label class="harga-edit-label">Harga Jual</label>
                <div class="harga-edit-input-wrap">
                    <span class="harga-input-prefix">Rp</span>
                    <input type="number" id="harga-edit-price" class="harga-edit-input" placeholder="0">
                </div>
            </div>
            <div id="harga-edit-profit" class="harga-edit-profit"></div>
        </div>
        <div class="harga-edit-actions">
            <button class="btn btn-cancel" id="harga-btn-cancel">Batal</button>
            <button class="btn btn-primary" id="harga-btn-save">Simpan</button>
        </div>
    </div>
</div>