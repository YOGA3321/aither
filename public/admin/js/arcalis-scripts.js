// public/admin/js/arcalis-scripts.js

// Variabel global untuk instance Chart.js
let kelembabanTanahChart;
let npkTanahChart;
let phTanahChart;
let suhuKelembabanUdaraChart;

// Fungsi untuk mengambil data sensor terbaru untuk kartu dari API
async function fetchLatestSensorDataForCards() {
    console.log('fetchLatestSensorDataForCards: START');
    try {
        const response = await fetch({{ route('api.latest-sensor-data') }}); 
        if (!response.ok) {
            console.error('fetchLatestSensorDataForCards: HTTP Error', response.status);
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();
        console.log('fetchLatestSensorDataForCards: Sukses, data:', data);
        return data;
    } catch (error) {
        console.error("fetchLatestSensorDataForCards: GAGAL", error);
        return []; 
    }
}

// Fungsi untuk mengambil data sensor historis untuk grafik dari API
async function fetchHistoricalSensorDataForCharts(plantId = null) {
    console.log('fetchHistoricalSensorDataForCharts: START', plantId);
    try {
        const url = plantId ? `{{ route('api.historical-sensor-data') }}?plant_id=${plantId}&hours=24` : `{{ route('api.historical-sensor-data') }}?hours=24`;
        const response = await fetch(url);
        if (!response.ok) {
            console.error('fetchHistoricalSensorDataForCharts: HTTP Error', response.status);
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();
        console.log('fetchHistoricalSensorDataForCharts: Sukses, data:', data);
        return data;
    } catch (error) {
        console.error("fetchHistoricalSensorDataForCharts: GAGAL", error);
        return {
            labels: [],
            plant_name: 'N/A', 
            kelembabanTanah: [{label: 'Kelembaban Tanah', data: []}], 
            npkTanah: [{label: 'NPK N', data: []}, {label: 'NPK P', data: []}, {label: 'NPK K', data: []}],
            phTanah: [{label: 'pH Tanah', data: []}],
            suhuKelembabanUdara: [{label: 'Suhu Udara', data: []}, {label: 'Kelembaban Udara', data: []}]
        };
    }
}

// Fungsi untuk mengambil laporan harian dari API
async function fetchDailyReportsFromApi(startDate = null, endDate = null, plantId = null) {
    console.log('fetchDailyReportsFromApi: START', startDate, endDate, plantId);
    let url = `{{ route('api.daily-reports') }}`; 
    const params = new URLSearchParams();
    if (startDate && endDate) {
        params.append('start_date', startDate);
        params.append('end_date', endDate);
    }
    if (plantId) {
        params.append('plant_id', plantId);
    }
    if (params.toString()) {
        url += `?${params.toString()}`;
    }
    try {
        const response = await fetch(url);
        if (!response.ok) {
            console.error('fetchDailyReportsFromApi: HTTP Error', response.status);
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();
        console.log('fetchDailyReportsFromApi: Sukses, data:', data);
        return data;
    } catch (error) {
        console.error("Gagal mengambil laporan harian:", error);
        return [];
    }
}

// Fungsi untuk memperbarui kartu ESP di dashboard
function updateEspCards(sensorData) {
    console.log('updateEspCards: START', sensorData); // Log di awal fungsi
    const espSensorCardsDiv = document.getElementById('espSensorCardsContainer'); 
    if (!espSensorCardsDiv) {
        console.error("ID 'espSensorCardsContainer' tidak ditemukan di DOM.");
        return;
    }

    let htmlContent = '';
    if (sensorData.length === 0) {
        console.log('updateEspCards: Tidak ada data sensor, menampilkan pesan default.');
        htmlContent = '<div class="col-12"><p class="text-center text-gray-500">Belum ada perangkat Arcalis yang terdaftar atau belum ada data sensor yang diterima.</p></div>';
    } else {
        console.log('updateEspCards: Membangun kartu untuk', sensorData.length, 'perangkat.');
        try { // Tambahkan try-catch di sekitar loop forEach
            sensorData.forEach(device => {
                // Pastikan semua properti ada atau gunakan default 'N/A'
                const status = device.status ?? 'N/A';
                const statusClass = device.status_class ?? 'secondary'; 
                
                // Gunakan id internal jika nama null atau N/A
                const deviceDisplayName = device.name && device.name !== 'N/A' && device.name !== null ? device.name : (device.id ?? 'N/A');
                
                // Pastikan properti numerik diformat dengan toFixed jika bukan 'N/A'
                const suhu = device.suhu !== 'N/A' ? `${device.suhu}°C` : 'N/A';
                const kelembaban_udara = device.kelembaban_udara !== 'N/A' ? `${device.kelembaban_udara}%` : 'N/A';
                const npk = device.npk ?? 'N/A'; // NPK sudah string
                const ph = device.ph !== 'N/A' ? `${device.ph}` : 'N/A';
                const kelembaban_tanah = device.kelembaban_tanah !== 'N/A' ? `${device.kelembaban_tanah}%` : 'N/A';
                const plant_type = device.plant_type ?? 'Tidak Dikaitkan';
                const recommendation = device.recommendation ?? 'Tidak ada rekomendasi.';
                const last_received_at = device.last_received_at ?? 'Belum ada data';

                htmlContent += `
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4"> {{-- Sesuaikan kolom agar lebih pas --}}
                    <div class="card border-left-${statusClass} shadow h-100 py-2 arcalis-card-small">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1 text-${statusClass}">
                                        ${deviceDisplayName}</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        Suhu: ${suhu} | Kelembaban Udara: ${kelembaban_udara}
                                    </div>
                                    <div class="text-gray-700 small mt-1">
                                        NPK: ${npk} | pH: ${ph} | Kelembaban Tanah: ${kelembaban_tanah}
                                    </div>
                                    <div class="text-xs font-weight-bold mt-2 text-${statusClass}">
                                        Status: ${status}
                                    </div>
                                    <div class="text-xs text-muted mt-1">
                                        Tanaman: ${plant_type}
                                    </div>
                                    <div class="text-xs text-muted">
                                        Terakhir: ${last_received_at}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-microchip fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                `;
            });
        }
        espSensorCardsDiv.innerHTML = htmlContent; // Mengisi container dengan kartu HTML
        console.log('updateEspCards: Sukses, HTML diisi.'); // Log setelah update
    } catch (e) {
        console.error('updateEspCards: GAGAL saat membangun HTML atau mengisi DOM', e); // Log jika ada error di dalam loop/saat mengisi
        espSensorCardsDiv.innerHTML = '<div class="col-12 text-center text-danger">Gagal menampilkan kartu. Error di JS.</div>';
    }
}

// Fungsi untuk menginisialisasi grafik Chart.js
async function initCharts(plantId = null) {
    console.log('initCharts: START', plantId);
    const chartData = await fetchHistoricalSensorDataForCharts(plantId);
    const plantName = chartData.plant_name || 'Semua Tanaman';

    // Hancurkan chart yang sudah ada sebelum membuat yang baru
    if (kelembabanTanahChart) kelembabanTanahChart.destroy();
    if (npkTanahChart) npkTanahChart.destroy();
    if (phTanahChart) phTanahChart.destroy();
    if (suhuKelembabanUdaraChart) suhuKelembabanUdaraChart.destroy();

    const ctxKelembaban = document.getElementById('kelembabanTanahChart');
    if (ctxKelembaban) {
        kelembabanTanahChart = new Chart(ctxKelembaban, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: chartData.kelembabanTanah && chartData.kelembabanTanah.length > 0 ? chartData.kelembabanTanah.map(ds => ({...ds, label: `${ds.label} (${plantName})`})) : []
            },
            options: {
                maintainAspectRatio: false, responsive: true, plugins: { legend: { display: true } },
                scales: {
                    x: { grid: { display: false, drawBorder: false }, ticks: { maxTicksLimit: 12 } },
                    y: {
                        ticks: { maxTicksLimit: 5, padding: 10, callback: function(value) { return value + '%'; } },
                        grid: { color: "#e3e6f0", zeroLineColor: "rgb(234, 236, 244)", drawBorder: false, borderDash: [2], zeroLineBorderDash: [2] }
                    }
                },
                tooltips: { mode: 'index', intersect: false },
                animation: { duration: 1000, easing: 'easeOutQuart' }
            }
        });
    }

    const ctxNpk = document.getElementById('npkTanahChart');
    if (ctxNpk) {
        npkTanahChart = new Chart(ctxNpk, {
            type: 'line',
            data: { labels: chartData.labels, datasets: chartData.npkTanah && chartData.npkTanah.length > 0 ? chartData.npkTanah.map(ds => ({...ds, label: `${ds.label} (${plantName})`})) : [] }, // Menggunakan chartData.npkTanah
            options: {
                maintainAspectRatio: false, responsive: true, plugins: { legend: { display: true } },
                scales: {
                    x: { grid: { display: false, drawBorder: false }, ticks: { maxTicksLimit: 12 } },
                    y: {
                        ticks: { maxTicksLimit: 5, padding: 10 },
                        grid: { color: "#e3e6f0", zeroLineColor: "rgb(234, 236, 244)", drawBorder: false, borderDash: [2], zeroLineBorderDash: [2] }
                    }
                },
                tooltips: { mode: 'index', intersect: false },
                animation: { duration: 1000, easing: 'easeOutQuart' }
            }
        });
    }

    const ctxPh = document.getElementById('phTanahChart');
    if (ctxPh) {
        phTanahChart = new Chart(ctxPh, {
            type: 'line',
            data: { labels: chartData.labels, datasets: chartData.phTanah && chartData.phTanah.length > 0 ? chartData.phTanah.map(ds => ({...ds, label: `${ds.label} (${plantName})`})) : [] },
            options: {
                maintainAspectRatio: false, responsive: true, plugins: { legend: { display: true } },
                scales: {
                    x: { grid: { display: false, drawBorder: false }, ticks: { maxTicksLimit: 12 } },
                    y: {
                        ticks: { maxTicksLimit: 5, padding: 10, min: 0, max: 14 },
                        grid: { color: "#e3e6f0", zeroLineColor: "rgb(234, 236, 244)", drawBorder: false, borderDash: [2], zeroLineBorderDash: [2] }
                    }
                },
                tooltips: { mode: 'index', intersect: false },
                animation: { duration: 1000, easing: 'easeOutQuart' }
            }
        });
    }

    const ctxSuhuKelembabanUdara = document.getElementById('suhuKelembabanUdaraChart');
    if (ctxSuhuKelembabanUdara) {
        suhuKelembabanUdaraChart = new Chart(ctxSuhuKelembabanUdara, {
            type: 'line',
            data: { labels: chartData.labels, datasets: chartData.suhuKelembabanUdara && chartData.suhuKelembabanUdara.length > 0 ? chartData.suhuKelembabanUdara.map(ds => ({...ds, label: `${ds.label} (${plantName})`})) : [] },
            options: {
                maintainAspectRatio: false, responsive: true, plugins: { legend: { display: true } },
                scales: {
                    x: { grid: { display: false, drawBorder: false }, ticks: { maxTicksLimit: 12 } },
                    y: {
                        'temperature-y-axis': {
                            type: 'linear', position: 'left', beginAtZero: false,
                            ticks: { maxTicksLimit: 5, padding: 10, callback: function(value) { return value + '°C'; } },
                            grid: { color: "#e3e6f0", zeroLineColor: "rgb(234, 236, 244)", drawBorder: false, borderDash: [2], zeroLineBorderDash: [2] }
                        },
                        'humidity-y-axis': {
                            type: 'linear', position: 'right', beginAtZero: false,
                            ticks: { maxTicksLimit: 5, padding: 10, callback: function(value) { return value + '%'; } },
                            grid: { display: false }
                        }
                    }
                },
                tooltips: { mode: 'index', intersect: false },
                animation: { duration: 1000, easing: 'easeOutQuart' }
            }
        });
    }
}

async function refreshDashboardData(plantId = null) {
    console.log('refreshDashboardData: START', plantId); // Debug log
    const initialLoadingMessageDiv = document.getElementById('initialLoadingMessage'); // Ambil elemen loading

    if (initialLoadingMessageDiv) {
        initialLoadingMessageDiv.style.display = 'block'; // Pastikan pesan loading terlihat
        initialLoadingMessageDiv.innerHTML = '<i class="fas fa-spinner fa-spin fa-2x"></i> Memuat data perangkat...';
    }
    // Pastikan container cards dikosongkan sebelum diisi
    const espSensorCardsContainer = document.getElementById('espSensorCardsContainer');
    if (espSensorCardsContainer) {
        espSensorCardsContainer.innerHTML = ''; // Kosongkan dulu kontennya
        // Tambahkan kembali div initialLoadingMessage ke dalam container jika perlu
        if (initialLoadingMessageDiv && !espSensorCardsContainer.contains(initialLoadingMessageDiv)) {
            espSensorCardsContainer.appendChild(initialLoadingMessageDiv);
        }
    }


    try {
        const latestSensorData = await fetchLatestSensorDataForCards();
        updateEspCards(latestSensorData);

        await initCharts(plantId);

        const dailyReports = await fetchDailyReportsFromApi(null, null, plantId);
        updateDailyReportTable(dailyReports);

    } catch (error) {
        console.error('refreshDashboardData: GAGAL TOTAL', error);
        if (initialLoadingMessageDiv) {
            initialLoadingMessageDiv.innerHTML = '<div class="col-12 text-center text-danger">Gagal memuat data. Periksa konsol browser untuk detail error.</div>';
        }
    } finally {
        // Sembunyikan loading jika tidak ada pesan error kustom (berarti sukses atau kosong)
        if (initialLoadingMessageDiv && initialLoadingMessageDiv.innerHTML.includes('Memuat data perangkat...')) { // Hanya sembunyikan jika masih pesan loading
            initialLoadingMessageDiv.style.display = 'none';
        }
        console.log('refreshDashboardData: SELESAI. Loading disembunyikan (jika tidak ada error).');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const plantFilter = document.getElementById('plantFilter');
    if (plantFilter) {
        plantFilter.addEventListener('change', function() {
            const selectedPlantId = this.value;
            refreshDashboardData(selectedPlantId);
        });
    }

    // >> LOGIC UNTUK MEMUAT DATA SAAT HALAMAN PERTAMA KALI DIMUAT <<
    // Ambil nilai default dari dropdown
    const initialPlantTypeId = plantFilter ? plantFilter.value : null; 
    refreshDashboardData(initialPlantTypeId);

    setInterval(() => refreshDashboardData(plantFilter ? plantFilter.value : null), 30000);
});