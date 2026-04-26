<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UsuarioCreadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public string $passwordPlano)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bienvenido a Liberxo - Tus credenciales de acceso',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.usuario-creado',
            with: [
                'user' => $this->user,
                'password' => $this->passwordPlano,
                'loginUrl' => route('login'),
            ],
        );
    }
}
