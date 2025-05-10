<?php

namespace App\Jobs;

use App\Models\Message;
use Carbon\Carbon;
use Http;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PollDeliveryWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Message $message,
        public string $status, // 'sent', 'delivered', 'failed'
        public int     $attempt = 1,
        public ?Carbon $started_at = null,
    )
    {
        $this->started_at ??= now();
    }

    public function handle(): void
    {
        $webhookUrl = config('services.sms.webhook');

        try {
            $response = Http::timeout(5)->asForm()->post($webhookUrl, [
                'event' => 'message'.$this->status,
                'data' => [
                    'to' => $this->message->to,
                    'message' => $this->message,
                ],
            ]);

            if ($response->successful()) {
                return;
            }
        } catch (\Throwable $e) {
            Log::warning("Webhook failed for message ID {$this->message->id}", [
                'error' => $e->getMessage(),
            ]);
        }

        if (now()->diffInHours($this->started_at) >= 24) {
            return;
        }

        $nextDelay = match (true) {
            $this->attempt <= 12 => 5,      // Every 5s for 1 min
            $this->attempt <= 17 => 60,     // Every 1 min for 5 min
            $this->attempt <= 29 => 300,    // Every 5 min for 55 min
            default            => 3600,     // Then hourly
        };

        self::dispatch($this->message, $this->status, $this->attempt + 1, $this->started_at)
            ->delay(now()->addSeconds($nextDelay));
    }

    public function failed(): void
    {
        //
    }
}
