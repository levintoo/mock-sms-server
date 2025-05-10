<?php

namespace App\Jobs;

use App\Enums\MessageStatus;
use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class MockMessageDeliveryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected Message $message)
    {
    }

    public function handle(): void
    {
        $this->message->update([
            'status' => MessageStatus::Delivered,
            'delivered_at' => now(),
        ]);

        PollDeliveryWebhookJob::dispatch($this->message, 'delivered');
    }

    public function failed(): void
    {
        //
    }
}
