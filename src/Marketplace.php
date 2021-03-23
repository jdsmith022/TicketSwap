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

    /** Checks listings that are for sale and sets as bought
     *
     * Finds ticket in listing available for sale by matching ticket ids
     * throws exception error if: ticket is already bought and buyer is trying to buy own ticket
     */
    public function buyTicket(Buyer $buyer, TicketId $ticketId) : Ticket
    {
        $ticketAlreadySoldException = new TicketAlreadySoldException();
        try {
            $listingsForSale = $this->getListingsForSale();
            if (!$listingsForSale) {
                $soldTicket = $this->findSoldTicket($ticketId);
                throw $ticketAlreadySoldException->withTicket($soldTicket);
            }
            foreach($listingsForSale as $listing) {
                foreach($listing->getTickets() as $ticket) {
                    if ($ticket->getId()->equals($ticketId) && (string)$buyer != (string)$listing->getSeller()) {
                        $ticketBought = $ticket->buyTicket($buyer);
                        return $ticketBought;
                    } else if ($ticket->getId()->equals($ticketId) && (string)$buyer == (string)$listing->getSeller())  {
                        throw $ticketAlreadySoldException->withTicket($ticket);
                    }
                }
            }
        } catch (TicketAlreadySoldException $e) {
            throw new ticketAlreadySoldException($e);
        }
    }


    /**
     * @return array<Listing>
     * Returns array of only listings that have tickets available to sell
     */
    public function getListingsForSale() : array
    {
        $unsoldListings = [];
        foreach($this->listingsForSale as $listing) {
            if ($listing->getTickets(true)) {
                array_push($unsoldListings, $listing);
            }
        }
        return $unsoldListings;
    }


    public function findSoldTicket(TicketId $ticketId) : Ticket
    {
        foreach($this->listingsForSale as $listing) {
            foreach($listing->getTickets(false) as $ticket) {
                if ($ticket->getId()->equals($ticketId)) {
                    return $ticket;
                }
            }
        }
    }

    /** Pushes new Listing to current Listing if the ticket isn't already being sold
     *
     * First setListingForSale checks that a ticket with the same barcode as the new Listing does not
     * exist in the current Listing. If it does, the new Listing is not added to the current Listing, else
     * the new Listing is pushed to the current Listing */
    public function setListingForSale(Listing $newListingForSale) : void
    {
        foreach($this->listingsForSale as $currentListingForSale) {
            foreach($currentListingForSale->getTickets(true) as $currentListingTicket) {
                foreach($newListingForSale->getTickets() as $newListingTicket) {
                    $currentBarcodes = $currentListingTicket->getBarcodes();
                    $newBarcodes = $newListingTicket->getBarcodes();
                    foreach ($currentBarcodes as $currentBarcode) {
                        foreach ($newBarcodes as $newBarcode) {
                            if ((string)$currentBarcode === (string)$newBarcode) {
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
