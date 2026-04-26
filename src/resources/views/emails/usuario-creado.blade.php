<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bienvenido a Liberxo</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h2 style="color: #1e3a8a;">Bienvenido a Liberxo</h2>

    <p>Hola {{ $user->nombre }},</p>

    <p>Se ha creado una cuenta para ti en el <strong>Sistema de Gestión de Clientes y Expedientes de Liberxo</strong>.</p>

    <p>Estos son tus datos de acceso:</p>

    <table style="border-collapse: collapse; margin: 20px 0; background: #f3f4f6; border-radius: 8px; width: 100%;">
        <tr>
            <td style="padding: 12px 16px; border-bottom: 1px solid #e5e7eb;"><strong>Correo:</strong></td>
            <td style="padding: 12px 16px; border-bottom: 1px solid #e5e7eb; font-family: monospace;">{{ $user->email }}</td>
        </tr>
        <tr>
            <td style="padding: 12px 16px;"><strong>Contraseña temporal:</strong></td>
            <td style="padding: 12px 16px; font-family: monospace; font-size: 16px;"><code>{{ $password }}</code></td>
        </tr>
    </table>

    <p>Te recomendamos cambiar tu contraseña al iniciar sesión por primera vez. Puedes hacerlo desde los ajustes de tu perfil o mediante el enlace "¿Has olvidado tu contraseña?" de la pantalla de acceso.</p>

    <p style="margin: 28px 0;">
        <a href="{{ $loginUrl }}" style="background: #1e3a8a; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;">
            Acceder al sistema
        </a>
    </p>

    <p style="font-size: 12px; color: #6b7280; border-top: 1px solid #e5e7eb; padding-top: 16px;">
        Si no esperabas este correo, ignóralo o contacta con el administrador del sistema.
    </p>
</body>
</html>
