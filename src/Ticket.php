<?php

namespace TicketSwap\Assessment;

use TicketSwap\Assessment\Listing;
use TicketSwap\Assessment\Buyer;
use TicketSwap\Assessment\TicketId;
use TicketSwap\Assessment\Barcode;

final class Ticket
{
    public function __construct(private TicketId $id, private Barcode $barcode, private ?Buyer $buyer = null)
    {
        $this->id = $id;
        $this->barcode = $barcode;
        $this->buyer = $buyer;
    }

    public function getId() : TicketId
    {
        return $this->id;
    }

    public function getBarcode() : Barcode
    {
        return $this->barcode;
    }

    public function getBuyer() : Buyer
    {
        return $this->buyer;
    }

    public function isBought() : bool
    {
        return $this->buyer !== null;
    }

    public function buyTicket(Buyer $buyer) : self
    {
        $this->buyer = $buyer;

        return $this;
    }

    public function deleteTicket(Ticket $ticket) : void
    {
       unset($ticket);
    }
}
