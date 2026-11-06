<?php

namespace App\Notifications;

use App\Models\Anuncio;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AnuncioCreado extends Notification
{
    use Queueable;

    public $anuncio;

    /**
     * Create a new notification instance.
     */
    public function __construct(Anuncio $anuncio)
    {
        $this->anuncio = $anuncio;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'anuncio_id' => $this->anuncio->id,
            'titulo' => $this->anuncio->titulo,
            'message' => 'Se ha creado un anuncio que requiere aprobación: ' . $this->anuncio->titulo,
            'url' => route('anuncios.index')
        ];
    }
}
