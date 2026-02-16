<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $report->title }}</title>
    <style>
        @page {
            margin: 0cm 0cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            margin: 1cm;
            line-height: 1.5;
        }
        .header {
            background-color: #1e3a8a;
            color: white;
            padding: 40px;
            margin: -1cm -1cm 40px -1cm;
            position: relative;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
            letter-spacing: -0.5px;
        }
        .header p {
            margin: 10px 0 0 0;
            opacity: 0.8;
            font-size: 14px;
        }
        .container {
            width: 100%;
        }
        .card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
        }
        .card-title {
            font-size: 16px;
            font-weight: bold;
            color: #334155;
            margin-bottom: 20px;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
            color: #64748b;
            padding: 12px 8px;
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }
        td {
            padding: 14px 8px;
            font-size: 13px;
            border-bottom: 1px solid #f1f5f9;
        }
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }
        .badge-work { background-color: #dcfce7; color: #166534; }
        .badge-other { background-color: #f1f5f9; color: #475569; }
        .badge-untagged { background-color: #fee2e2; color: #991b1b; }
        .footer {
            position: fixed;
            bottom: 0cm;
            left: 0cm;
            right: 0cm;
            height: 1cm;
            background-color: #f1f5f9;
            text-align: center;
            line-height: 1cm;
            font-size: 10px;
            color: #94a3b8;
        }
        .summary-grid {
            margin-bottom: 30px;
            overflow: hidden;
        }
        .summary-item {
            float: left;
            width: 25%;
            text-align: center;
        }
        .summary-val {
            font-size: 24px;
            font-bold: true;
            color: #1e3a8a;
        }
        .summary-label {
            font-size: 11px;
            color: #64748b;
            margin-top: 5px;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>PPAS Performans Raporu</h1>
        <p>{{ $report->title }} | Oluşturulma: {{ $generated_at->format('d.m.Y H:i') }}</p>
    </div>

    <div class="container">
        <div class="card clearfix">
            <div class="card-title">Genel Özet</div>
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="summary-val">{{ $summaries->where('category_type', 'work')->sum('total_duration_ms') / (1000 * 60 * 60) }}</div>
                    <div class="summary-label">İş (Saat)</div>
                </div>
                <div class="summary-item">
                    <div class="summary-val">{{ $summaries->where('category_type', 'other')->sum('total_duration_ms') / (1000 * 60 * 60) }}</div>
                    <div class="summary-label">Diğer (Saat)</div>
                </div>
                <div class="summary-item">
                    <div class="summary-val">{{ $summaries->where('category_type', 'untagged')->sum('total_duration_ms') / (1000 * 60 * 60) }}</div>
                    <div class="summary-label">Tanımsız (Saat)</div>
                </div>
                 <div class="summary-item">
                    <div class="summary-val">{{ number_format($summaries->sum('activity_count')) }}</div>
                    <div class="summary-label">Toplam Aktivite</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-title">Günlük Detaylar</div>
            <table>
                <thead>
                    <tr>
                        <th>Tarih</th>
                        <th>Kullanıcı</th>
                        <th>Tür</th>
                        <th>Süre (Saat)</th>
                        <th>Aktivite</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($summaries->sortByDesc('date')->take(50) as $summary)
                    <tr>
                        <td>{{ $summary->date->format('d.m.Y') }}</td>
                        <td>{{ $summary->username }}</td>
                        <td>
                            <span class="badge badge-{{ $summary->category_type }}">
                                {{ strtoupper($summary->category_type) }}
                            </span>
                        </td>
                        <td>{{ round($summary->total_duration_ms / (1000 * 60 * 60), 2) }}</td>
                        <td>{{ number_format($summary->activity_count) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="footer">
        © {{ date('Y') }} PPAS - Perfas Performans Analiz Sistemi. Bu rapor otomatik olarak oluşturulmuştur.
    </div>
</body>
</html>
