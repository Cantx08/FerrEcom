<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Producto;

class BajoStockNotification extends Notification
{
    use Queueable;

    protected $producto;

    /**
     * Create a new notification instance.
     */
    public function __construct(Producto $producto)
    {
        $this->producto = $producto;
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
            'icon' => 'fa-solid fa-triangle-exclamation',
            'title' => 'Bajo Stock',
            'message' => 'El producto "' . $this->producto->nombre . '" está bajo su nivel mínimo (' . $this->producto->stock_actual . ').',
            'url' => route('productos.index', ['low_stock' => 1]),
            'color' => 'red'
        ];
    }
}
