<div class="nota-header">
    <span class="nota-label">Nota Penjualan</span>
    <div style="display:flex;justify-content:space-between;align-items:baseline;">
        <h2 class="nota-title">IkhwanMart</h2>
        <div style="text-align:right;">
            <p style="font-size:0.625rem;font-weight:700;letter-spacing:0.05em;text-transform:uppercase;color:var(--outline);">No. Nota</p>
            <p id="nota-sale-id" style="font-family:var(--font-display);font-size:1.125rem;font-weight:700;color:var(--on-surface);">#---</p>
        </div>
    </div>
</div>

<div class="nota-card" id="nota-content">
    <div class="nota-body">
        <div class="nota-meta">
            <div>
                <span class="meta-label">Pelanggan</span>
                <p class="meta-value" id="nota-customer">-</p>
            </div>
            <div>
                <span class="meta-label">Waktu</span>
                <p class="meta-value right" id="nota-time">-</p>
            </div>
            <div>
                <span class="meta-label">Kasir</span>
                <p class="meta-value" id="nota-kasir">-</p>
            </div>
        </div>

        <div class="nota-divider"></div>

        <div>
            <span class="meta-label" style="display:block;margin-bottom:1rem;">Item Penjualan</span>
            <div class="nota-items" id="nota-items">
                <div class="nota-item-row">
                    <div><p class="nota-item-name">Memuat...</p></div>
                </div>
            </div>
        </div>

        <div class="nota-total-section">
            <div class="nota-total-row sub">
                <span>Subtotal</span>
                <span id="nota-subtotal">Rp 0</span>
            </div>
            <div class="nota-total-row grand">
                <span class="label">TOTAL AKHIR</span>
                <span class="amount" id="nota-total">Rp 0</span>
            </div>
        </div>

        <div class="nota-divider"></div>

        <div>
            <span class="meta-label" style="display:block;margin-bottom:0.5rem;">Pembayaran</span>
            <div id="nota-payments"></div>
        </div>

        <div id="nota-rekening"></div>
    </div>
</div>

<div class="nota-actions">
    <button class="btn btn-whatsapp" onclick="IM.sendWhatsApp()">
        <svg viewBox="0 0 24 24" fill="currentColor" style="width:24px;height:24px"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.246 2.248 3.484 5.232 3.483 8.413-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.308 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.319 1.592 5.448 0 9.886-4.438 9.889-9.886.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.438-9.889 9.886-.001 2.225.613 3.913 1.592 5.513l-.999 3.651 3.769-.988zm11.387-4.464c-.301-.15-1.781-.879-2.056-.979-.275-.101-.475-.15-.675.15-.199.299-.775.979-.95 1.174-.175.195-.35.219-.65.069-.3-.15-1.265-.465-2.403-1.485-.885-.79-1.481-1.768-1.656-2.068-.175-.3-.019-.463.13-.612.134-.133.301-.35.451-.525.15-.175.199-.299.299-.499.1-.2.05-.374-.025-.524-.075-.15-.675-1.625-.925-2.225-.244-.583-.491-.503-.675-.512-.174-.008-.375-.01-.575-.01-.2 0-.525.075-.8.375-.275.3-1.05 1.025-1.05 2.5 0 1.475 1.075 2.9 1.225 3.1.15.2 2.115 3.23 5.124 4.535.715.311 1.273.497 1.71.636.72.228 1.373.196 1.891.118.577-.087 1.781-.727 2.031-1.427.25-.7.25-1.3.175-1.425-.075-.125-.275-.2-.575-.35z"/></svg>
        Kirim ke WhatsApp
    </button>
    <button class="btn btn-copy" onclick="IM.copyNota()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:20px;height:20px"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
        Copy Nota
    </button>
</div>

<div class="nota-footer">
    <p>Terima kasih telah berbelanja di IkhwanMart.<br>Simpan nota ini sebagai bukti pembayaran yang sah.</p>
</div>