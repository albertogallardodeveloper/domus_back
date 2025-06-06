<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Verificación de correo Domus</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 20px;">
  <div style="max-width: 600px; margin: auto; background-color: #ffffff; padding: 20px; border-radius: 8px;">
    <h2 style="color: #004aad;">¡Hola, {{ $name }}!</h2>
    <p>Gracias por registrarte en Domus. Tu código de verificación es:</p>
    <p style="font-size: 24px; font-weight: bold; color: #eec494; letter-spacing: 4px; margin: 20px 0;">
      {{ $code }}
    </p>
    <p>Introduce este código en la app para confirmar tu correo. Si no fuiste tú quien solicitó este código, ignora este correo.</p>
  </div>
</body>
</html>
