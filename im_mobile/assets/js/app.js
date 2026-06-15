const IM = {
    page: 'dashboard',
    chart: null,

    init() {
        const hash = window.location.hash.replace('#', '') || 'dashboard';
        this.navigateTo(hash, false);
        this.bindNavEvents();
    },

    bindNavEvents() {
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                const page = item.dataset.page;
                this.navigateTo(page);
            });
        });
    },

    async navigateTo(page, updateHash = true) {
        const validPages = ['dashboard', 'transfer', 'nota', 'stok', 'omset'];
        if (!validPages.includes(page)) page = 'dashboard';

        this.page = page;
        if (updateHash) window.location.hash = page;

        this.updateNavActive(page);

        const content = document.getElementById('content');
        if (!content) return;

        content.classList.add('loading');
        try {
            const pageFile = page === 'stok' ? 'habis' : page;
            const resp = await fetch('pages/' + pageFile + '.php');
            if (resp.ok) {
                content.innerHTML = await resp.text();
                this.afterPageLoad(page);
            } else {
                content.innerHTML = '<div class="stock-empty"><p>Gagal memuat halaman</p></div>';
            }
        } catch (err) {
            content.innerHTML = '<div class="stock-empty"><p>Error: ' + err.message + '</p></div>';
        }
        content.classList.remove('loading');
        window.scrollTo(0, 0);
    },

    updateNavActive(page) {
        document.querySelectorAll('.nav-item').forEach(item => {
            item.classList.toggle('active', item.dataset.page === page);
        });
    },

    afterPageLoad(page) {
        switch (page) {
            case 'dashboard': this.initDashboard(); break;
            case 'transfer': this.initTransfer(); break;
            case 'nota': this.initNota(); break;
            case 'stok': this.initHabis(); break;
            case 'omset': this.initOmset(); break;
        }
    },

    formatDate(dateStr) {
        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const d = new Date(dateStr);
        return days[d.getDay()] + ', ' + d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
    },

    formatRupiah(num) {
        if (num >= 1000000) return 'Rp ' + (num / 1000000).toFixed(1).replace('.0', '') + 'jt';
        if (num >= 1000) return 'Rp ' + (num / 1000).toFixed(0) + 'k';
        return 'Rp ' + num.toLocaleString('id-ID');
    },

    formatRupiahFull(num) {
        return 'Rp ' + Math.round(num).toLocaleString('id-ID');
    },

    showToast(msg) {
        const old = document.querySelector('.toast');
        if (old) old.remove();
        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.textContent = msg;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    },

    // ===== DASHBOARD =====
    initDashboard() {
        const dateInput = document.getElementById('dash-date');
        if (!dateInput) return;

        const today = new Date().toISOString().split('T')[0];
        dateInput.value = today;

        dateInput.addEventListener('change', () => {
            this.loadDashboard(dateInput.value);
        });

        this.loadDashboard(today);
    },

    async loadDashboard(date) {
        try {
            const resp = await fetch('api/dashboard.php?date=' + date);
            const data = await resp.json();
            this.renderDashboard(data);
        } catch (err) {
            console.error('Dashboard load error:', err);
        }
    },

    renderDashboard(data) {
        const elJumlah = document.getElementById('dash-jumlah');
        const elNilai = document.getElementById('dash-nilai');
        if (elJumlah) elJumlah.textContent = data.total_jumlah;
        if (elNilai) elNilai.textContent = this.formatRupiah(data.total_nilai);

        this.renderChart(data.hourly);
        this.renderPaymentTable(data.hourly);
    },

    renderChart(hourly) {
        const canvas = document.getElementById('dash-chart');
        if (!canvas) return;

        if (this.chart) {
            this.chart.destroy();
            this.chart = null;
        }

        const labels = [];
        const values = [];
        for (let h = 6; h <= 21; h++) {
            labels.push(h.toString().padStart(2, '0') + ':00');
            const row = hourly.find(r => r.jam === h);
            values.push(row ? row.nilai_transaksi : 0);
        }

        const ctx = canvas.getContext('2d');
        this.chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Nilai Penjualan',
                    data: values,
                    backgroundColor: 'rgba(30, 64, 175, 0.7)',
                    borderColor: '#1e40af',
                    borderWidth: 1,
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => IM.formatRupiahFull(ctx.parsed.y)
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 9 },
                            maxRotation: 0,
                            callback: function(val, idx) {
                                return idx % 3 === 0 ? this.getLabelForValue(val) : '';
                            }
                        }
                    },
                    y: {
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: {
                            font: { size: 9 },
                            callback: (val) => IM.formatRupiah(val)
                        }
                    }
                }
            }
        });
    },

    renderPaymentTable(hourly) {
        const tbody = document.getElementById('dash-tbody');
        if (!tbody) return;

        let html = '';
        for (let h = 6; h <= 21; h++) {
            const row = hourly.find(r => r.jam === h);
            if (!row || (row.tunai === 0 && row.debit === 0 && row.transfer === 0 && row.qris === 0 && row.nilai_transaksi === 0)) {
                continue;
            }
            const tunai = row.tunai > 0 ? this.formatRupiah(row.tunai) : '\u2014';
            const debit = row.debit > 0 ? this.formatRupiah(row.debit) : '\u2014';
            const qris = row.qris > 0 ? this.formatRupiah(row.qris) : '\u2014';
            html += '<tr>';
            html += '<td class="jam">' + h.toString().padStart(2, '0') + ':00</td>';
            html += '<td>' + tunai + '</td>';
            html += '<td class="text-center">' + debit + '</td>';
            html += '<td class="text-center">' + (row.transfer > 0 ? this.formatRupiah(row.transfer) : '\u2014') + '</td>';
            html += '<td class="text-right">' + qris + '</td>';
            html += '</tr>';
        }

        if (!html) {
            html = '<tr><td colspan="5" class="text-center">Tidak ada data</td></tr>';
        }
        tbody.innerHTML = html;
    },

    // ===== TRANSFER =====
    initTransfer() {
        const dateInput = document.getElementById('transfer-date');
        if (!dateInput) return;

        const today = new Date().toISOString().split('T')[0];
        dateInput.value = today;

        dateInput.addEventListener('change', () => {
            this.loadTransfer(dateInput.value);
        });

        this.loadTransfer(today);
    },

    async loadTransfer(date) {
        try {
            const resp = await fetch('api/transfer.php?date=' + date);
            const data = await resp.json();
            this.renderTransfer(data);
        } catch (err) {
            console.error('Transfer load error:', err);
        }
    },

    renderTransfer(data) {
        const container = document.getElementById('transfer-list');
        if (!container) return;

        const summaryEl = document.getElementById('transfer-summary');
        const totalValueEl = document.getElementById('transfer-total-value');

        if (!data.transfers || data.transfers.length === 0) {
            container.innerHTML = '<div class="stock-empty"><p>Tidak ada transaksi transfer pada tanggal ini</p></div>';
            if (summaryEl) summaryEl.classList.add('hidden');
            return;
        }

        if (summaryEl) summaryEl.classList.remove('hidden');
        if (totalValueEl) totalValueEl.textContent = this.formatRupiahFull(data.total_nilai || 0);

        let html = '';
        data.transfers.forEach(t => {
            html += '<div class="transfer-item" id="transfer-' + t.sale_id + '">';
            html += '<div class="transfer-row">';
            html += '<span class="transfer-jam">' + t.jam_fmt + '</span>';
            html += '<span class="transfer-nilai">' + this.formatRupiahFull(t.nilai) + '</span>';
            html += '</div>';
            html += '<div class="transfer-kasir">Kasir: ' + this.escapeHtml(t.kasir) + '</div>';
            html += '<div class="transfer-bottom">';
            html += '<a href="#nota" class="transfer-pelanggan" data-sale-id="' + t.sale_id + '" onclick="IM.openNota(' + t.sale_id + '); return false;">' + this.escapeHtml(t.pelanggan) + '</a>';
            if (this.isAdmin) {
                html += '<button class="transfer-dismiss" onclick="IM.dismissTransfer(' + t.sale_id + ')" title="Hapus dari daftar">';
                html += '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>';
                html += '</button>';
            }
            html += '</div>';
            html += '</div>';
        });

        container.innerHTML = html;
    },

    escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    },

    openNota(saleId) {
        window._pendingNotaId = saleId;
        this.navigateTo('nota');
    },

    async dismissTransfer(saleId) {
        try {
            const resp = await fetch('api/transfer_dismiss.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ sale_id: saleId })
            });
            const data = await resp.json();
            if (data.success) {
                const el = document.getElementById('transfer-' + saleId);
                if (el) el.remove();
                const dateInput = document.getElementById('transfer-date');
                if (dateInput && dateInput.value) {
                    this.loadTransfer(dateInput.value);
                }
            } else {
                this.showToast('Gagal menghapus transaksi');
            }
        } catch (err) {
            this.showToast('Error: ' + err.message);
        }
    },

    // ===== NOTA =====
    initNota() {
        const saleId = window._pendingNotaId || new URLSearchParams(window.location.search).get('sale_id');
        if (saleId) {
            this.loadNota(parseInt(saleId));
            window._pendingNotaId = null;
        }
    },

    async loadNota(saleId) {
        try {
            const resp = await fetch('api/nota.php?id=' + saleId);
            const data = await resp.json();
            this.renderNota(data);
        } catch (err) {
            console.error('Nota load error:', err);
        }
    },

    renderNota(data) {
        const el = document.getElementById('nota-content');
        if (!el) return;

        const d = new Date(data.sale_time);
        const pad = n => n.toString().padStart(2, '0');
        const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        const timeStr = pad(d.getDate()) + '/' + pad(d.getMonth()+1) + '/' + d.getFullYear() + ' ' + pad(d.getHours()) + ':' + pad(d.getMinutes());

        let itemsHtml = '';
        data.items.forEach(item => {
            const unit = item.pack_name && item.pack_name !== 'Each' ? '/' + this.escapeHtml(item.pack_name) : '';
            itemsHtml += '<div class="nota-item-row">';
            itemsHtml += '<div>';
            itemsHtml += '<p class="nota-item-name">' + this.escapeHtml(item.name) + unit + '</p>';
            itemsHtml += '<p class="nota-item-detail">' + item.qty + ' x ' + this.formatRupiahFull(item.unit_price) + '</p>';
            itemsHtml += '</div>';
            itemsHtml += '<p class="nota-item-price">' + this.formatRupiahFull(item.line_total) + '</p>';
            itemsHtml += '</div>';
        });

        let paymentsHtml = '';
        data.payments.forEach(p => {
            paymentsHtml += '<div class="nota-payment-row">';
            paymentsHtml += '<span>' + this.escapeHtml(p.type) + '</span>';
            paymentsHtml += '<span>' + this.formatRupiahFull(p.amount) + '</span>';
            paymentsHtml += '</div>';
        });

        let rekeningHtml = '';
        if (data.is_transfer) {
            rekeningHtml = '<div class="nota-rekening">';
            rekeningHtml += '<span class="rekening-bank">' + data.rekening + '</span>';
            rekeningHtml += '<span class="rekening-an">a.n ' + data.rekening_an + '</span>';
            rekeningHtml += '</div>';
        }

        const elSaleId = document.getElementById('nota-sale-id');
        const elTime = document.getElementById('nota-time');
        const elCustomer = document.getElementById('nota-customer');
        const elKasir = document.getElementById('nota-kasir');
        const elItems = document.getElementById('nota-items');
        const elSubtotal = document.getElementById('nota-subtotal');
        const elTotal = document.getElementById('nota-total');
        const elPayments = document.getElementById('nota-payments');
        const elRekening = document.getElementById('nota-rekening');

        if (elSaleId) elSaleId.textContent = '#' + data.sale_id;
        if (elTime) elTime.textContent = timeStr;
        if (elCustomer) elCustomer.textContent = data.pelanggan || '-';
        if (elKasir) elKasir.textContent = (data.kasir || '-').toUpperCase();
        if (elItems) elItems.innerHTML = itemsHtml;
        if (elSubtotal) elSubtotal.textContent = this.formatRupiahFull(data.subtotal);
        if (elTotal) elTotal.textContent = this.formatRupiahFull(data.total);
        if (elPayments) elPayments.innerHTML = paymentsHtml;
        if (elRekening) elRekening.innerHTML = rekeningHtml;

        window._notaData = data;
    },

    formatNotaText(data, isWA) {
        const d = new Date(data.sale_time);
        const pad = n => n.toString().padStart(2, '0');
        const months = ['01','02','03','04','05','06','07','08','09','10','11','12'];
        const dateStr = pad(d.getDate()) + '/' + months[d.getMonth()] + '/' + d.getFullYear();
        const timeStr = pad(d.getHours()) + ':' + pad(d.getMinutes());
        const bold = isWA ? '*' : '';
        const line = isWA ? '━' : '─';
        const thinLine = isWA ? '─' : '-';

        let text = '';
        text += '🧾 ' + bold + 'NOTA PENJUALAN' + bold + '\n';
        text += line.repeat(30) + '\n';
        text += bold + 'Nota #' + data.sale_id + bold + '\n';
        text += '📅 Tanggal: ' + dateStr + ' ' + timeStr + '\n';
        text += '👤 Kasir: ' + data.kasir.toUpperCase() + '\n';
        text += '🛒 Pelanggan: ' + data.pelanggan.toUpperCase() + '\n\n';

        text += bold + 'ITEM PENJUALAN:' + bold + '\n';
        text += thinLine.repeat(16) + '\n';

        data.items.forEach(item => {
            const unit = item.pack_name && item.pack_name !== 'Each' ? '/' + item.pack_name : '';
            text += '📦 ' + item.name + unit + '\n';
            const qtyStr = Number.isInteger(item.qty) ? item.qty : item.qty;
            text += '   ' + qtyStr + ' x ' + this.formatRupiahFull(item.unit_price) + '\n';
            text += '   → ' + this.formatRupiahFull(item.line_total) + '\n';
        });

        text += thinLine.repeat(16) + '\n';
        text += bold + 'Subtotal: ' + this.formatRupiahFull(data.subtotal) + bold + '\n';
        text += bold + 'TOTAL: ' + this.formatRupiahFull(data.total) + bold + '\n\n';

        text += '💳 ' + bold + 'Pembayaran:' + bold + '\n';
        data.payments.forEach(p => {
            const label = p.type;
            text += '   ' + label + ': ' + this.formatRupiahFull(p.amount) + '\n';
        });

        if (data.is_transfer) {
            text += '\n ' + data.rekening + ' \n';
            text += ' a.n ' + data.rekening_an + ' \n';
        }

        text += line.repeat(30) + '\n';
        text += 'Terima kasih 🙏';

        return text;
    },

    sendWhatsApp() {
        if (!window._notaData) return;
        const text = this.formatNotaText(window._notaData, true);
        const url = 'https://wa.me/?text=' + encodeURIComponent(text);
        window.open(url, '_blank');
    },

    copyNota() {
        if (!window._notaData) return;
        const text = this.formatNotaText(window._notaData, true);

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(() => {
                this.showToast('Nota berhasil disalin!');
            }).catch(() => {
                this.copyFallback(text);
            });
        } else {
            this.copyFallback(text);
        }
    },

    copyFallback(text) {
        const ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.left = '-9999px';
        ta.style.top = '-9999px';
        ta.style.opacity = '0';
        document.body.appendChild(ta);
        ta.focus();
        ta.select();
        try {
            const ok = document.execCommand('copy');
            document.body.removeChild(ta);
            if (ok) {
                this.showToast('Nota berhasil disalin!');
            } else {
                this.showToast('Gagal menyalin nota');
            }
        } catch (e) {
            document.body.removeChild(ta);
            this.showToast('Gagal menyalin nota');
        }
    },

    // ===== STOK HABIS =====
    initHabis() {
        const select = document.getElementById('habis-period');
        if (!select) return;
        select.addEventListener('change', () => {
            if (select.value === 'none') {
                const container = document.getElementById('habis-list');
                const actions = document.getElementById('habis-actions');
                if (container) container.innerHTML = '<div class="stock-empty"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:48px;height:48px;color:var(--outline)"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg><p>Pilih periode terlebih dahulu</p></div>';
                if (actions) actions.classList.add('hidden');
            } else {
                this.loadHabis(select.value);
            }
        });
    },

    async loadHabis(period) {
        const container = document.getElementById('habis-list');
        if (!container) return;
        container.innerHTML = '<div class="stock-empty"><p>Memuat data stok...</p></div>';

        try {
            const resp = await fetch('api/stok_habis.php?period=' + encodeURIComponent(period));
            const data = await resp.json();
            this.renderHabis(data);
        } catch (err) {
            container.innerHTML = '<div class="stock-empty"><p>Gagal memuat data</p></div>';
        }
    },

    renderHabis(data) {
        const container = document.getElementById('habis-list');
        const actions = document.getElementById('habis-actions');
        if (!container) return;

        if (!data.items || data.items.length === 0) {
            container.innerHTML = '<div class="stock-empty"><p>Tidak ada barang yang habis dalam periode ini</p></div>';
            if (actions) actions.classList.add('hidden');
            return;
        }

        if (actions) actions.classList.remove('hidden');

        let html = '';
        data.items.forEach(group => {
            html += '<div class="supplier-section">';
            html += '<div class="supplier-header">';
            html += '<div class="bar"></div>';
            html += '<h2>' + this.escapeHtml(group.supplier) + '</h2>';
            html += '</div>';
            group.items.forEach(item => {
                html += '<div class="stock-card">';
                html += '<div class="stock-info">';
                html += '<h3>' + this.escapeHtml(item.name) + '</h3>';
                html += '</div>';
                html += '<div class="stock-count">';
                html += '<span class="count">' + item.quantity + '</span>';
                html += '<p class="label">' + this.escapeHtml(item.pack_name || 'PCS') + '</p>';
                html += '</div>';
                html += '</div>';
            });
            html += '</div>';
        });

        container.innerHTML = html;
        window._habisData = data;
    },

    sendHabisWhatsApp() {
        if (!window._habisData) return;
        let text = '*Stok Rendah IkhwanMart (Habisnya Barang)*\n\n';
        window._habisData.items.forEach(group => {
            text += '*' + group.supplier + '*\n';
            group.items.forEach(item => {
                text += '\u2022 ' + item.name + ': ' + item.quantity + ' ' + (item.pack_name || 'pcs') + ' (min: ' + item.reorder_level + ')\n';
            });
            text += '\n';
        });
        const url = 'https://wa.me/?text=' + encodeURIComponent(text);
        window.open(url, '_blank');
    },

    copyHabisList() {
        if (!window._habisData) return;
        let text = 'Stok Rendah IkhwanMart (Habisnya Barang)\n\n';
        window._habisData.items.forEach(group => {
            text += '*' + group.supplier + '*\n';
            group.items.forEach(item => {
                text += '- ' + item.name + ': ' + item.quantity + ' ' + (item.pack_name || 'pcs') + ' (min: ' + item.reorder_level + ')\n';
            });
            text += '\n';
        });
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(() => {
                this.showToast('List stok berhasil disalin!');
            }).catch(() => {
                this.copyFallback(text);
                this.showToast('List stok berhasil disalin!');
            });
        } else {
            this.copyFallback(text);
        }
    },

    // ===== OMSET =====
    initOmset() {
        this.loadOmset();
    },

    async loadOmset() {
        const container = document.getElementById('omset-comparison');
        if (!container) return;

        try {
            const resp = await fetch('api/omset.php');
            const data = await resp.json();
            this.renderOmset(data);
        } catch (err) {
            container.innerHTML = '<div class="stock-empty"><p>Gagal memuat data</p></div>';
        }
    },

    renderOmset(data) {
        const container = document.getElementById('omset-comparison');
        const elAvgThis = document.getElementById('omset-avg-this');
        const elAvgLast = document.getElementById('omset-avg-last');
        const elSubThis = document.getElementById('omset-sub-this');
        const elSubLast = document.getElementById('omset-sub-last');
        if (!container) return;

        if (elAvgThis) elAvgThis.textContent = this.formatRupiahFull(data.avg_this_month);
        if (elAvgLast) elAvgLast.textContent = this.formatRupiahFull(data.avg_last_month);
        if (elSubThis) elSubThis.textContent = '(' + data.days_this_month + ' hari, total ' + this.formatRupiahFull(data.total_this_month) + ')';
        if (elSubLast) elSubLast.textContent = '(' + data.days_last_month + ' hari, total ' + this.formatRupiahFull(data.total_last_month) + ')';

        const pct = data.change_percent;
        const isUp = pct >= 0;
        const arrow = isUp ? '&#9650;' : '&#9660;';
        const color = isUp ? '#059669' : '#ba1a1a';
        const label = isUp ? 'Naik' : 'Turun';

        let html = '';
        html += '<div class="omset-card" style="border-color:' + color + '">';
        html += '<div class="omset-card-header">';
        html += '<span class="omset-card-label">Perbandingan Rata-rata Harian</span>';
        html += '<span class="omset-card-badge" style="background:' + color + ';color:#fff;">' + arrow + ' ' + label + ' ' + Math.abs(pct) + '%</span>';
        html += '</div>';
        html += '<div class="omset-card-body">';
        html += '<div class="omset-bar-container">';
        const maxVal = Math.max(data.avg_this_month, data.avg_last_month, 1);
        const thisPct = (data.avg_this_month / maxVal * 100).toFixed(1);
        const lastPct = (data.avg_last_month / maxVal * 100).toFixed(1);
        html += '<div class="omset-bar-row">';
        html += '<span class="omset-bar-label">Bulan Ini</span>';
        html += '<div class="omset-bar-track"><div class="omset-bar-fill" style="width:' + thisPct + '%;background:var(--primary)"></div></div>';
        html += '<span class="omset-bar-value">' + this.formatRupiahFull(data.avg_this_month) + '</span>';
        html += '</div>';
        html += '<div class="omset-bar-row">';
        html += '<span class="omset-bar-label">Bulan Lalu</span>';
        html += '<div class="omset-bar-track"><div class="omset-bar-fill" style="width:' + lastPct + '%;background:var(--outline-variant)"></div></div>';
        html += '<span class="omset-bar-value">' + this.formatRupiahFull(data.avg_last_month) + '</span>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</div>';

        container.innerHTML = html;
    }
};

document.addEventListener('DOMContentLoaded', () => {
    if (typeof Chart !== 'undefined') {
        IM.init();
    }
});