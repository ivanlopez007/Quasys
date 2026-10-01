<?php

namespace App\Mail;

use App\Models\CambioDocumento;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SolicitudResueltaMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public CambioDocumento $solicitud,
        public bool $aprobada,
    ) {}

    public function envelope(): Envelope
    {
        $estado = $this->aprobada ? 'aprobada' : 'rechazada';

        return new Envelope(
            subject: "Solicitud {$estado}: {$this->solicitud->codigo_documento} v{$this->solicitud->version}",
        );
    }

    public function content(): Content
    {
        $solicitud = $this->solicitud;
        $nombre = fn ($u) => $u
            ? ($u->informacion ? trim($u->informacion->nombre . ' ' . $u->informacion->apellidos) : $u->email)
            : '—';

        $resultado = match (true) {
            !$this->aprobada => 'La solicitud fue rechazada. Si corresponde, realiza los ajustes y registra una nueva solicitud.',
            $solicitud->esEliminacion() => 'El documento fue dado de baja y ya no forma parte de la Lista Maestra. El archivo se conserva durante su periodo de retención.',
            $solicitud->esRevision() => "La versión {$solicitud->version} ya está vigente; la versión anterior quedó obsoleta.",
            default => 'El documento ya está publicado y vigente en la Lista Maestra.',
        };

        return new Content(
            view: 'emails.solicitud_resuelta',
            with: [
                'solicitanteNombre' => $nombre($solicitud->solicitante),
                'aprobadorNombre' => $nombre($solicitud->aprobador),
                'resultado' => $resultado,
                'urlSolicitudes' => route('documentos.solicitudes.index'),
                'urlHistorial' => $this->aprobada && $solicitud->codigo_documento
                    ? route('documentos.historial', $solicitud->codigo_documento)
                    : null,
            ],
        );
    }
}
