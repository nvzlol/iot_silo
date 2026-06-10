<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Silo Monitoring</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body { margin:0; font-family:sans-serif; background:#f4f6f9; }

    .header {
      background:#1e4ea1; color:white; padding:15px;
      display:flex; justify-content:space-between; align-items:center;
    }

    .container {
      padding:20px;
      display:grid;
      grid-template-columns: repeat(3, 1fr);
      gap:20px;
    }

    .card {
      background:white; padding:20px; border-radius:12px;
      box-shadow:0 5px 15px rgba(0,0,0,0.1); text-align:center;
    }

    .card h3 { margin:0 0 12px; color:#555; font-size:14px; text-transform:uppercase; }

    .value { font-size:28px; font-weight:600; color:#1e4ea1; }

    /* Tombol servo */
    .servo-btn {
      display:block; width:100%; padding:12px;
      margin-bottom:10px; border:none; border-radius:8px;
      font-size:15px; font-weight:600; cursor:pointer; transition:opacity .2s;
    }
    .servo-btn:hover { opacity:.85; }
    .btn-open  { background:#22c55e; color:white; }
    .btn-close { background:#ef4444; color:white; }

    .servo-status {
      margin-top:10px; font-size:13px; padding:6px 12px;
      border-radius:99px; display:inline-block; font-weight:500;
    }
    .status-open  { background:#dcfce7; color:#15803d; }
    .status-close { background:#fee2e2; color:#b91c1c; }

    .feedback { font-size:12px; color:#888; margin-top:8px; min-height:16px; }

    .chart-box {
      margin:0 20px 20px; background:white;
      padding:20px; border-radius:12px;
      box-shadow:0 5px 15px rgba(0,0,0,0.05);
    }

    table { width:100%; border-collapse:collapse; text-align:center; }
    th, td { padding:8px; border:1px solid #ddd; font-size:13px; }
    th { background:#1e4ea1; color:white; }
    tr:nth-child(even) td { background:#f9f9f9; }
  </style>
</head>
<body>

<div class="header">
  <h2 style="margin:0">SiloTrack</h2>
  <div style="display:flex; align-items:center; gap:15px;">
    <span id="time"></span>
    <a href="/logout" style="color:white; text-decoration:none; background:red; padding:6px 12px; border-radius:6px;">
      Logout
    </a>
  </div>
</div>

<div class="container">

  {{-- Card 1: Berat / Level --}}
  <div class="card">
    <h3>Berat Silo</h3>
    <div class="value" id="level">— g</div>
    <div style="font-size:12px; color:#aaa; margin-top:6px" id="last-update">Menunggu data...</div>
  </div>

  {{-- Card 2: Status --}}
  <div class="card">
    <h3>Status</h3>
    <div class="value" id="alert" style="color:green">Aman</div>
  </div>

  {{-- Card 3: Kontrol Servo --}}
  <div class="card">
    <h3>Kontrol Valve (Servo)</h3>

    <button class="servo-btn btn-open"  onclick="kirimServo('open')">&#9654; Buka Valve</button>
    <button class="servo-btn btn-close" onclick="kirimServo('close')">&#9646; Tutup Valve</button>

    <div>
      <span id="servo-status" class="servo-status {{ ($latestCommand && $latestCommand->command === 'open') ? 'status-open' : 'status-close' }}">
        {{ ($latestCommand && $latestCommand->command === 'open') ? 'Valve TERBUKA' : 'Valve TERTUTUP' }}
      </span>
    </div>

    <div class="feedback" id="servo-feedback"></div>
  </div>

</div>

{{-- Chart --}}
<div class="chart-box">
  <h3 style="margin:0 0 16px; color:#333">Grafik Berat Silo</h3>
  <canvas id="myChart"></canvas>
</div>

{{-- Tabel riwayat --}}
<div class="chart-box">
  <h3 style="margin:0 0 16px; color:#333">Riwayat Data Sensor</h3>
  <table>
    <thead>
      <tr>
        <th>Waktu</th>
        <th>Berat (gram)</th>
      </tr>
    </thead>
    <tbody id="historyTable"></tbody>
  </table>
</div>

<script>
// ── Jam ──────────────────────────────────────────────────────────
setInterval(() => {
  document.getElementById('time').innerText = new Date().toLocaleTimeString('id-ID');
}, 1000);

// ── Chart ────────────────────────────────────────────────────────
const ctx = document.getElementById('myChart');
let chart = new Chart(ctx, {
  type: 'line',
  data: {
    labels: [],
    datasets: [{
      label: 'Berat Silo (gram)',
      data: [],
      borderColor: '#1e4ea1',
      backgroundColor: 'rgba(30,78,161,0.08)',
      tension: 0.3,
      fill: true,
    }]
  },
  options: { animation: false, responsive: true }
});

// ── Load data sensor ─────────────────────────────────────────────
async function loadData() {
  try {
    const res  = await fetch('/api/sensor');
    const data = await res.json();

    if (!data.length) return;

    const last = data[0];

    // Berat card
    document.getElementById('level').innerText = parseFloat(last.value).toFixed(1) + ' g';
    document.getElementById('last-update').innerText =
      'Update: ' + new Date(last.created_at).toLocaleTimeString('id-ID');

    // Status
    const alertEl = document.getElementById('alert');
    if (last.value <= 100) {
      alertEl.innerText = 'Kritis!';
      alertEl.style.color = 'red';
    } else {
      alertEl.innerText = 'Aman';
      alertEl.style.color = 'green';
    }

    // Tabel
    const tbody = document.getElementById('historyTable');
    tbody.innerHTML = '';
    data.forEach(item => {
      const waktu = new Date(item.created_at).toLocaleString('id-ID');
      tbody.innerHTML += `<tr><td>${waktu}</td><td>${parseFloat(item.value).toFixed(1)}</td></tr>`;
    });

    // Chart
    const reversed = [...data].reverse();
    chart.data.labels = reversed.map(i => new Date(i.created_at).toLocaleTimeString('id-ID'));
    chart.data.datasets[0].data = reversed.map(i => i.value);
    chart.update();

  } catch (err) {
    console.error('loadData error:', err);
  }
}

loadData();
setInterval(loadData, 3000);

// ── Kontrol Servo ────────────────────────────────────────────────
async function kirimServo(command) {
  const feedback  = document.getElementById('servo-feedback');
  const statusEl  = document.getElementById('servo-status');
  feedback.innerText = 'Mengirim perintah...';

  try {
    const res = await fetch('/servo/command', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json',
      },
      body: JSON.stringify({ command }),
    });

    const data = await res.json();

    if (res.ok) {
      const isOpen = command === 'open';
      statusEl.innerText    = isOpen ? 'Valve TERBUKA' : 'Valve TERTUTUP';
      statusEl.className    = 'servo-status ' + (isOpen ? 'status-open' : 'status-close');
      feedback.innerText    = 'Berhasil dikirim ke ESP32.';
    } else {
      feedback.innerText = 'Gagal: ' + (data.message ?? 'Unknown error');
    }

  } catch (err) {
    feedback.innerText = 'Error koneksi ke server.';
  }
}
</script>

</body>
</html>