<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Şifre Sıfırlama İsteği</title>
    <style>
        body {
            font-family: 'Inter', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f5f8f7; /* background-light */
            color: #3f3f46; /* secondary-700 */
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        .email-wrapper {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            border: 1px solid #e4e4e7; /* secondary-200 */
        }
        .header {
            background-color: #ffffff;
            padding: 30px;
            text-align: center;
            border-bottom: 2px solid #f4f4f5; /* secondary-100 */
        }
        .header img {
            max-height: 60px;
            width: auto;
        }
        .content {
            padding: 40px 30px;
            line-height: 1.6;
            font-size: 16px;
            color: #52525b; /* secondary-600 */
        }
        h1 {
            font-size: 24px;
            font-weight: 600;
            color: #18181b; /* secondary-900 */
            margin-top: 0;
            margin-bottom: 20px;
            text-align: center;
        }
        .button-container {
            text-align: center;
            margin: 35px 0;
        }
        .button {
            display: inline-block;
            padding: 14px 32px;
            background-color: #006840; /* primary-500 */
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
            transition: background-color 0.2s;
        }
        .button:hover {
            background-color: #017746; /* primary-600 */
        }
        .footer {
            background-color: #f5f8f7; /* background-light */
            padding: 24px 30px;
            text-align: center;
            font-size: 14px;
            color: #71717a; /* secondary-500 */
            border-top: 1px solid #e4e4e7; /* secondary-200 */
        }
        .security-notice {
            margin-top: 30px;
            padding: 15px;
            background-color: #f0f9ff; /* primary-50 */
            border-left: 4px solid #19a70c; /* brand-teal */
            border-radius: 4px;
            font-size: 14px;
            color: #005539; /* primary-700 */
        }
        .help-text {
            font-size: 13px;
            color: #71717a; /* secondary-500 */
            margin-top: 30px;
            word-break: break-all;
        }
        .help-text a {
            color: #19a70c; /* brand-teal */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="email-wrapper">
            <div class="header">
                <img src="{{ $message->embed(public_path('assets/images/perfas-light.png')) }}" alt="Perfas">
            </div>
            
            <div class="content">
                <h1>Şifre Sıfırlama İsteği</h1>
                
                <p>Merhaba <strong>{{ $user->name }}</strong>,</p>
                
                <p>Hesabınız için bir şifre sıfırlama talebi aldık. Aşağıdaki butona tıklayarak yeni bir şifre belirtebilirsiniz.</p>
                
                <div class="button-container">
                    <a href="{{ $url }}" class="button" target="_blank">Şifremi Sıfırla</a>
                </div>
                
                <p>Şifre sıfırlama bağlantısının süresi yakında dolacaktır, bu nedenle işleminizi en kısa sürede tamamlamanızı öneririz.</p>
                
                <div class="security-notice">
                    <strong>Bilgilendirme:</strong> Eğer şifre sıfırlama talebinde siz bulunmadıysanız, herhangi bir işlem yapmanıza gerek yoktur ve hesabınız güvendedir.
                </div>

                <div class="help-text">
                    "Şifremi Sıfırla" butonuna tıklamada sorun yaşıyorsanız, aşağıdaki bağlantıyı kopyalayıp web tarayıcınıza yapıştırabilirsiniz: <br>
                    <a href="{{ $url }}">{{ $url }}</a>
                </div>
            </div>
            
            <div class="footer">
                <p>&copy; {{ date('Y') }} PPAS. Tüm hakları saklıdır.</p>
            </div>
        </div>
    </div>
</body>
</html>
