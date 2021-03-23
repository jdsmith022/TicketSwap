<?php

namespace TicketSwap\Assessment\tests;

use PHPUnit\Framework\TestCase;
use Money\Currency;
use Money\Money;
use TicketSwap\Assessment\Barcode;
use TicketSwap\Assessment\Buyer;
use TicketSwap\Assessment\Listing;
use TicketSwap\Assessment\ListingId;
use TicketSwap\Assessment\Marketplace;
use TicketSwap\Assessment\Seller;
use TicketSwap\Assessment\Ticket;
use TicketSwap\Assessment\TicketAlreadySoldException;
use TicketSwap\Assessment\NoListingForSale;
use TicketSwap\Assessment\TicketId;


class MarketplaceTest extends TestCase
{

    /**
     * @test
     */
    public function it_should_list_all_the_tickets_for_sale()
    {
        $marketplace = new Marketplace(
            listingsForSale: [
                new Listing(
                    id: new ListingId('D59FDCCC-7713-45EE-A050-8A553A0F1169'),
                    seller: new Seller('Pascal'),
                    tickets: [
                        new Ticket(
                                new TicketId('6293BB44-2F5F-4E2A-ACA8-8CDF01AF401B'),
                                barcodes: [
                                    new Barcode('EAN-13', '38974312923')
                                ],
                            ),
                        ],
                        price: new Money(4950, new Currency('EUR')),
                    ),
                ]
            );

        $listingsForSale = $marketplace->getListingsForSale();
        $this->assertCount(1, $listingsForSale);
        $listingTicket = $listingsForSale[0]->getTickets();
        // $this->assertSame('EAN-13:38974312923', (string) $listingTicket[0]->getBarcodes());
    }

    /**
     * @test
     *
     */
    public function it_should_be_possible_to_buy_a_ticket()
    {
        $marketplace = new Marketplace(
            listingsForSale: [
                new Listing(
                    id: new ListingId('D59FDCCC-7713-45EE-A050-8A553A0F1169'),
                    seller: new Seller('Pascal'),
                    tickets: [
                        new Ticket(
                            new TicketId('6293BB44-2F5F-4E2A-ACA8-8CDF01AF401B'),
                            barcodes: [
                                new Barcode('EAN-13', '38974312924'),
                                new Barcode('EAN-13', '38974312923')
                            ],
                        ),
                    ],
                    price: new Money(4950, new Currency('EUR')),
                ),
                ]
            );
        $boughtTicket = $marketplace->buyTicket(
            buyer: new Buyer('Sarah'),
            ticketId: new TicketId('6293BB44-2F5F-4E2A-ACA8-8CDF01AF401B')
        );

        $this->assertNotNull($boughtTicket);
        $barcodeTest = (array)$boughtTicket->getBarcodes();
        $this->assertSame('EAN-13:38974312923', (string)$barcodeTest[1]);
    }

    /**
     * @test
     */
    public function it_should_not_be_possible_to_buy_the_same_ticket_twice()
    {
        $this->expectException(TicketAlreadySoldException::class);
        $marketplace = new Marketplace(
            listingsForSale: [
                new Listing(
                    id: new ListingId('D59FDCCC-7713-45EE-A050-8A553A0F1169'),
                    seller: new Seller('Pascal'),
                    tickets: [
                            new Ticket(
                                    new TicketId('6293BB44-2F5F-4E2A-ACA8-8CDF01AF401B'),
                                    barcodes: [
                                        new Barcode('EAN-13', '38974312923')
                                    ],
                                ),
                            ],
                            price: new Money(4950, new Currency('EUR')),
                        ),
                    ]
            );

        $boughtTicket = $marketplace->buyTicket(
            buyer: new Buyer('Sarah'),
            ticketId: new TicketId('6293BB44-2F5F-4E2A-ACA8-8CDF01AF401B')
        );
        $this->assertNotNull($boughtTicket);
        // $this->assertSame('EAN-13:38974312923', (string) $boughtTicket->getBarcodes());

        $boughtTicket = $marketplace->buyTicket(
            buyer: new Buyer('Tom'),
            ticketId: new TicketId('6293BB44-2F5F-4E2A-ACA8-8CDF01AF401B')
        );
        $this->assertNull($boughtTicket);
    }


    /**
     * @test
     */
    public function it_should_be_possible_to_put_a_listing_for_sale()
    {
        $marketplace = new Marketplace(
            listingsForSale: [
                new Listing(
                    id: new ListingId('D59FDCCC-7713-45EE-A050-8A553A0F1169'),
                    seller: new Seller('Pascal'),
                    tickets: [
                            new Ticket(
                                    new TicketId('6293BB44-2F5F-4E2A-ACA8-8CDF01AF401B'),
                                    new Buyer('Jane'),
                                    barcodes: [
                                        new Barcode('EAN-13', '38974312923'),
                                    ],
                                ),
                            new Ticket(
                                new TicketId('4273BT22-6Y6E-2K2I-BNE9-0ARU09AJ513H'),
                                barcodes: [
                                    new Barcode('EAN-13', '57239802321')
                                ]
                            ),
                        ],
                            price: new Money(4950, new Currency('EUR')),
                        ),
                    ]
            );

        $marketplace->setListingForSale(
            new Listing(
                id: new ListingId('26A7E5C4-3F59-4B3C-B5EB-6F2718BC31AD'),
                seller: new Seller('Tom'),
                tickets: [
                    new Ticket(
                        new TicketId('45B96761-E533-4925-859F-3CA62182848E'),
                        barcodes: [
                            new Barcode('EAN-13', '893759834')
                        ],
                    ),
                ],
                price: new Money(4950, new Currency('EUR')),
            )
        );

        $listingsForSale = $marketplace->getListingsForSale();
        $this->assertCount(2, $listingsForSale);
        $listingTicket = $listingsForSale[0]->getTickets();
        // $this->assertSame('EAN-13:38974312923', (string) $listingTicket[0]->getBarcodes());
        // $this->assertSame('EAN-13:57239802321', (string) $listingTicket[1]->getBarcodes());
        $listingTickets = $listingsForSale[1]->getTickets();
        // $this->assertSame('EAN-13:893759834', (string) $listingTickets[0]->getBarcodes());
    }

    /**
     * @test
     */
    public function it_should_not_be_possible_to_sell_a_ticket_with_a_barcode_that_is_already_for_sale()
    {
        $marketplace = new Marketplace(
            listingsForSale: [
                new Listing(
                    id: new ListingId('D59FDCCC-7713-45EE-A050-8A553A0F1169'),
                    seller: new Seller('Pascal'),
                    tickets: [
                            new Ticket(
                                    new TicketId('6293BB44-2F5F-4E2A-ACA8-8CDF01AF401B'),
                                    barcodes: [
                                        new Barcode('EAN-13', '38974312924'),
                                        new Barcode('EAN-13', '38974312923')
                                    ],
                                ),
                            ],
                            price: new Money(4950, new Currency('EUR')),
                        ),
                    ]
            );

        //Tom tries to sell ticket with same bardcode
        $marketplace->setListingForSale(
            new Listing(
                id: new ListingId('26A7E5C4-3F59-4B3C-B5EB-6F2718BC31AD'),
                seller: new Seller('Tom'),
                tickets: [
                    new Ticket(
                            new TicketId('6293BB44-2F5F-4E2A-ACA8-8CDF01AF401B'),
                            barcodes: [
                                new Barcode('EAN-13', '38974312923')
                            ],
                    ),
                ],
                price: new Money(4950, new Currency('EUR')),
            )
        );

        //confirm only one listing is available for sale
        $listingsForSale = $marketplace->getListingsForSale();
        $this->assertCount(1, $listingsForSale);
        $this->assertSame('Pascal', (string)$listingsForSale[0]->getSeller());
    }

    /**
     * @test
     *
     * Tests that a buyer can re-list and resell their bought ticket
     */
    public function it_should_be_possible_for_a_buyer_of_a_ticket_to_sell_it_again()
    {
        $marketplace = new Marketplace(
            listingsForSale: [
                new Listing(
                    id: new ListingId('D59FDCCC-7713-45EE-A050-8A553A0F1169'),
                    seller: new Seller('Pascal'),
                    tickets: [
                            new Ticket(
                                    new TicketId('6293BB44-2F5F-4E2A-ACA8-8CDF01AF401B'),
                                    barcodes: [
                                        new Barcode('EAN-13', '38974312923')
                                    ],
                                ),
                            ],
                            price: new Money(4950, new Currency('EUR')),
                        ),
                    ]
            );
        //Tom buys ticket from Pascal
        $boughtTicket = $marketplace->buyTicket(
            buyer: new Buyer('Tom'),
            ticketId: new TicketId('6293BB44-2F5F-4E2A-ACA8-8CDF01AF401B')
        );

        $this->assertNotNull($boughtTicket);
        // $this->assertSame('EAN-13:38974312923', (string) $boughtTicket->getBarcodes());

        //Tom lists ticket
        $marketplace->setListingForSale(
            new Listing(
                id: new ListingId('26A7E5C4-3F59-4B3C-B5EB-6F2718BC31AD'),
                seller: new Seller('Tom'),
                tickets: [
                    new Ticket(
                            new TicketId('6293BB44-2F5F-4E2A-ACA8-8CDF01AF401B'),
                            barcodes: [
                                new Barcode('EAN-13', '38974312923')
                            ],
                    ),
                ],
                price: new Money(4950, new Currency('EUR')),
            )
        );

        $listingsForSale = $marketplace->getListingsForSale();
        $this->assertCount(1, $listingsForSale);
        $listingTicket = $listingsForSale[0]->getTickets();
        $this->assertSame('26A7E5C4-3F59-4B3C-B5EB-6F2718BC31AD', (string)$listingsForSale[0]->getId());
        // $this->assertSame('EAN-13:38974312923', (string) $listingTicket[0]->getBarcodes());

        //Sarah buys Tom's tickets
        $boughtTicket = $marketplace->buyTicket(
            buyer: new Buyer('Sarah'),
            ticketId: new TicketId('6293BB44-2F5F-4E2A-ACA8-8CDF01AF401B')
        );

        $this->assertNotNull($boughtTicket);
        // $this->assertSame('EAN-13:38974312923', (string) $boughtTicket->getBarcodes());
    }


    /**
     * @test
     *
     * Tests that a buyer can relist and resell their bought ticket
     */
    public function it_should_not_be_possible_for_a_buyer_to_buy_their_ticket()
    {
        $this->expectException(TicketAlreadySoldException::class);
        $marketplace = new Marketplace(
            listingsForSale: [
                new Listing(
                    id: new ListingId('D59FDCCC-7713-45EE-A050-8A553A0F1169'),
                    seller: new Seller('Pascal'),
                    tickets: [
                            new Ticket(
                                    new TicketId('6293BB44-2F5F-4E2A-ACA8-8CDF01AF401B'),
                                    barcodes: [
                                        new Barcode('EAN-13', '38974312923')
                                    ],
                                ),
                            ],
                            price: new Money(4950, new Currency('EUR')),
                        ),
                    ]
            );

        //attempts to buy same ticket from self
        $boughtTicket = $marketplace->buyTicket(
            buyer: new Buyer('Pascal'),
            ticketId: new TicketId('6293BB44-2F5F-4E2A-ACA8-8CDF01AF401B')
        );
    }


    /**
     * @test
     * Tests if getListingsForSale() returns nothing is no listings are availble for sale
     */
    public function it_should_not_be_possible_to_get_list_if_not_ticket_are_for_sale()
    {
        $marketplace = new Marketplace(
            listingsForSale: [
                new Listing(
                    id: new ListingId('D59FDCCC-7713-45EE-A050-8A553A0F1169'),
                    seller: new Seller('Pascal'),
                    tickets: [
                        new Ticket(
                            new TicketId('6293BB44-2F5F-4E2A-ACA8-8CDF01AF401B'),
                            barcodes: [
                                new Barcode('EAN-13', '38974312923')
                            ],
                        ),
                        new Ticket(
                            new TicketId('4273BT22-6Y6E-2K2I-BNE9-0ARU09AJ513H'),
                            barcodes: [
                                new Barcode('EAN-13', '57239802321')
                            ],
                        ),
                    ],
                    price: new Money(4950, new Currency('EUR')),
                ),
            ]
        );

        //Tom buy first ticket
        $boughtTicket = $marketplace->buyTicket(
            buyer: new Buyer('Tom'),
            ticketId: new TicketId('6293BB44-2F5F-4E2A-ACA8-8CDF01AF401B')
        );

        $this->assertNotNull($boughtTicket);
        // $this->assertSame('EAN-13:38974312923', (string) $boughtTicket->getBarcodes());

        //checks listings for sale is only 1
        $listingsForSale = $marketplace->getListingsForSale();
        $this->assertCount(1, $listingsForSale);

        //Sarah buy second ticket
        $boughtTicket = $marketplace->buyTicket(
            buyer: new Buyer('Sarah'),
            ticketId: new TicketId('4273BT22-6Y6E-2K2I-BNE9-0ARU09AJ513H')
        );

        $this->assertNotNull($boughtTicket);
        // $this->assertSame('EAN-13:57239802321', (string) $boughtTicket->getBarcodes());

        //checks listings for sale is 0
        $listingsForSale = $marketplace->getListingsForSale();
        $this->assertCount(0, $listingsForSale);
    }

    /**
     * @test
     * Tests if getListingsForSale() returns nothing is no listings are availble for sale
     */
    public function it_should_not_be_possible_to_buy_from_listing_that_has_sold_all_tickets()
    {
        $this->expectException(TicketAlreadySoldException::class);
        $marketplace = new Marketplace(
            listingsForSale: [
                new Listing(
                    id: new ListingId('D59FDCCC-7713-45EE-A050-8A553A0F1169'),
                    seller: new Seller('Pascal'),
                    tickets: [
                        new Ticket(
                            new TicketId('4273BT22-6Y6E-2K2I-BNE9-0ARU09AJ513H'),
                            barcodes: [
                                new Barcode('EAN-13', '57239802321')
                            ],
                        )
                    ],
                    price: new Money(4950, new Currency('EUR')),
                ),
            ]
        );

        //Sarah buys first ticket
        $boughtTicket = $marketplace->buyTicket(
            buyer: new Buyer('Sarah'),
            ticketId: new TicketId('4273BT22-6Y6E-2K2I-BNE9-0ARU09AJ513H')
        );

        $this->assertNotNull($boughtTicket);
        // $this->assertSame('EAN-13:57239802321', (string) $boughtTicket->getBarcodes());

        //checks listings for sale is 0
        $listingsForSale = $marketplace->getListingsForSale();
        $this->assertCount(0, $listingsForSale);

        //Jan tries to buy ticket that was already sold
        $boughtTicket = $marketplace->buyTicket(
            buyer: new Buyer('Jan'),
            ticketId: new TicketId('4273BT22-6Y6E-2K2I-BNE9-0ARU09AJ513H')
        );
    }

}
