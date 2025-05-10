<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\Message;

class MessageController extends Controller
{
    public function index()
    {
        return MessageResource::collection(Message::all());
    }

    public function store(StoreMessageRequest $request)
    {
        $data = $request->validated();

        return new MessageResource(Message::create($data));
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
