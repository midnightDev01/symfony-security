<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Models\AutoincrementTrait;
use App\Models\Message;
use App\Models\Organization;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketController extends BaseController
{
    use AutoincrementTrait;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        // domains is an array, in order to sort it we need to append a .0 as per MongoDB docs
        $this->sortBy = $this->sortBy === 'domains' ? 'domains.0' : $this->sortBy;
    }

    /**
     * List all tickets
     * URL: api/tickets
     * Method: GET
     *
     * @return array
     */
    public function index(): array
    {
        return $this->filter()
            ->orderBy($this->sortBy, $this->sort)
            ->paginate($this->items)
            ->items();
    }

    /**
     * Display a listing of the resource filtered by Organization Id.
     * URL: api/tickets/organization/{organizationId}
     * Method: GET
     *
     * @param int $organizationId
     * @return array
     */
    public function getByOrganization(int $organizationId): array
    {
        return $this->filter()
            ->whereRelation('organization', 'id', $organizationId)
            ->orderBy($this->sortBy, $this->sort)
            ->paginate($this->items)
            ->items();
    }

    /**
     * Returns the Query Builder with the filters attached to it
     *
     * @return Builder
     */
    private function filter(): Builder
    {
        $query = Ticket::query();

        if ($this->query) {
            $query = $query->orWhere('category', 'LIKE', "%$this->query%")
                ->orWhere('status', 'LIKE', "%$this->query%")
                ->orWhere('priority', 'LIKE', "%$this->query%")
                ->orWhere('organization', 'LIKE', "%$this->query%")
                ->orWhere('domains', 'LIKE', "%$this->query%")
                ->orWhere('category', 'LIKE', "%$this->query%")
                ->orWhere('owner', 'LIKE', "%$this->query%")
                ->orWhere('assignee', 'LIKE', "%$this->query%");
        }

        if ($this->from) {
            $query->where('created_at', '>=', $this->from);
        }

        if ($this->to) {
            $query->where('created_at', '<=', $this->to);
        }

        return $query;
    }

    /**
     * Stores a new ticket
     * URL: api/tickets
     * Method: POST
     *
     * @param StoreTicketRequest $request
     * @return JsonResponse
     */
    //@todo get user info from session
    public function store(StoreTicketRequest $request): JsonResponse
    {
        $ticket         = new Ticket($request->getAcceptedFields());
        $ticket->owner  = 'Thomas Jasper';
        $ticket->status = 'open';

        $ticket->autoincrement();
        $ticket->save();

        //@todo get organization info from session
        $organization = Organization::first();
        $success = $organization->tickets()->save($ticket);

        if ($success) {
            $success = $ticket->messages()->save(
                new Message($request->get('message'))
            );
        }

        if ($success) {
            return response()->json([
                'data' => $ticket->load('messages', 'organization'),
            ], 201);
        }

        return response()->json([
            'message' => 'failure',
        ]);

    }

    /**
     * Show a ticket
     * URL: api/tickets/{ticketId}
     * Method: GET
     *
     * @param Ticket  $ticket
     * @param Request $request
     * @return Ticket
     */
    public function show(Ticket $ticket, Request $request): Ticket
    {
        $internal = $request->get('internal') === 'true';
        $internal = true;
        /* @todo remove when we have users with privileges */

        if ($internal) {
            return $ticket->load('messages.attachments', 'organization');
        }

        return $ticket->load([
            'messages' => function ($q) use ($internal) {
                $q->where('internal', '=', $internal)->with('attachments');
            },
        ])->with('organization');
    }

    /**
     * Updates a ticket
     * URL: api/tickets/{ticketId}
     * Method: PUT
     *
     * @param UpdateTicketRequest $request
     * @param Ticket              $ticket
     * @return JsonResponse
     */
    public function update(UpdateTicketRequest $request, Ticket $ticket): JsonResponse
    {
        $errors = 0;

        $message = $request->get('message');

        if (isset($message['content'])) {

            /* @todo change to real owner */
            $message["owner"] = "Testing owner";
            /* @todo change to real internal value */
            $message["internal"] = false;

            $errors += $ticket->messages()->save(
                new Message($message)
            ) === false ? 1 : 0;
        }

        $errors += $ticket->update($request->getAcceptedFields()) ? 0 : 1;

        if ($errors === 0) {
            return response()->json([
                'data' => $ticket->load('messages', 'organization'),
            ]);
        }

        return response()->json([
            'message' => 'failure',
        ], 500);
    }

    public function reopenTicket(Ticket $ticket) {
        if( !$ticket->closed() ) {
            return response()->json([
                'message' => 'Ticket is not closed.',
            ], 205);
        }
        $ticket->updateStatus(Ticket::STATUS_REOPENED);
        return $ticket;
    }

    public function closeTicket(Ticket $ticket, $success=true) {
        if( $ticket->closed() ) {
            return response()->json([
                'message' => 'Ticket is already closed.',
            ], 205);
        }
        $ticket->updateStatus($success?Ticket::STATUS_CLOSED_SUCCESS:Ticket::STATUS_CLOSED_FAILED);
        return $ticket;
    }
}
