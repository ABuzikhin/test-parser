<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Service\ParsingService;
use App\Service\UrlCollector;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ParseUrlSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly UrlCollector $urlCollector,
        private readonly EntityManagerInterface $entityManager,
        private readonly ParsingService $parser,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::TERMINATE => 'parseUrl',
        ];
    }

    public function parseUrl(TerminateEvent $event): void
    {
        if (!$this->urlCollector->hasUrls()) {
            return;
        }

        foreach ($this->urlCollector->getUrls() as $parseUrl) {
            $this->entityManager->persist($parseUrl);
            $this->entityManager->flush();

            try {
                $this->parser->parse($parseUrl);
            } catch (\Exception $exception) {
                if (!$this->entityManager->isOpen()) {
                    return;
                }

                $parseUrl->markError($exception->getMessage());
                $this->entityManager->flush();
            }
        }
    }
}