<section class="mb-8">
    <div class="section-header">
        <div>
            <span class="section-label">Transaction Report</span>
            <h2>Daily Overview</h2>
        </div>
        <div class="date-selector" id="dash-date-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <span id="dash-date-display">Pilih tanggal</span>
            <input type="date" id="dash-date" class="date-input">
        </div>
    </div>

    <div class="metrics-grid">
        <div class="metric-card">
            <span class="metric-label">Jumlah Transaksi</span>
            <div>
                <span class="metric-value" id="dash-jumlah">0</span>
            </div>
        </div>
        <div class="metric-card">
            <span class="metric-label">Nilai Transaksi</span>
            <div>
                <span class="metric-value" id="dash-nilai">Rp 0</span>
            </div>
        </div>
    </div>
</section>

<section class="mb-8">
    <div class="chart-card">
        <div class="chart-header">
            <h3 class="chart-title">Hourly Sales Flow</h3>
        </div>
        <div class="chart-container">
            <canvas id="dash-chart"></canvas>
        </div>
    </div>
</section>

<section class="mb-8">
    <div class="data-table-wrapper">
        <div class="data-table-header">
            <h3>Payment Breakdown</h3>
        </div>
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Jam</th>
                        <th>Tunai</th>
                        <th class="text-center">Debit</th>
                        <th class="text-center">Transfer</th>
                        <th class="text-right">QRIS</th>
                    </tr>
                </thead>
                <tbody id="dash-tbody">
                    <tr><td colspan="5" class="text-center">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</section>