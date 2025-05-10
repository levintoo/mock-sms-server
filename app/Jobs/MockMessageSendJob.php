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

class MockMessageSendJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected Message $message)
    {
    }

    public function handle(): void
    {
        $this->message->update([
           'status' => MessageStatus::Sent,
            'sent_at' => now(),
        ]);
        PollDeliveryWebhookJob::dispatch($this->message, 'sent');
        MockMessageDeliveryJob::dispatch($this->message)->delay(now()->addSeconds(rand(3, 20)));
    }

    public function failed(): void
    {
        try {
            $this->message->update([
                'status' => MessageStatus::Failed,
                'failed_at' => now(),
            ]);
        } catch(Throwable $throwable) {
            // do nothing
        }
    }
}
