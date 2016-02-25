<?php

namespace othillo\BroadwayReplayer\Functional\Replayer;

use Broadway\Domain\DomainEventStream;
use Broadway\Domain\DomainMessage;
use Broadway\Domain\Metadata;
use Broadway\EventHandling\SimpleEventBus;
use Broadway\EventStore\InMemoryEventStore;
use Broadway\EventStore\Management\Criteria;
use Broadway\ReadModel\InMemory\InMemoryRepository;
use Broadway\ReadModel\ProjectorInterface;
use Broadway\ReadModel\ReadModelInterface;
use Broadway\ReadModel\RepositoryInterface;
use othillo\BroadwayReplayer\EventHandling\ReplayAwareSimpleEventBus;
use othillo\BroadwayReplayer\EventStore\EventBusPublishingVisitor;
use othillo\BroadwayReplayer\EventStore\ReplayAwareEventBusPublishingVisitor;
use othillo\BroadwayReplayer\ReadModel\ReplayAwareProjectorInterface;
use othillo\BroadwayReplayer\Replayer\Replayer;

class ReplayerTest extends \PHPUnit_Framework_TestCase
{
    private $eventBus;
    private $eventStore;
    private $eventVisitor;
    private $replayer;

    public function setUp()
    {
        $this->eventStore   = $this->createEventStore();
        $this->eventBus     = new SimpleEventBus();
        $this->eventVisitor = new EventBusPublishingVisitor($this->eventBus);
        $this->replayer     = new Replayer($this->eventStore, $this->eventVisitor);
    }

    /**
     * @test
     */
    public function it_replays_events()
    {
        // register projectors
        $readModelRepository = new InMemoryRepository();
        $readModelRepository->save(new CountReadModel(2)); // count = 2 because events are already projected before replaying

        $this->eventBus->subscribe(new CountProjector($readModelRepository));

        $this->replayer->replay(new Criteria());

        $readModel = $readModelRepository->find('1337');

        $this->assertEquals(4, $readModel->getCount());
    }

    /**
     * @test
     */
    public function it_replays_events_with_replay_aware_projectors()
    {
        // register projectors
        $readModelRepository = new InMemoryRepository();
        $readModelRepository->save(new CountReadModel(2)); // count = 2 because events are already projected before replaying

        $this->eventBus->subscribe(new ReplayAwareCountProjector($readModelRepository));

        $replayer = new Replayer($this->eventStore, new ReplayAwareEventBusPublishingVisitor(new ReplayAwareSimpleEventBus()));
        $replayer->replay(new Criteria());

        $readModel = $readModelRepository->find('1337');

        $this->assertEquals(2, $readModel->getCount());
    }

    private function createDomainMessage($playhead, $payload)
    {
        return DomainMessage::recordNow(1, $playhead, new Metadata([]), $payload);
    }

    private function createEventStore()
    {
        $eventStore = new InMemoryEventStore();

        // create replayable events
        $event1      = $this->createDomainMessage(1, ['foo' => 'bar']);
        $event2      = $this->createDomainMessage(2, ['bar' => 'baz']);
        $eventStream = new DomainEventStream([$event1, $event2]);

        $eventStore->append('42', $eventStream);

        return $eventStore;
    }
}

class CountProjector implements ProjectorInterface
{
    protected $readModelRepository;

    public function __construct(RepositoryInterface $readModelRepository)
    {
        $this->readModelRepository = $readModelRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(DomainMessage $domainMessage)
    {
        $readModel = $this->readModelRepository->find('1337');
        $readModel->incrementCount();

        $this->readModelRepository->save($readModel);
    }
}

class ReplayAwareCountProjector extends CountProjector implements ReplayAwareProjectorInterface
{
    /**
     * {@inheritdoc}
     */
    public function beforeReplay()
    {
        // reset the counter
        $this->readModelRepository->remove('1337');
        $this->readModelRepository->save(new CountReadModel(0));
    }

    /**
     * {@inheritdoc}
     */
    public function afterReplay()
    {
        // TODO: Implement afterReplay() method.
    }
}

class CountReadModel implements ReadModelInterface
{
    private $count = 0;

    /**
     * @param int $count
     */
    public function __construct($count)
    {
        $this->count = $count;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return '1337';
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    public function incrementCount()
    {
        $this->count++;
    }
}
