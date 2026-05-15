<section class="mb-8">
    <div class="section-header">
        <div>
            <span class="section-label">Transaction Report</span>
            <h2>Daily Overview</h2>
        </div>
        <input type="date" id="dash-date" class="date-input">
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