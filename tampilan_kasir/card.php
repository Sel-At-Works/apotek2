<div class="main-content">
    <h1 class="dashboard-title">👨‍⚕️ Aplikasi Apotek</h1>
    <p class="welcome-msg">Anda login sebagai <strong><?= htmlspecialchars($role); ?></strong></p>

   <div class="dashboard-summary">
    <!-- Card Member -->
    <div class="card member">
        <div class="icon">👥</div>
        <div class="card-info">
            <h3>Total Member</h3>
            <div class="count-row">
                <p><?= $totalMember; ?></p>
                <a href="member.php" class="view-details">Lihat</a>
            </div>
        </div>
    </div>

    <!-- Card Kategori -->
    <div class="card kategori">
        <div class="icon">📦</div>
        <div class="card-info">
            <h3>Total Kategori</h3>
            <div class="count-row">
                <p><?= $totalKategori; ?></p>
                <!-- <a href="kategori.php" class="view-details">Lihat</a> -->
            </div>
        </div>
    </div>

    <!-- Card Produk -->
    <div class="card produk">
        <div class="icon">💊</div>
        <div class="card-info">
            <h3>Total Produk</h3>
            <div class="count-row">
                <p><?= $totalProduk; ?></p>
                <a href="produk.php" class="view-details">Lihat</a>
            </div>
        </div>
    </div>
</div>


<style>
/* Layout utama */
.main-content {
    padding: 40px;
    margin-left: 260px;
    font-family: 'Segoe UI', sans-serif;
}

.dashboard-title {
    font-size: 30px;
    color: #333;
    margin-bottom: 5px;
}

.welcome-msg {
    font-size: 16px;
    color: #666;
}

/* Dashboard Summary */
.dashboard-summary {
    display: flex;
    gap: 24px;
    margin-top: 40px;
    flex-wrap: wrap;
}

/* Kartu Ringkasan */
.card {
    flex: 1 1 300px;
    background: linear-gradient(135deg, #e3f2fd, #bbdefb);
    border-radius: 20px;
    box-shadow: 0 8px 18px rgba(0, 0, 0, 0.12);
    padding: 24px;
    color: #333;
    display: flex;
    align-items: center;
    gap: 20px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.2);
}

/* Warna Kartu Spesifik */
.card.member {
    background: linear-gradient(135deg, #2196f3, #64b5f6);
    color: white;
}

.card.kategori {
    background: linear-gradient(135deg, #4caf50, #81c784);
    color: white;
}

.card.produk {
    background: linear-gradient(135deg, #e53935, #ef5350);
    color: white;
}

/* Ikon */
.card .icon {
    font-size: 44px;
    line-height: 1;
    flex-shrink: 0;
}

.card-info h3 {
    font-size: 20px;
    font-weight: 700;
    margin: 0 0 10px;
}

.count-row {
    display: flex;
    align-items: center;
    gap: 16px;
}

.count-row p {
    margin: 0;
    font-size: 38px;
    font-weight: bold;
}

/* Tombol View */
.view-details {
    padding: 6px 16px;
    background-color: white;
    border-radius: 20px;
    color: #333;
    text-decoration: none;
    font-weight: 600;
    font-size: 13px;
    transition: all 0.3s ease;
}

.member .view-details {
    color: #1976d2;
}

.kategori .view-details {
    color: #2e7d32;
}

.produk .view-details {
    color: #b71c1c;
}

.view-details:hover {
    background-color: #f5f5f5;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

/* Responsive */
@media (max-width: 768px) {
    .main-content {
        margin-left: 0;
        padding: 20px;
    }

    .dashboard-summary {
        flex-direction: column;
    }

    .card {
        flex: 1 1 100%;
    }
}
</style>
