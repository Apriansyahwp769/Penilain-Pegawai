<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penilaian Kinerja</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10pt;
            color: #333;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            background: #fff;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e2e8f0;
        }
        .header h1 {
            font-size: 18pt;
            font-weight: bold;
            color: #1e293b;
            margin: 0;
        }
        .header p {
            font-size: 10pt;
            color: #64748b;
            margin: 4px 0 0;
        }
        .filters {
            font-size: 9pt;
            color: #475569;
            margin-bottom: 15px;
            padding: 6px 10px;
            background: #f8fafc;
            border-radius: 6px;
            border-left: 3px solid #3b82f6;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 12pt;
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 10px;
            padding-bottom: 4px;
            border-bottom: 1px solid #e2e8f0;
        }
        .section-desc {
            font-size: 9pt;
            color: #64748b;
            margin-bottom: 12px;
        }

        /* KPI Grid */
        .kpi-grid {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .kpi-card {
            flex: 1;
            min-width: 120px;
            background: #f9fafb;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
            text-align: center;
        }
        .kpi-label {
            font-size: 9pt;
            color: #64748b;
            margin-bottom: 4px;
        }
        .kpi-value {
            font-size: 16pt;
            font-weight: bold;
            color: #1e293b;
        }
        .kpi-value.green { color: #059669; }
        .kpi-value.red { color: #dc2626; }

        /* Distribution list */
        .dist-list {
            margin-top: 8px;
            font-size: 9pt;
            color: #475569;
        }
        .dist-list li {
            margin-bottom: 3px;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        th {
            background-color: #f1f5f9;
            font-weight: bold;
            font-size: 9.5pt;
            color: #334155;
        }
        td {
            font-size: 10pt;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 8.5pt;
            font-weight: bold;
        }
        .badge.good { background: #dcfce7; color: #059669; }
        .badge.warning { background: #fef9c3; color: #a16207; }
        .badge.poor { background: #fee2e2; color: #dc2626; }

        .footer-note {
            margin-top: 25px;
            padding: 12px;
            background: #eff6ff;
            border-radius: 6px;
            font-size: 9pt;
            color: #1e40af;
            border-left: 3px solid #3b82f6;
        }

        @page {
            margin: 20mm;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Laporan dan Ekspor Data</h1>
            <p>Visualisasi dan penyediaan data akhir</p>
        </div>

        @if($appliedFilters['siklus'] || $appliedFilters['divisi'])
            <div class="filters">
                Filter aktif:
                @if($appliedFilters['siklus'])
                    Siklus: {{ $siklusList->firstWhere('id', $appliedFilters['siklus'])?->nama ?: '–' }}
                @endif
                @if($appliedFilters['divisi'])
                    @if($appliedFilters['siklus']) • @endif
                    Divisi: {{ $divisionsList->firstWhere('id', $appliedFilters['divisi'])?->name ?: '–' }}
                @endif
            </div>
        @endif

        <!-- Grafik (ganti dengan teks terstruktur) -->
        <div class="section">
            <div class="section-title">Grafik Distribusi Skor</div>
            <p class="section-desc">Sebaran frekuensi skor di perusahaan</p>
            <ul class="dist-list">
                @foreach(['Skor 1' => 1, 'Skor 2' => 2, 'Skor 3' => 3, 'Skor 4' => 4, 'Skor 5' => 5] as $label => $score)
                    @php
                        $count = $allData->where('skor', $score)->count();
                    @endphp
                    <li>{{ $label }}: <strong>{{ $count }}</strong> pegawai</li>
                @endforeach
            </ul>
        </div>

        <!-- KPI -->
        @php
            $penilaianArray = $allData->unique(function ($item) {
                return $item['siklus_id'] . '_' . $item['division_id'] . '_' . $item['skor_akhir'];
            });
            $total = $penilaianArray->count();
            $avg = $total ? number_format($penilaianArray->avg('skor_akhir'), 1) : 0;
            $sangatBaik = $penilaianArray->where('skor_akhir', '>=', 4.0)->count();
            $perluPerbaikan = $penilaianArray->where('skor_akhir', '<', 2.5)->count();
        @endphp

        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-label">Total Pegawai Dinilai</div>
                <div class="kpi-value">{{ $total }}</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">Rata-rata Skor</div>
                <div class="kpi-value {{ $avg >= 4.0 ? 'green' : ($avg < 2.5 ? 'red' : '') }}">{{ $avg }}</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">Sangat Baik (≥4.0)</div>
                <div class="kpi-value green">{{ $sangatBaik }}</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">Perlu Perbaikan (<2.5)</div>
                <div class="kpi-value red">{{ $perluPerbaikan }}</div>
            </div>
        </div>

        <!-- Tabel Divisi -->
        <div class="section">
            <div class="section-title">Tabel Rata-rata Divisi</div>
            <p class="section-desc">Ringkasan kinerja per divisi (diurutkan dari tertinggi ke terendah)</p>

            @php
                $divisiMap = [];
                foreach ($penilaianArray as $item) {
                    if (!$item['division_id']) continue;
                    $id = $item['division_id'];
                    if (!isset($divisiMap[$id])) {
                        $divisiMap[$id] = ['nama' => $item['division_nama'], 'count' => 0, 'total' => 0];
                    }
                    $divisiMap[$id]['count']++;
                    $divisiMap[$id]['total'] += $item['skor_akhir'];
                }
                $divisiList = [];
                foreach ($divisiMap as $d) {
                    $avgDiv = $d['total'] / $d['count'];
                    $divisiList[] = [
                        'nama' => $d['nama'],
                        'count' => $d['count'],
                        'avg' => number_format($avgDiv, 1),
                        'kategori' => $avgDiv >= 4.0 ? ['Sangat Baik', 'good'] :
                                     ($avgDiv >= 3.5 ? ['Baik', 'good'] :
                                     ($avgDiv >= 3.0 ? ['Cukup Baik', 'warning'] : ['Perlu Perbaikan', 'poor']))
                    ];
                }
                usort($divisiList, fn($a, $b) => $b['avg'] <=> $a['avg']);
            @endphp

            @if(empty($divisiList))
                <p style="text-align: center; color: #64748b; font-style: italic;">Tidak ada data penilaian yang selesai.</p>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Divisi</th>
                            <th>Jumlah Pegawai</th>
                            <th>Rata-rata Skor</th>
                            <th>Kategori</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($divisiList as $div)
                            <tr>
                                <td>{{ $div['nama'] }}</td>
                                <td>{{ $div['count'] }}</td>
                                <td>
                                    <span style="font-weight: bold; {{ $div['kategori'][1] === 'good' ? 'color: #059669;' : ($div['kategori'][1] === 'poor' ? 'color: #dc2626;' : 'color: #334155;') }}">
                                        {{ $div['avg'] }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $div['kategori'][1] }}">{{ $div['kategori'][0] }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="footer-note">
            Laporan ini membantu Eksekutif dan HR membandingkan kinerja tim secara makro.
            @if($appliedFilters['siklus'] || $appliedFilters['divisi'])
                Filter aktif – data ditampilkan sesuai filter yang dipilih.
            @endif
        </div>
    </div>
</body>
</html>