<?php

namespace TicketSwap\Assessment;

use TicketSwap\Assessment\Buyer;
use TicketSwap\Assessment\TicketId;
use TicketSwap\Assessment\Listing;
use TicketSwap\Assessment\Ticket;

final class Marketplace
{
    /**
     * @param array<Listing> $listingsForSale
     */
    public function __construct(private array $listingsForSale = [])
    {
        $this->listingsForSale = $listingsForSale;
    }

    /**
     * @return array<Listing>
     */
    public function getListingsForSale() : array
    {
        return $this->listingsForSale;
    }

    public function buyTicket(Buyer $buyer, TicketId $ticketId) : Ticket
    {
        foreach($this->listingsForSale as $listing) {
            foreach($listing->getTickets() as $ticket) {
                if ($ticket->getId()->equals($ticketId)) {
                    $ticketBought = $ticket->buyTicket($buyer);
                    //remove ticket from listing
                    $ticket->deleteTicket($ticket);
                    //if listing is empty, delete listing
                    if ($listing->getTickets() === null) {
                        $listing->deleteListing($listing);
                    }
                    return $ticketBought;
                }
            }
        }
    }

    /** Pushes new Listing to contructed Listing if the ticket isn't already being sold
     * First setListingForSale checks that a ticket with the same id of as the new Listing does not
     * exist in the current Listing. If it does, the new Listing is not added to the Listing, else
     * the new Listing is pushed to the current Listing */
    public function setListingForSale(Listing $listing) : void
    {
        foreach($this->listingsForSale as $currentlisting) {
            foreach($currentlisting->getTickets() as $ticket) {
                //if new Listing ticket id equals a ticket id already in the Listing, ticket can't be sold, return Listing as is
                foreach($listing->getTickets() as $newTicket) {
                    if ($ticket->getId()->equals($newTicket->getId())) {
                        return ;
                    }
                }
            }
        }
        array_push($this->listingsForSale, $listing);
        return ;
    }
}
