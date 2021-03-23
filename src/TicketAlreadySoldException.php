<?php

namespace TicketSwap\Assessment;


final class TicketAlreadySoldException extends \Exception
{
    private $ticket;
    public static function withTicket(Ticket $ticket) : self
    {
        return new self(
            sprintf(
                'Ticket (%s) has already been sold',
                (string) $ticket->getId()
            )
        );
    }
}
