<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Event;
use PHPUnit\Framework\onNotSuccessfulTest;
use Tests\AppBundle\Framework\WebTestCase;


class EventsControllerTest extends WebTestCase
{

    /** @test */
    public function index_should_list_all_events()
    {

        $event1 = new Event ;
        $event1->setName('Symfony Conference')
            ->setLocation('Paris, FR')
            ->setDescription('Best Symfony course')
            ->setStartsAt(new \DateTime('+50 days'))
            ->setPrice(25)
        ;

        $event2 = new Event ;
        $event2->setName('Lavarel Conference')
            ->setLocation('Quebec, CA')
            ->setDescription('Best Symfony course')
            ->setStartsAt(new \DateTime('+5 years'))
            ->setPrice(0)
        ;

        $this->em->persist($event1);
        $this->em->persist($event2);

        $this->em->flush();

        $this->visit('/events')
            ->assertResponseOk()
            ->seeText('2 Events')
            ->seeText($event1->getName())
            ->seeText(mb_substr($event1->getDescription(), 0, $this->getParameter('default_truncate_limit')))
            ->seeText($event1->getLocation())
            ->seeText($event1->getStartsAt()->format($this->getParameter('date_format_default')))
            ->seeText('$25')

            ->seeText($event1->getName())
        ;

    }

}
