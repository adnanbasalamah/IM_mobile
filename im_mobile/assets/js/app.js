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
        const validPages = ['dashboard', 'transfer', 'nota', 'stok', 'kasir', 'harga'];
        if (!validPages.includes(page)) page = 'dashboard';

        this.page = page;
        if (updateHash) window.location.hash = page;

        this.updateNavActive(page);

        const content = document.getElementById('content');
        if (!content) return;

        content.classList.add('loading');
        try {
            const resp = await fetch('pages/' + page + '.php');
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
            case 'stok': this.initStok(); break;
            case 'kasir': this.initKasir(); break;
            case 'harga': this.initHarga(); break;
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
        const dateDisplay = document.getElementById('dash-date-display');
        if (!dateInput) return;

        const today = new Date().toISOString().split('T')[0];
        dateInput.value = today;
        if (dateDisplay) dateDisplay.textContent = this.formatDate(today);

        const dateBtn = document.getElementById('dash-date-btn');
        if (dateBtn) {
            dateBtn.addEventListener('click', () => dateInput.showPicker ? dateInput.showPicker() : dateInput.click());
        }

        dateInput.addEventListener('change', () => {
            if (dateDisplay) dateDisplay.textContent = this.formatDate(dateInput.value);
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
        const dateDisplay = document.getElementById('transfer-date-display');
        if (!dateInput) return;

        const today = new Date().toISOString().split('T')[0];
        dateInput.value = today;
        if (dateDisplay) dateDisplay.textContent = this.formatDate(today);

        const dateBtn = document.getElementById('transfer-date-btn');
        if (dateBtn) {
            dateBtn.addEventListener('click', () => dateInput.showPicker ? dateInput.showPicker() : dateInput.click());
        }

        dateInput.addEventListener('change', () => {
            if (dateDisplay) dateDisplay.textContent = this.formatDate(dateInput.value);
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
            html += '<div class="transfer-item">';
            html += '<div class="transfer-row">';
            html += '<span class="transfer-jam">' + t.jam_fmt + '</span>';
            html += '<span class="transfer-nilai">' + this.formatRupiahFull(t.nilai) + '</span>';
            html += '</div>';
            html += '<div class="transfer-kasir">Kasir: ' + this.escapeHtml(t.kasir) + '</div>';
            html += '<a href="#nota" class="transfer-pelanggan" data-sale-id="' + t.sale_id + '" onclick="IM.openNota(' + t.sale_id + '); return false;">' + this.escapeHtml(t.pelanggan) + '</a>';
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

    // ===== CEK STOK =====
    initStok() {
        const select = document.getElementById('stok-kategori');
        if (!select) return;

        this.loadCategories(select);
        select.addEventListener('change', () => {
            this.loadStok(select.value);
        });
    },

    async loadCategories(selectEl) {
        try {
            const resp = await fetch('api/categories.php');
            const data = await resp.json();
            if (data.categories && data.categories.length > 0) {
                data.categories.forEach(cat => {
                    const opt = document.createElement('option');
                    opt.value = cat;
                    opt.textContent = cat;
                    selectEl.appendChild(opt);
                });
            }
        } catch (err) {
            console.error('Categories load error:', err);
        }
    },

    async loadStok(kategori) {
        const container = document.getElementById('stok-list');
        const actions = document.getElementById('stok-actions');
        if (!container) return;

        if (!kategori) {
            container.innerHTML = '<div class="stock-empty"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><path d="M3.27 6.96L12 12.01l8.73-5.05M12 22.08V12"/></svg><p>Pilih kategori terlebih dahulu</p></div>';
            if (actions) actions.classList.add('hidden');
            return;
        }

        container.innerHTML = '<div class="stock-empty"><p>Memuat data stok...</p></div>';

        try {
            const resp = await fetch('api/stok.php?kategori=' + encodeURIComponent(kategori));
            const data = await resp.json();
            this.renderStok(data);
        } catch (err) {
            container.innerHTML = '<div class="stock-empty"><p>Gagal memuat data</p></div>';
        }
    },

    renderStok(data) {
        const container = document.getElementById('stok-list');
        const actions = document.getElementById('stok-actions');
        if (!container) return;

        if (!data.items || data.items.length === 0) {
            container.innerHTML = '<div class="stock-empty"><p>Tidak ada barang dengan stok rendah untuk kategori ini</p></div>';
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
                if (item.sku) html += '<p class="sku">SKU: ' + this.escapeHtml(item.sku) + '</p>';
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
        window._stokData = data;
    },

    sendStokWhatsApp() {
        if (!window._stokData) return;
        let text = '*Stok Rendah IkhwanMart*\n\n';
        window._stokData.items.forEach(group => {
            text += '*' + group.supplier + '*\n';
            group.items.forEach(item => {
                text += '\u2022 ' + item.name + ': ' + item.quantity + ' ' + (item.pack_name || 'pcs') + ' (min: ' + item.reorder_level + ')\n';
            });
            text += '\n';
        });
        const url = 'https://wa.me/?text=' + encodeURIComponent(text);
        window.open(url, '_blank');
    },

    copyStokList() {
        if (!window._stokData) return;
        let text = 'Stok Rendah IkhwanMart\n\n';
        window._stokData.items.forEach(group => {
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

    // ===== CEK KASIR =====
    initKasir() {
        const dateInput = document.getElementById('kasir-date');
        const timeInput = document.getElementById('kasir-time');
        const select = document.getElementById('kasir-employee');

        if (!dateInput || !timeInput) return;

        const now = new Date();
        dateInput.value = now.toISOString().split('T')[0];
        timeInput.value = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');

        this.loadEmployees(select);
        this.bindKasirInputs();
    },

    async loadEmployees(selectEl) {
        if (!selectEl) return;
        try {
            const resp = await fetch('api/employees.php');
            const data = await resp.json();
            if (data.employees) {
                data.employees.forEach(emp => {
                    const opt = document.createElement('option');
                    opt.value = emp.person_id;
                    opt.textContent = emp.nama;
                    selectEl.appendChild(opt);
                });
            }
        } catch (err) {
            console.error('Employees load error:', err);
        }
    },

    bindKasirInputs() {
        const inputs = document.querySelectorAll('.kasir-count-input');
        inputs.forEach(input => {
            input.addEventListener('input', () => this.calculateKasir());
        });

        const form = document.getElementById('kasir-form');
        if (form) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.submitKasir();
            });
        }
    },

    calculateKasir() {
        const rp100k = parseInt(document.getElementById('kasir-100k')?.value || 0) * 100000;
        const rp50k = parseInt(document.getElementById('kasir-50k')?.value || 0) * 50000;
        const rp20k = parseInt(document.getElementById('kasir-20k')?.value || 0) * 20000;
        const rp10k = parseInt(document.getElementById('kasir-10k')?.value || 0) * 10000;
        const rp5k = parseInt(document.getElementById('kasir-5k')?.value || 0) * 5000;
        const rp2k = parseInt(document.getElementById('kasir-2k')?.value || 0) * 2000;
        const rp1k = parseInt(document.getElementById('kasir-1k')?.value || 0) * 1000;
        const coinTotal = parseInt(document.getElementById('kasir-coin')?.value || 0);

        const totalKutipan = rp100k + rp50k;
        const totalDiKasir = totalKutipan + rp20k + rp10k + rp5k + rp2k + rp1k + coinTotal;

        const elKutipan = document.getElementById('total-kutipan');
        const elKasir = document.getElementById('total-di-kasir');
        if (elKutipan) elKutipan.textContent = this.formatRupiahFull(totalKutipan);
        if (elKasir) elKasir.textContent = this.formatRupiahFull(totalDiKasir);
    },

    async submitKasir() {
        const personId = parseInt(document.getElementById('kasir-employee')?.value || 0);
        const cashierName = document.getElementById('kasir-employee')?.selectedOptions[0]?.text || '';
        const recordDate = document.getElementById('kasir-date')?.value || '';
        const recordTime = document.getElementById('kasir-time')?.value || '';

        const rp100k = parseInt(document.getElementById('kasir-100k')?.value || 0);
        const rp50k = parseInt(document.getElementById('kasir-50k')?.value || 0);
        const rp20k = parseInt(document.getElementById('kasir-20k')?.value || 0);
        const rp10k = parseInt(document.getElementById('kasir-10k')?.value || 0);
        const rp5k = parseInt(document.getElementById('kasir-5k')?.value || 0);
        const rp2k = parseInt(document.getElementById('kasir-2k')?.value || 0);
        const rp1k = parseInt(document.getElementById('kasir-1k')?.value || 0);
        const coinTotal = parseInt(document.getElementById('kasir-coin')?.value || 0);
        const totalKutipan = rp100k * 100000 + rp50k * 50000;
        const totalDiKasir = totalKutipan + rp20k * 20000 + rp10k * 10000 + rp5k * 5000 + rp2k * 2000 + rp1k * 1000 + coinTotal;

        if (!personId || !recordDate || !recordTime) {
            this.showToast('Data tidak lengkap');
            return;
        }

        try {
            const resp = await fetch('api/kasir_save.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    person_id: personId,
                    cashier: cashierName,
                    record_date: recordDate,
                    record_time: recordTime,
                    rp100k: rp100k,
                    rp50k: rp50k,
                    rp20k: rp20k,
                    rp10k: rp10k,
                    rp5k: rp5k,
                    rp2k: rp2k,
                    rp1k: rp1k,
                    coin_total: coinTotal,
                    total_kutipan: totalKutipan,
                    total_di_kasir: totalDiKasir,
                })
            });

            const data = await resp.json();
            if (data.success) {
                this.showToast('Data kasir berhasil disimpan!');
                this.resetKasirForm();
            } else {
                this.showToast('Gagal menyimpan: ' + (data.error || 'Unknown error'));
            }
        } catch (err) {
            this.showToast('Error: ' + err.message);
        }
    },

    resetKasirForm() {
        const inputs = document.querySelectorAll('.kasir-count-input');
        inputs.forEach(input => { input.value = ''; });
        this.calculateKasir();
    },

    // ===== UPDATE HARGA =====
    initHarga() {
        const searchInput = document.getElementById('harga-search');
        const clearBtn = document.getElementById('harga-search-clear');
        if (!searchInput) return;

        searchInput.addEventListener('input', () => {
            const q = searchInput.value.trim();
            clearBtn.style.display = q ? 'flex' : 'none';
            this.searchHarga(q);
        });

        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                searchInput.value = '';
                clearBtn.style.display = 'none';
                document.getElementById('harga-list').innerHTML = '<div class="stock-empty"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:48px;height:48px;color:var(--outline)"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg><p>Ketik minimal 3 huruf untuk mencari</p></div>';
                document.getElementById('harga-count').textContent = '0 Produk';
            });
        }

        const modalOverlay = document.getElementById('harga-modal-overlay');
        if (modalOverlay) modalOverlay.addEventListener('click', () => this.closeHargaModal());

        const btnCancel = document.getElementById('harga-btn-cancel');
        if (btnCancel) btnCancel.addEventListener('click', () => this.closeHargaModal());

        const btnSave = document.getElementById('harga-btn-save');
        if (btnSave) btnSave.addEventListener('click', () => this.saveHarga());

        const costInput = document.getElementById('harga-edit-cost');
        const priceInput = document.getElementById('harga-edit-price');
        if (costInput) costInput.addEventListener('input', () => this.updateProfitDisplay());
        if (priceInput) priceInput.addEventListener('input', () => this.updateProfitDisplay());
    },

    async loadAllHarga() {
        try {
            const resp = await fetch('api/harga_search.php');
            const data = await resp.json();
            this.renderHargaResults(data);
        } catch (err) {
            console.error('Harga load error:', err);
        }
    },

    searchTimeout: null,

    searchHarga(q) {
        clearTimeout(this.searchTimeout);
        const container = document.getElementById('harga-list');
        const countEl = document.getElementById('harga-count');
        if (q.length < 3) {
            if (container) container.innerHTML = '<div class="stock-empty"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:48px;height:48px;color:var(--outline)"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg><p>Ketik minimal 3 huruf untuk mencari</p></div>';
            if (countEl) countEl.textContent = '0 Produk';
            return;
        }
        this.searchTimeout = setTimeout(() => {
            fetch('api/harga_search.php?q=' + encodeURIComponent(q)).then(r => r.json()).then(data => this.renderHargaResults(data)).catch(() => {});
        }, 300);
    },

    renderHargaResults(data) {
        const container = document.getElementById('harga-list');
        const countEl = document.getElementById('harga-count');
        if (!container) return;

        const items = data.items || [];
        if (countEl) countEl.textContent = (data.total || items.length) + ' Produk';

        if (items.length === 0) {
            container.innerHTML = '<div class="stock-empty"><p>Produk tidak ditemukan</p></div>';
            return;
        }

        let html = '';
        items.forEach(item => {
            html += '<div class="harga-item" onclick="IM.openHargaEdit(' + item.item_id + ')">';
            html += '<div class="harga-item-info">';
            html += '<p class="harga-item-name">' + this.escapeHtml(item.name) + '</p>';
            html += '<div class="harga-item-detail">';
            html += '<div class="harga-item-prices">';
            html += '<span class="harga-item-price-tag harga-item-cost">Modal ' + this.formatRupiahFull(item.cost_price) + '</span>';
            html += '<span class="harga-item-price-tag harga-item-selling">Jual ' + this.formatRupiahFull(item.unit_price) + '</span>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            html += '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="harga-item-arrow" style="width:20px;height:20px"><polyline points="9 18 15 12 9 6"/></svg>';
            html += '</div>';
        });

        container.innerHTML = html;
    },

    hargaEditItem: null,

    openHargaEdit(itemId) {
        const item = document.querySelector('.harga-item[onclick*="' + itemId + '"]');
        fetch('api/harga_search.php?q=').then(r => r.json()).then(data => {
            const found = (data.items || []).find(i => i.item_id === itemId);
            if (!found) return;

            this.hargaEditItem = found;
            const nameEl = document.getElementById('harga-edit-name');
            const skuEl = document.getElementById('harga-edit-sku');
            const costEl = document.getElementById('harga-edit-cost');
            const priceEl = document.getElementById('harga-edit-price');

            if (nameEl) nameEl.textContent = found.name;
            if (skuEl) skuEl.textContent = found.sku ? 'SKU: ' + found.sku : '';
            if (costEl) costEl.value = found.cost_price;
            if (priceEl) priceEl.value = found.unit_price;

            this.updateProfitDisplay();

            const modal = document.getElementById('harga-edit-modal');
            if (modal) modal.style.display = 'flex';
            if (costEl) costEl.focus();
        });
    },

    closeHargaModal() {
        const modal = document.getElementById('harga-edit-modal');
        if (modal) modal.style.display = 'none';
        this.hargaEditItem = null;
    },

    updateProfitDisplay() {
        const costEl = document.getElementById('harga-edit-cost');
        const priceEl = document.getElementById('harga-edit-price');
        const profitEl = document.getElementById('harga-edit-profit');

        if (!costEl || !priceEl || !profitEl) return;

        const cost = parseFloat(costEl.value) || 0;
        const price = parseFloat(priceEl.value) || 0;
        const profit = price - cost;
        const pct = cost > 0 ? ((profit / cost) * 100).toFixed(1) : '0.0';

        if (profit >= 0) {
            profitEl.innerHTML = '<span>Estimasi Keuntungan:</span> <span class="harga-edit-profit-value">+Rp ' + this.formatRupiahFull(profit) + ' (' + pct + '%)</span>';
            profitEl.style.color = '#059669';
            profitEl.style.background = 'rgba(5,150,105,0.1)';
            profitEl.style.borderColor = 'rgba(5,150,105,0.3)';
        } else {
            profitEl.innerHTML = '<span>Kerugian:</span> <span class="harga-edit-profit-value">-Rp ' + this.formatRupiahFull(Math.abs(profit)) + '</span>';
            profitEl.style.color = '#ba1a1a';
            profitEl.style.background = 'rgba(186,26,26,0.1)';
            profitEl.style.borderColor = 'rgba(186,26,26,0.3)';
        }
    },

    async saveHarga() {
        if (!this.hargaEditItem) return;

        const costEl = document.getElementById('harga-edit-cost');
        const priceEl = document.getElementById('harga-edit-price');
        const btnSave = document.getElementById('harga-btn-save');

        const costPrice = parseFloat(costEl.value) || 0;
        const unitPrice = parseFloat(priceEl.value) || 0;

        if (btnSave) btnSave.textContent = 'Menyimpan...';
        if (btnSave) btnSave.disabled = true;

        try {
            const resp = await fetch('api/harga_update.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    item_id: this.hargaEditItem.item_id,
                    cost_price: costPrice,
                    unit_price: unitPrice,
                })
            });

            const data = await resp.json();
            if (data.success) {
                this.showToast('Harga berhasil disimpan!');
                this.closeHargaModal();
                const searchInput = document.getElementById('harga-search');
                this.searchHarga(searchInput ? searchInput.value.trim() : '');
            } else {
                this.showToast('Gagal: ' + (data.error || 'Unknown error'));
            }
        } catch (err) {
            this.showToast('Error: ' + err.message);
        } finally {
            if (btnSave) { btnSave.textContent = 'Simpan'; btnSave.disabled = false; }
        }
    }
};

document.addEventListener('DOMContentLoaded', () => {
    if (typeof Chart !== 'undefined') {
        IM.init();
    }
});