<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title', 'PCT Mail')</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f7fa; color: #1f2937; }
        .card { max-width: 620px; margin: 24px auto; background: #fff; border-radius: 12px; padding: 24px; }
        .brand { font-weight: 700; color: #0a3d91; font-size: 20px; }
        .footer { font-size: 12px; color: #6b7280; margin-top: 24px; }
    </style>
</head>
<body>
<div class="card">
    <div class="brand">PCT • Mail Transport</div>
    @yield('content')
    <div class="footer">
        Mensagem automática do ecossistema PCT. Não responda este e-mail.
    </div>
</div>
</body>
</html>
