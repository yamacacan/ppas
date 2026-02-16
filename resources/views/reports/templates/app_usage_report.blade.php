<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Uygulama Kullanım Raporu</title>
    <style>
        @page { margin: 0cm 0cm; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; background-color: #f8fafc; color: #1e293b; margin: 1cm; line-height: 1.5; }
        .header { background-color: #4f46e5; color: white; padding: 40px; margin: -1cm -1cm 40px -1cm; }
        .header h1 { margin: 0; font-size: 24px; font-weight: bold; }
        .card { background: white; border-radius: 12px; padding: 20px; margin-bottom: 20px; border: 1px solid #e2e8f0; }
        .card-title { font-size: 15px; font-weight: bold; color: #334155; margin-bottom: 15px; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; font-size: 11px; text-transform: uppercase; color: #64748b; padding: 8px; background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; }
        td { padding: 10px 8px; font-size: 12px; border-bottom: 1px solid #f1f5f9; }
        .app-name { font-weight: bold; color: #4338ca; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; height: 1cm; background: #f1f5f9; text-align: center; line-height: 1cm; font-size: 9px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Uygulama Kullanım Analizi</h1>
        <p>En Çok Vakit Geçirilen Uygulamalar ve Süreçler</p>
    </div>

    <div class="card">
        <div class="card-title">Top 20 Uygulama Listesi</div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Uygulama Adı</th>
                    <th>Toplam Süre (Saat)</th>
                    <th>Aktivite Sayısı</th>
                </tr>
            </thead>
            <tbody>
                @foreach($processes as $index => $proc)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="app-name">{{ $proc->process_name }}</td>
                    <td>{{ round($proc->total_duration / (1000 * 60 * 60), 2) }} Saat</td>
                    <td>{{ number_format($proc->activity_count) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        Bu rapor PPAS Uygulama Envanter Takibi tarafından oluşturulmuştur.
    </div>
</body>
</html>
