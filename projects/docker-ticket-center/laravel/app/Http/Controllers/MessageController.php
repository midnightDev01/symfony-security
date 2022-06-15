<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Models\Attachment;
use App\Models\Message;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use MongoDB\BSON\Binary;

class MessageController extends Controller
{
    /**
     * Stores a message
     * URL: api/messages
     * Method: POST
     *
     * @param StoreMessageRequest $request
     * @return JsonResponse
     */
    public function store(StoreMessageRequest $request): JsonResponse
    {
        $ticket = Ticket::findOrFail($request->get('ticket_id'));

        $message = new Message($request->only(['content', 'owner', 'internal']));

        $success = $ticket->messages()->save(
            $message
        );

        if ($request->hasFile('attachment')) {
            $attachments = [];
            foreach ($request->file('attachment') as $file) {
                $attachments[] = [
                    'filename'   => $file->getClientOriginalName(),
                    'filetype'   => $file->getClientMimeType(),
                    'attachment' => new Binary(file_get_contents($file->getRealPath()), Binary::TYPE_GENERIC),
                ];
            }

            $success = $message->attachments()->createMany($attachments);
        }

        if ($success) {
            return response()->json([
                'data' => $ticket->load('messages.attachments', 'organization'),
            ]);
        }

        return response()->json([
            'message' => 'failure',
        ], 500);
    }
}
