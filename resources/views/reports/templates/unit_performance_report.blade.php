<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Birim Performans Raporu - {{ $unit->name ?? 'Birim' }}</title>
    <style>
        @page { margin: 0cm 0cm; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; background-color: #f8fafc; color: #1e293b; margin: 1cm; line-height: 1.5; }
        .header { background-color: #1e40af; color: white; padding: 40px; margin: -1cm -1cm 40px -1cm; }
        .header h1 { margin: 0; font-size: 24px; font-weight: bold; }
        .card { background: white; border-radius: 12px; padding: 20px; margin-bottom: 20px; border: 1px solid #e2e8f0; }
        .card-title { font-size: 15px; font-weight: bold; color: #334155; margin-bottom: 15px; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; font-size: 11px; text-transform: uppercase; color: #64748b; padding: 8px; background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; }
        td { padding: 10px 8px; font-size: 12px; border-bottom: 1px solid #f1f5f9; }
        .bar-container { width: 100px; height: 8px; background: #f1f5f9; border-radius: 4px; overflow: hidden; display: inline-block; vertical-align: middle; }
        .bar-fill { height: 100%; background: #3b82f6; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; height: 1cm; background: #f1f5f9; text-align: center; line-height: 1cm; font-size: 9px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Birim Performans Analizi</h1>
        <p>{{ $unit->name ?? 'Tüm Birimler' }} | Toplam {{ $summaries->unique('username')->count() }} Kullanıcı</p>
    </div>

    <div class="card">
        <div class="card-title">Kullanıcı Bazlı Performans Özeti</div>
        <table>
            <thead>
                <tr>
                    <th>Kullanıcı</th>
                    <th>İş Saati</th>
                    <th>Diğer</th>
                    <th>Aktivite</th>
                </tr>
            </thead>
            <tbody>
                @foreach($summaries->groupBy('username') as $user => $userSummaries)
                @php
                    $workHours = $userSummaries->where('category_type', 'work')->sum('total_duration_ms') / (1000 * 60 * 60);
                    $otherHours = $userSummaries->where('category_type', 'other')->sum('total_duration_ms') / (1000 * 60 * 60);
                @endphp
                <tr>
                    <td><b>{{ $user }}</b></td>
                    <td>{{ round($workHours, 1) }} Saat</td>
                    <td>{{ round($otherHours, 1) }} Saat</td>
                    <td>{{ number_format($userSummaries->sum('activity_count')) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        Bu rapor PPAS tarafından {{ date('d.m.Y H:i') }} tarihinde oluşturulmuştur.
    </div>
</body>
</html>
