<?php
$conn = new mysqli("localhost", "root", "", "apotek1");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Query data produk terjual & keuntungan
$grafikQuery = "
    SELECT 
        p.nama_produk,
        SUM(td.jumlah) AS total_terjual,
        SUM((p.harga_jual - p.harga_beli) * td.jumlah) AS total_keuntungan
    FROM transaksi_detail td
    JOIN transaksi t ON td.id_transaksi = t.id
    JOIN produk p ON td.id_produk = p.id
    GROUP BY p.id
    ORDER BY total_terjual DESC
";
$grafikResult = $conn->query($grafikQuery);

$produkLabels = [];
$produkData = [];
$keuntunganData = [];
$totalSemuaProduk = 0;
$totalSemuaKeuntungan = 0;

while ($g = $grafikResult->fetch_assoc()) {
    $produkLabels[] = $g['nama_produk'];
    $produkData[] = $g['total_terjual'];
    $keuntunganData[] = $g['total_keuntungan'];
    $totalSemuaProduk += $g['total_terjual'];
    $totalSemuaKeuntungan += $g['total_keuntungan'];
}
// // Cek isi array
// echo "<pre>";
// print_r($produkData);
// print_r($keuntunganData);
// echo "</pre>";
?>

<!-- Kotak Statistik -->
<div style="display:flex; justify-content:center; gap:30px; margin:20px 0 15px 0; flex-wrap:wrap;">
    <div style="text-align:center; padding:10px 20px; background:#f8f9fa; border-radius:8px; min-width:150px; box-shadow:0 1px 4px rgba(0,0,0,0.1);">
        <h4 style="margin:5px 0; font-size:14px; color:#555;">Total Semua Produk Terjual</h4>
        <div style="font-size:20px; font-weight:bold; color:#28a745;">
            <?= number_format($totalSemuaProduk, 0, ',', '.') ?> pcs
        </div>
    </div>
    <div style="text-align:center; padding:10px 20px; background:#f8f9fa; border-radius:8px; min-width:150px; box-shadow:0 1px 4px rgba(0,0,0,0.1);">
        <h4 style="margin:5px 0; font-size:14px; color:#555;">Total Keuntungan</h4>
        <div style="font-size:20px; font-weight:bold; color:#ff9800;">
            Rp<?= number_format($totalSemuaKeuntungan, 0, ',', '.') ?>
        </div>
    </div>
</div>

<!-- Judul Grafik -->
<h3 style="text-align:center;">Statistik Produk Terjual & Keuntungan</h3>
<canvas id="grafikStatistik" style="max-height:400px;"></canvas>

<!-- Script Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<button id="downloadPDF" style="
    display: block;
    margin: 20px auto;
    padding: 12px 25px;
    background: linear-gradient(90deg, #28a745, #218838);
    color: #fff;
    font-size: 16px;
    font-weight: 600;
    border: none;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(40, 167, 69, 0.4);
    cursor: pointer;
    transition: background 0.3s ease, box-shadow 0.3s ease;
">
    <span style="display: inline-flex; align-items: center; gap: 8px;">
        <!-- Icon download simple SVG -->
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="white" viewBox="0 0 24 24">
            <path d="M12 16l4-5h-3V4h-2v7H8l4 5zm-7 4v2h14v-2H5z"/>
        </svg>
        Download Grafik PDF
    </span>
</button>

<script>
const ctx = document.getElementById('grafikStatistik').getContext('2d');
const grafikStatistik = new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($produkLabels) ?>,
        datasets: [
            {
                label: 'Total Terjual (pcs)',
                data: <?= json_encode($produkData) ?>,
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                fill: true,
                tension: 0.3,
                yAxisID: 'y'
            },
            {
                label: 'Total Keuntungan (Rp)',
                data: <?= json_encode($keuntunganData) ?>,
                borderColor: 'rgba(255, 159, 64, 1)',
                backgroundColor: 'rgba(255, 159, 64, 0.2)',
                fill: true,
                tension: 0.3,
                yAxisID: 'y1'
            }
        ]
    },
    options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        stacked: false,
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        if (context.dataset.label.includes('Keuntungan')) {
                            return 'Rp' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                        } else {
                            return context.parsed.y + ' pcs';
                        }
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                title: { display: true, text: 'Jumlah Terjual (pcs)' }
            },
            y1: {
                beginAtZero: true,
                position: 'right',
                title: { display: true, text: 'Keuntungan (Rp)' },
                grid: { drawOnChartArea: false }
            }
        }
    }
});
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
    document.getElementById('downloadPDF').addEventListener('click', () => {
  const { jsPDF } = window.jspdf;

  const canvas = document.getElementById('grafikStatistik');
  
  html2canvas(canvas).then((canvasImg) => {
    const imgData = canvasImg.toDataURL('image/png');
    const pdf = new jsPDF({
      orientation: 'landscape',
      unit: 'pt',
      format: [canvas.width, canvas.height]
    });

    pdf.addImage(imgData, 'PNG', 0, 0, canvas.width, canvas.height);
    pdf.save('grafik_produk.pdf');
  });
});

</script>
