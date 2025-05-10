<?php

namespace App\Http\Controllers;

use App\Enums\MessageStatus;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Resources\MessageResource;
use App\Jobs\MockMessageDeliveryJob;
use App\Jobs\MockMessageSendJob;
use App\Models\Message;

class MessageController extends Controller
{
    public function index()
    {
        return MessageResource::collection(Message::latest()->get());
    }

    public function store(StoreMessageRequest $request)
    {
        $data = [
            ...$request->validated(),
            'status' => MessageStatus::Sent,
            'sent_at' => now(),
        ];

        if (is_array($to = $data['to'] ?? null)) {
            $messages = collect();

            foreach ($to as $recipient) {
                $message = Message::create([
                    ...$data,
                    'status' => MessageStatus::Queued,
                    'to' => $recipient,
                    'sent_at' => null,
                ]);

                $messages->push($message);

                MockMessageSendJob::dispatch($message);
            }

            return MessageResource::collection($messages);
        }

        $message = Message::create($data);
        MockMessageDeliveryJob::dispatch($message)->delay(now()->addSeconds(rand(3, 20)));
        return new MessageResource($message);
    }

    public function show(Message $message)
    {
        return new MessageResource($message);
    }

    public function destroy(Message $message)
    {
        $message->delete();

        return response()->json();
    }
}
