<?php
namespace App\Events;

    use App\Models\ChatMessage;
    use Illuminate\Broadcasting\Channel;
    use Illuminate\Broadcasting\InteractsWithSockets;
    use Illuminate\Broadcasting\PresenceChannel;
    use Illuminate\Broadcasting\PrivateChannel;
    use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
    use Illuminate\Foundation\Events\Dispatchable;
    use Illuminate\Queue\SerializesModels;

    // 1. Tambahkan "implements ShouldBroadcast"
    class ChatMessageSent implements ShouldBroadcast
    {
        use Dispatchable, InteractsWithSockets, SerializesModels;

        // 2. Buat properti publik untuk menampung data pesan
        public ChatMessage $message;

        /**
         * Create a new event instance.
         */
        public function __construct(ChatMessage $message)
        {
            $this->message = $message;
        }

        /**
         * Get the channels the event should broadcast on.
         *
         * @return array<int, \Illuminate\Broadcasting\Channel>
         */
        public function broadcastOn(): array
        {
            // 3. Siarkan event ini di channel privat yang spesifik untuk setiap sesi konsultasi
            return [
                new PrivateChannel('consultation.' . $this->message->consultation_id),
            ];
        }
    }
    