<?php

namespace TicketSwap\Assessment;

use Money\Money;

final class Listing
{
    /**
     * @param array<Ticket> $tickets
     */
    public function __construct( private ListingId $id, private Seller $seller, private array $tickets, private Money $price ) {
        $this->tickets = $tickets;
        if ($this->checkBarcodes()) {
            $this->id = $id;
            $this->seller = $seller;
            $this->price = $price;
        }
    }

    public function checkBarcodes() : bool {
        $ticketBarcodes = [];
        foreach ($this->tickets as $ticket) {
            $barcode = (string)$ticket->getBarcode();
            foreach ($ticketBarcodes as $code) {
                if ($code === $barcode) {
                    $this->tickets = [];
                    return FALSE;
                }
            }
            array_push($ticketBarcodes, $barcode);
        }
        return TRUE;
    }

    public function getId() : ListingId
    {
        return $this->id;
    }

    public function getSeller() : Seller
    {
        return $this->seller;
    }

    /**
     * @return array<Ticket>
     */
    public function getTickets(?bool $forSale = null) : array
    {
        if (true === $forSale) {
            $forSaleTickets = [];
            foreach ($this->tickets as $ticket) {
                if (!$ticket->isBought()) {
                    $forSaleTickets[] = $ticket;
                }
            }
            return $forSaleTickets;
        } else if (false === $forSale) {
            $notForSaleTickets = [];
            foreach ($this->tickets as $ticket) {
                if ($ticket->isBought()) {
                    $notForSaleTickets[] = $ticket;
                }
            }
            return $notForSaleTickets;
        } else {
            return $this->tickets;
        }
    }

    public function getPrice() : Money
    {
        return $this->price;
    }

    public function deleteListing(Listing $listing) : void
    {
        unset($listing);
    }
}
