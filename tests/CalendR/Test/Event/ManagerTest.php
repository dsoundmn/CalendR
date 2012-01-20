<?php
namespace CalendR\Test\Event;

use CalendR\Event\Manager;
use CalendR\Event\Event;
use CalendR\Event\EventInterface;
use CalendR\Period\PeriodInterface;
use CalendR\Period\Day;

/**
 * Test class for Manager.
 * Generated by PHPUnit on 2012-01-20 at 19:25:21.
 */
class ManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Manager
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Manager;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testAdd()
    {
        $this->assertEquals(0, count($this->object));
        for ($i = 0 ; $i < 5 ; $i++ ) {
            $this->object->add($this->getAnEvent($i));
            $this->assertEquals($i + 1, count($this->object));
        }
    }

    public function testHas()
    {
        $this->object->add($this->getAnEvent(1));
        $this->assertFalse($this->object->has('event-0'));
        $this->assertTrue($this->object->has('event-1'));
    }

    public function testGet()
    {
        $event = $this->getAnEvent(1);
        $this->object->add($event);
        $this->assertSame($event->getUid(), $this->object->get('event-1')->getUid());
    }

    /**
     * @expectedException \CalendR\Event\Exception\NotFound
     */
    public function testGetInvalid()
    {
        $this->object->get('event-1')->getUid();
    }

    public function testRemove()
    {
        $this->object->add($this->getAnEvent(1));
        $this->assertEquals(1, count($this->object));
        $this->object->remove('event-1');
        $this->assertEquals(0, count($this->object));
    }

    public function testAll()
    {
        for ($i = 0 ; $i < 5 ; $i++ ) {
            $this->object->add($this->getAnEvent($i));
        }

        $events = $this->object->all();
        $this->assertInternalType('array', $events);

        for ($i = 0 ; $i < 5 ; $i++ ) {
            $this->assertArrayHasKey('event-' . $i, $events);
            $this->assertInstanceOf('CalendR\Event\EventInterface', $events['event-'.$i]);
        }
    }


    public function findProvider()
    {
        return array(
            array(
                new Day(new \DateTime('2012-01-01')),
                new Event('event-1', new \DateTime('2012-01-01 09:30'), new \DateTime('2012-01-01 12:30')),
                new Event('event-2', new \DateTime('2012-02-01 09:30'), new \DateTime('2012-02-01 12:30'))
            ),
            array(
                new Day(new \DateTime('2012-01-01')),
                new Event('event-1', new \DateTime('2012-01-01 09:30'), new \DateTime('2012-02-01 12:30')),
                new Event('event-2', new \DateTime('2012-02-01 09:30'), new \DateTime('2012-02-01 12:30'))
            ),
            array(
                new Day(new \DateTime('2012-01-01')),
                new Event('event-1', new \DateTime('2011-01-01 09:30'), new \DateTime('2012-01-01 12:30')),
                new Event('event-2', new \DateTime('2012-02-01 09:30'), new \DateTime('2012-02-01 12:30'))
            ),
            array(
                new Day(new \DateTime('2012-01-01')),
                new Event('event-1', new \DateTime('2011-01-01 09:30'), new \DateTime('2012-02-01 12:30')),
                new Event('event-2', new \DateTime('2010-01-01 09:30'), new \DateTime('2011-02-01 12:30'))
            ),
        );
    }

    /**
     *  @dataProvider findProvider
     */
    public function testFind(PeriodInterface $period, EventInterface $find, EventInterface $dontFind)
    {
        $this->object->add($find)->add($dontFind);
        $events = $this->object->find($period);
        $this->assertEquals(1, count($events));
        $this->assertEquals($find->getUid(), $events[0]->getUid());
    }

    public function testGetIterator()
    {
        for ($i = 0 ; $i < 5 ; $i++ ) {
            $this->object->add($this->getAnEvent($i));
        }

        $i = 0;

        foreach ($this->object as $key => $event) {
            $this->assertSame('event-'.$i++, $key);
        }
        $this->assertEquals(5, $i);
    }

    private function getAnEvent($i)
    {
        return new Event('event-'.$i, new \DateTime(), new \DateTime());
    }

}
