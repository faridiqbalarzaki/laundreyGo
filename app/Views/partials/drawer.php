<!-- ORDER DETAIL DRAWER -->
<div class="drawer-backdrop" id="orderDrawerBackdrop" style="display:none;" onclick="closeOrderDrawer()"></div>
<div class="order-drawer" id="orderDrawer">
    <div class="drawer-header">
        <div>
            <div class="drawer-subtitle text-muted">Rincian Lengkap Pesanan</div>
            <h2 class="drawer-title" id="drawerOrderId">LG-000000-000</h2>
        </div>
        <button type="button" class="btn-close-drawer" onclick="closeOrderDrawer()">&times;</button>
    </div>

    <div class="drawer-body" id="drawerBody">
        <!-- Loading State -->
        <div class="drawer-loading" id="drawerLoading" style="display:none;">
            <div class="spinner"></div>
            <p>Memuat rincian pesanan...</p>
        </div>

        <!-- Content Container -->
        <div id="drawerContent">
            <!-- Header Badges -->
            <div class="drawer-badges-row">
                <span class="badge-status" id="drawerStatusBadge">Antrean</span>
                <span class="badge" id="drawerPaymentBadge">Belum Lunas</span>
                <span class="badge-rak" id="drawerRakBadge" style="display:none;">Rak: -</span>
            </div>

            <!-- Customer Info Box -->
            <div class="drawer-section-card">
                <div class="section-title">Data Pelanggan</div>
                <div class="drawer-info-grid">
                    <div>
                        <span class="text-muted">Nama:</span>
                        <strong id="drawerCustName">-</strong>
                    </div>
                    <div>
                        <span class="text-muted">Telepon:</span>
                        <span id="drawerCustPhone">-</span>
                    </div>
                    <div style="grid-column: span 2;">
                        <span class="text-muted">Alamat:</span>
                        <span id="drawerCustAddress">-</span>
                    </div>
                </div>
            </div>

            <!-- Service Items Table -->
            <div class="drawer-section-card">
                <div class="section-title">Item Layanan</div>
                <div id="drawerItemsList" class="drawer-items-list"></div>
                <div class="drawer-total-box">
                    <span>Total Tagihan:</span>
                    <strong class="text-primary font-bold" id="drawerTotalHarga" style="font-size:18px;">Rp 0</strong>
                </div>
            </div>

            <!-- Notes & Preferences -->
            <div class="drawer-section-card">
                <div class="section-title">Catatan & Parfum</div>
                <div class="drawer-info-grid">
                    <div>
                        <span class="text-muted">Aroma Parfum:</span>
                        <span id="drawerParfum">-</span>
                    </div>
                    <div>
                        <span class="text-muted">Prioritas:</span>
                        <span id="drawerKilat">-</span>
                    </div>
                    <div style="grid-column: span 2;">
                        <span class="text-muted">Catatan Khusus:</span>
                        <span id="drawerCatatan" class="text-warning font-medium">-</span>
                    </div>
                </div>
            </div>

            <!-- Timeline Status History -->
            <div class="drawer-section-card">
                <div class="section-title">Riwayat Timeline Pengerjaan</div>
                <div class="drawer-timeline" id="drawerTimeline"></div>
            </div>
        </div>
    </div>

    <div class="drawer-footer">
        <button type="button" class="btn btn-secondary" onclick="printReceiptFromDrawer()">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            <span>Cetak Nota</span>
        </button>
        <a href="#" id="drawerWaLink" target="_blank" class="btn btn-success">
            <span>Kirim WhatsApp</span>
        </a>
    </div>
</div>
