@extends('layouts.app')

@section('page_heading', 'Dashboard MQTT')

@section('content')
<div x-data="mqttDashboard()" x-init="initMqtt()" class="pb-5">
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h1 class="h3 mb-0 text-gray-800">Analisis Tren Sensor (Realtime MQTT)</h1>
            <p class="text-muted small my-1">
                Koneksi MQTT: 
                <span x-text="connectionStatus" :class="{
                    'text-success font-weight-bold': connectionStatus === 'Terhubung',
                    'text-danger font-weight-bold': connectionStatus === 'Gagal' || connectionStatus === 'Terputus',
                    'text-warning font-weight-bold': connectionStatus === 'Menghubungkan...'
                }"></span>
            </p>
        </div>
    </div>

    <!-- menampilkan grafik-->
    <div class="row">
        <!-- CO2 Chart -->
        <div class="col-xl-12 col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Tren CO2 (Carbon Dioxide)</h6>
                    <div class="h5 mb-0 font-weight-bold text-gray-800" x-text="currentData.co2 + ' ppm'"></div>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="height: 250px;">
                        <canvas id="co2Chart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- O2 Chart -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-success">Tren O2 (Oxygen)</h6>
                    <div class="h5 mb-0 font-weight-bold text-gray-800" x-text="currentData.o2 + ' %'"></div>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="height: 250px;">
                        <canvas id="o2Chart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- PM2.5 Chart -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-danger">Tren PM2.5 (Particulate Matter)</h6>
                    <div class="h5 mb-0 font-weight-bold text-gray-800" x-text="currentData.pm25 + ' µg/m³'"></div>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="height: 250px;">
                        <canvas id="pm25Chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('mqttDashboard', () => ({
        connectionStatus: 'Menghubungkan...',
        client: null,
        currentData: {
            co2: 0,
            o2: 0,
            pm25: 0
        },
        charts: {
            co2: null,
            o2: null,
            pm25: null
        },
        chartData: {
            labels: [],
            co2: [],
            o2: [],
            pm25: []
        },
        maxDataPoints: 20,

        initMqtt() {
            this.initCharts();
            
            // Konfigurasi broker MQTT (public broker untuk demo, bisa diubah)
            const brokerUrl = 'ws://broker.emqx.io:8083/mqtt';
            const topic = 'aither/sensor/data';

            this.client = mqtt.connect(brokerUrl);

            this.client.on('connect', () => {
                this.connectionStatus = 'Terhubung';
                this.client.subscribe(topic);
            });

            this.client.on('message', (t, message) => {
                if (t === topic) {
                    try {
                        const payload = JSON.parse(message.toString());
                        this.updateData(payload);
                    } catch (e) {
                        console.error('Format pesan salah:', e);
                    }
                }
            });

            this.client.on('error', (err) => {
                console.error('MQTT Error: ', err);
                this.connectionStatus = 'Gagal';
            });

            this.client.on('close', () => {
                this.connectionStatus = 'Terputus';
            });
        },

        initCharts() {
            // Setup opsi dasar chart
            const commonOptions = {
                maintainAspectRatio: false,
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { maxTicksLimit: 12, autoSkip: true, padding: 10 } },
                    y: { ticks: { padding: 10 }, grid: { color: "#e3e6f0", borderDash: [2] } }
                },
                interaction: { mode: 'index', intersect: false },
            };

            const ctxCo2 = document.getElementById('co2Chart').getContext('2d');
            this.charts.co2 = new Chart(ctxCo2, {
                type: 'line',
                data: {
                    labels: this.chartData.labels,
                    datasets: [{
                        label: 'CO2 (ppm)',
                        data: this.chartData.co2,
                        borderColor: '#4e73df',
                        backgroundColor: 'rgba(78, 115, 223, 0.05)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: commonOptions
            });

            const ctxO2 = document.getElementById('o2Chart').getContext('2d');
            this.charts.o2 = new Chart(ctxO2, {
                type: 'line',
                data: {
                    labels: this.chartData.labels,
                    datasets: [{
                        label: 'O2 (%)',
                        data: this.chartData.o2,
                        borderColor: '#1cc88a',
                        backgroundColor: 'rgba(28, 200, 138, 0.05)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: commonOptions
            });

            const ctxPm25 = document.getElementById('pm25Chart').getContext('2d');
            this.charts.pm25 = new Chart(ctxPm25, {
                type: 'line',
                data: {
                    labels: this.chartData.labels,
                    datasets: [{
                        label: 'PM2.5 (µg/m³)',
                        data: this.chartData.pm25,
                        borderColor: '#e74a3b',
                        backgroundColor: 'rgba(231, 74, 59, 0.05)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: commonOptions
            });
        },

        updateData(data) {
            const now = new Date();
            const timeStr = now.getHours().toString().padStart(2, '0') + ':' + 
                          now.getMinutes().toString().padStart(2, '0') + ':' + 
                          now.getSeconds().toString().padStart(2, '0');

            this.currentData.co2 = data.co2 || 0;
            this.currentData.o2 = data.o2 || 0;
            this.currentData.pm25 = data.pm25 || 0;

            this.chartData.labels.push(timeStr);
            this.chartData.co2.push(this.currentData.co2);
            this.chartData.o2.push(this.currentData.o2);
            this.chartData.pm25.push(this.currentData.pm25);

            if (this.chartData.labels.length > this.maxDataPoints) {
                this.chartData.labels.shift();
                this.chartData.co2.shift();
                this.chartData.o2.shift();
                this.chartData.pm25.shift();
            }

            this.charts.co2.update();
            this.charts.o2.update();
            this.charts.pm25.update();
        }
    }));
});
</script>
@endpush
