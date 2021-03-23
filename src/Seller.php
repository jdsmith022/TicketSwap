<?php

namespace TicketSwap\Assessment;

final class Seller implements \Stringable
{
    public function __construct(private string $name)
    {
        $this->name = $name;
    }

    public function __toString() : string
    {
        return $this->name;
    }
}
