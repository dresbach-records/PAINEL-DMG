<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title', 'PCT Mail')</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f7fa; color: #1f2937; }
        .card { max-width: 620px; margin: 24px auto; background: #fff; border-radius: 12px; padding: 24px; }
        .brand { display:flex; align-items:center; gap:10px; font-weight: 700; color: #0a3d91; font-size: 20px; }
        .brand img { height: 36px; }
        .brand-logo { width: 100%; max-width: 240px; margin: 16px 0; }
        .footer { font-size: 12px; color: #6b7280; margin-top: 24px; }
    </style>
</head>
<body>
<div class="card">
    <div class="brand">
        <img src="{{ config('pct-mail.branding.icon_url', '/branding/pct/pct-icon.svg') }}" alt="PCT icon">
        <span>PCT • Mail Transport</span>
    </div>
    <img class="brand-logo" src="{{ config('pct-mail.branding.logo_url', '/branding/pct/pct-logo.svg') }}" alt="PCT logo">
    @yield('content')
    <div class="footer">
        Mensagem automática do ecossistema PCT. Não responda este e-mail.
    </div>
</div>
</body>
</html>
