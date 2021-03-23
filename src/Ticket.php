<?php

namespace TicketSwap\Assessment;

final class Ticket
{
    public function __construct(private TicketId $id,  private ?Buyer $buyer = null, private array $barcodes)
    {
        $this->id = $id;
        $this->barcodes = $barcodes;
        $this->buyer = $buyer;
    }

    public function getId() : TicketId
    {
        return $this->id;
    }

    public function getBarcodes() : Array
    {
        $barcodeArray = [];
        foreach($this->barcodes as $barcode) {
            array_push($barcodeArray, (string)$barcode);
        }
        return $barcodeArray;
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

    public function isResell() : void {
        $this->buyer = null;
        // return $this->buyer;
    }
}
