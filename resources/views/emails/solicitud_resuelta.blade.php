@php
    // Los correos necesitan estilos en línea: los clientes de correo no cargan Tailwind.
    $color = $aprobada ? '#059669' : '#e11d48';
    $fondo = $aprobada ? '#ecfdf5' : '#fff1f2';
    $estado = $aprobada ? 'APROBADA' : 'RECHAZADA';
    $fila = 'padding:8px 0;border-bottom:1px solid #f1f5f9;font-size:13px;';
    $etiqueta = $fila . 'color:#64748b;width:38%;';
    $valor = $fila . 'color:#0f172a;font-weight:600;';
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Solicitud {{ strtolower($estado) }}</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:Segoe UI,Helvetica,Arial,sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:32px 12px;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e2e8f0;">
                <tr>
                    <td style="background:#1e293b;padding:20px 28px;">
                        <p style="margin:0;color:#94a3b8;font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;">Control de documentos</p>
                        <p style="margin:4px 0 0;color:#ffffff;font-size:18px;font-weight:800;">Tu solicitud fue resuelta</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:28px;">
                        <p style="margin:0 0 16px;font-size:14px;color:#334155;">Hola {{ $solicitanteNombre }},</p>

                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:{{ $fondo }};border-radius:12px;margin-bottom:20px;">
                            <tr>
                                <td style="padding:14px 18px;">
                                    <span style="display:inline-block;background:{{ $color }};color:#ffffff;font-size:11px;font-weight:800;letter-spacing:1px;padding:4px 10px;border-radius:999px;">{{ $estado }}</span>
                                    <p style="margin:10px 0 0;font-size:13px;color:#334155;">{{ $resultado }}</p>
                                </td>
                            </tr>
                        </table>

                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                            <tr><td style="{{ $etiqueta }}">Tipo de solicitud</td><td style="{{ $valor }}">{{ $solicitud->tipoSolicitud?->tipo_solicitud ?? '—' }}</td></tr>
                            <tr><td style="{{ $etiqueta }}">Código</td><td style="{{ $valor }}font-family:Consolas,monospace;">{{ $solicitud->codigo_documento }}</td></tr>
                            <tr><td style="{{ $etiqueta }}">Documento</td><td style="{{ $valor }}">{{ $solicitud->nombre_documento }}</td></tr>
                            <tr><td style="{{ $etiqueta }}">Versión</td><td style="{{ $valor }}">v{{ $solicitud->version }}</td></tr>
                            <tr><td style="{{ $etiqueta }}">Solicitada el</td><td style="{{ $valor }}">{{ $solicitud->fecha_solicitud?->format('d/m/Y H:i') }}</td></tr>
                            <tr><td style="{{ $etiqueta }}">{{ $aprobada ? 'Aprobada por' : 'Rechazada por' }}</td><td style="{{ $valor }}">{{ $aprobadorNombre }}</td></tr>
                            <tr><td style="{{ $etiqueta }}">Fecha de resolución</td><td style="{{ $valor }}">{{ $solicitud->fecha_aprobacion?->format('d/m/Y H:i') }}</td></tr>
                        </table>

                        @if ($solicitud->comentario_aprobador)
                            <p style="margin:20px 0 6px;font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#64748b;">{{ $aprobada ? 'Comentario' : 'Motivo del rechazo' }}</p>
                            <p style="margin:0;padding:12px 16px;background:#f8fafc;border-left:3px solid {{ $color }};border-radius:6px;font-size:13px;color:#334155;white-space:pre-line;">{{ $solicitud->comentario_aprobador }}</p>
                        @endif

                        <table role="presentation" cellpadding="0" cellspacing="0" style="margin-top:24px;">
                            <tr>
                                @if ($urlHistorial)
                                    <td style="padding-right:8px;">
                                        <a href="{{ $urlHistorial }}" style="display:inline-block;background:#0f172a;color:#ffffff;text-decoration:none;font-size:12px;font-weight:700;padding:10px 18px;border-radius:10px;">Ver documento</a>
                                    </td>
                                @endif
                                <td>
                                    <a href="{{ $urlSolicitudes }}" style="display:inline-block;background:#ffffff;color:#0f172a;text-decoration:none;font-size:12px;font-weight:700;padding:9px 17px;border-radius:10px;border:1px solid #cbd5e1;">Ver mis solicitudes</a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding:16px 28px;background:#f8fafc;border-top:1px solid #e2e8f0;">
                        <p style="margin:0;font-size:11px;color:#94a3b8;">Correo automático del sistema de gestión documental. No respondas a este mensaje.</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
