<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kullanıcı Detay Raporu - {{ $user->name ?? $summaries->first()->username ?? 'Bilinmiyor' }}</title>
    <style>
        @page { margin: 0cm 0cm; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; background-color: #f8fafc; color: #1e293b; margin: 1cm; line-height: 1.5; }
        .header { background-color: #0f172a; color: white; padding: 40px; margin: -1cm -1cm 40px -1cm; }
        .header h1 { margin: 0; font-size: 24px; font-weight: bold; }
        .header p { margin: 5px 0 0 0; opacity: 0.7; font-size: 13px; }
        .card { background: white; border-radius: 12px; padding: 20px; margin-bottom: 20px; border: 1px solid #e2e8f0; }
        .card-title { font-size: 15px; font-weight: bold; color: #334155; margin-bottom: 15px; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px; }
        .profile-info { margin-bottom: 20px; }
        .profile-info b { color: #64748b; font-size: 11px; text-transform: uppercase; display: block; }
        .profile-info span { font-size: 16px; font-weight: bold; color: #0f172a; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; font-size: 11px; text-transform: uppercase; color: #64748b; padding: 8px; background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; }
        td { padding: 10px 8px; font-size: 12px; border-bottom: 1px solid #f1f5f9; }
        .badge { padding: 3px 6px; border-radius: 4px; font-size: 10px; font-weight: bold; }
        .badge-work { background-color: #dcfce7; color: #166534; }
        .badge-other { background-color: #f1f5f9; color: #475569; }
        .badge-untagged { background-color: #fee2e2; color: #991b1b; }
        .summary-stats { overflow: hidden; margin-bottom: 20px; }
        .stat-box { float: left; width: 33.33%; text-align: center; }
        .stat-val { font-size: 20px; font-weight: bold; color: #3b82f6; }
        .stat-label { font-size: 10px; color: #64748b; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; height: 1cm; background: #f1f5f9; text-align: center; line-height: 1cm; font-size: 9px; color: #94a3b8; }
        .clearfix::after { content: ""; clear: both; display: table; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Kullanıcı Performans Analizi</h1>
        <p>{{ $user->name ?? 'Bilinmeyen Kullanıcı' }} ({{ $user->username ?? '...' }}) | {{ $generated_at->format('d.m.Y') }}</p>
    </div>

    <div class="card">
        <div class="card-title">Kullanıcı Bilgileri</div>
        <div class="row clearfix">
            <div class="stat-box" style="text-align: left; width: 50%;">
                <div class="profile-info">
                    <b>Ad Soyad</b>
                    <span>{{ $user->name ?? 'Bilinmiyor' }}</span>
                </div>
            </div>
            <div class="stat-box" style="text-align: left; width: 50%;">
                <div class="profile-info">
                    <b>Birim / Departman</b>
                    <span>{{ $user->unit->name ?? 'Belirtilmemiş' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card clearfix">
        <div class="card-title">Zaman Dağılım Özeti</div>
        <div class="summary-stats">
            <div class="stat-box">
                <div class="stat-val">{{ round($summaries->where('category_type', 'work')->sum('total_duration_ms') / (1000 * 60 * 60), 1) }}</div>
                <div class="stat-label">Toplam İş Saati</div>
            </div>
            <div class="stat-box">
                <div class="stat-val">{{ round($summaries->where('category_type', 'other')->sum('total_duration_ms') / (1000 * 60 * 60), 1) }}</div>
                <div class="stat-label">Diğer Süre</div>
            </div>
            <div class="stat-box">
                <div class="stat-val">{{ round($summaries->where('category_type', 'untagged')->sum('total_duration_ms') / (1000 * 60 * 60), 1) }}</div>
                <div class="stat-label">Tanımsız Süre</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-title">Günlük Performans Özeti</div>
        <table>
            <thead>
                <tr>
                    <th>Tarih</th>
                    <th>İş Saati</th>
                    <th>Diğer</th>
                    <th>Tanımsız</th>
                    <th>Toplam</th>
                    <th>Aktivite</th>
                </tr>
            </thead>
            <tbody>
                @foreach($daily_summaries as $day)
                <tr>
                    <td>{{ $day['date']->format('d.m.Y') }}</td>
                    <td style="color: #166534; font-weight: bold;">{{ round($day['work_ms'] / (1000 * 60 * 60), 2) }} Saat</td>
                    <td style="color: #475569;">{{ round($day['other_ms'] / (1000 * 60 * 60), 2) }} Saat</td>
                    <td style="color: #991b1b;">{{ round($day['untagged_ms'] / (1000 * 60 * 60), 2) }} Saat</td>
                    <td style="font-weight: bold;">{{ round($day['total_ms'] / (1000 * 60 * 60), 2) }} Saat</td>
                    <td>{{ number_format($day['activity_count']) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        Bu rapor PPAS Performans Takip Sistemi tarafından otomatik olarak oluşturulmuştur.
    </div>
</body>
</html>
