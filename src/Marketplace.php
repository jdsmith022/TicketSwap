<?php

namespace TicketSwap\Assessment;

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
        try {
            foreach($this->listingsForSale as $listing) {
                foreach($listing->getTickets() as $ticket) {
                    if ($ticket->getId()->equals($ticketId) && !$ticket->isBought()) {
                        $ticketBought = $ticket->buyTicket($buyer);
                        //remove ticket from listing
                        $ticket->deleteTicket($ticket);
                        //if listing is empty, delete listing
                        if ($listing->getTickets() === null) {
                            $listing->deleteListing($listing);
                        }
                        return $ticketBought;
                    } else {
                        $ticketAlreadySoldException = new TicketAlreadySoldException();
                        throw $ticketAlreadySoldException->withTicket($ticket);
                    }
                }
            }
        } catch (TicketAlreadySoldException $e) {
            throw $e->withTicket($ticket);
        }
    }

    /** Pushes new Listing to contructed Listing if the ticket isn't already being sold
     * First setListingForSale checks that a ticket with the same barcode of as the new Listing does not
     * exist in the listings. If it does, the new Listing is not added to the Listing, else
     * the new Listing is pushed to the current Listing */
    public function setListingForSale(Listing $newListingForSale) : void
    {
        $newListingSeller = $newListingForSale ->getSeller();
        foreach($this->listingsForSale as $currentListingForSale) {
            foreach($currentListingForSale->getTickets() as $currentListingTicket) {
                foreach($newListingForSale->getTickets() as $newListingTicket) {
                    if ((string)$currentListingTicket->getBarcode() === (string)$newListingTicket->getBarcode()) {
                        if (!$currentListingTicket->isBought()) {
                            return ;
                        } else {
                            if ($newListingSeller != $currentListingTicket->getBuyer()) {
                                return ;
                            }
                        }
                    }
                }
            }
        }
        array_push($this->listingsForSale, $newListingForSale);
        return ;
    }
}
