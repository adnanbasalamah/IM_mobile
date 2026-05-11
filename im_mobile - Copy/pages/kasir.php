<div class="kasir-info-card">
    <div class="kasir-info-grid">
        <div>
            <span class="info-label" style="font-size:0.75rem;font-weight:600;letter-spacing:0.05em;text-transform:uppercase;color:var(--on-surface-variant);display:block;margin-bottom:4px;">Tanggal & Jam</span>
            <div class="info-value" style="display:flex;align-items:center;gap:8px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:20px;height:20px;color:var(--outline);"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <input type="date" id="kasir-date" class="kasir-select" style="width:auto;">
                <input type="time" id="kasir-time" class="kasir-select" style="width:auto;">
            </div>
        </div>
        <div>
            <span class="info-label" style="font-size:0.75rem;font-weight:600;letter-spacing:0.05em;text-transform:uppercase;color:var(--on-surface-variant);display:block;margin-bottom:4px;">Kasir</span>
            <div class="info-value" style="display:flex;align-items:center;gap:8px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:20px;height:20px;color:var(--outline);"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <select id="kasir-employee" class="kasir-select">
                    <option value="">Pilih Kasir</option>
                </select>
            </div>
        </div>
    </div>
</div>

<form id="kasir-form">
<section class="kasir-section">
    <div class="kasir-section-header">
        <h2>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:24px;height:24px;color:var(--primary);"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            Bagian 1: Kutipan Tunai
        </h2>
        <span class="kasir-section-badge badge-besar">BESAR</span>
    </div>

    <div class="denomination-card">
        <div class="denom-top">
            <span class="denom-label">Pecahan Rp 100.000</span>
            <span class="denom-hint">Jumlah Lembar</span>
        </div>
        <input type="number" id="kasir-100k" class="denomination-input kasir-count-input" placeholder="0" min="0" value="">
    </div>

    <div class="denomination-card">
        <div class="denom-top">
            <span class="denom-label">Pecahan Rp 50.000</span>
            <span class="denom-hint">Jumlah Lembar</span>
        </div>
        <input type="number" id="kasir-50k" class="denomination-input kasir-count-input" placeholder="0" min="0" value="">
    </div>

    <div class="total-card total-kutipan">
        <span class="total-label">Total Kutipan</span>
        <span class="total-amount" id="total-kutipan">Rp 0</span>
    </div>
</section>

<section class="kasir-section">
    <div class="kasir-section-header">
        <h2>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:24px;height:24px;color:var(--primary);"><rect x="2" y="5" width="20" height="14" rx="2" ry="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
            Bagian 2: Uang Di Kasir
        </h2>
        <span class="kasir-section-badge badge-kecil">KECIL / KOIN</span>
    </div>

    <div class="coin-row">
        <span class="coin-label">Rp 20.000</span>
        <input type="number" id="kasir-20k" class="coin-input kasir-count-input" placeholder="0" min="0">
    </div>
    <div class="coin-row">
        <span class="coin-label">Rp 10.000</span>
        <input type="number" id="kasir-10k" class="coin-input kasir-count-input" placeholder="0" min="0">
    </div>
    <div class="coin-row">
        <span class="coin-label">Rp 5.000</span>
        <input type="number" id="kasir-5k" class="coin-input kasir-count-input" placeholder="0" min="0">
    </div>
    <div class="coin-row">
        <span class="coin-label">Rp 2.000</span>
        <input type="number" id="kasir-2k" class="coin-input kasir-count-input" placeholder="0" min="0">
    </div>
    <div class="coin-row">
        <span class="coin-label">Rp 1.000</span>
        <input type="number" id="kasir-1k" class="coin-input kasir-count-input" placeholder="0" min="0">
    </div>

    <div class="coin-total-input">
        <span style="font-size:1.125rem;font-weight:600;display:flex;align-items:center;gap:8px;">Total Koin</span>
        <div style="position:relative;margin-top:8px;">
            <span style="position:absolute;left:16px;top:50%;transform:translateY(-50%);font-family:var(--font-display);font-size:1.5rem;font-weight:700;color:var(--outline);">Rp</span>
            <input type="number" id="kasir-coin" class="denomination-input kasir-count-input" placeholder="0" min="0" style="padding-left:56px;">
        </div>
    </div>

    <div class="total-card total-kasir">
        <span class="total-label">Total Di Kasir</span>
        <span class="total-amount" id="total-di-kasir">Rp 0</span>
    </div>
</section>

<div class="submit-fixed">
    <button type="submit" class="btn btn-primary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:20px;height:20px"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        Submit Pencatatan Kas
    </button>
</div>
</form>