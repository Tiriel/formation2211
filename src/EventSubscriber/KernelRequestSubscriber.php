<?php

namespace App\EventSubscriber;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class KernelRequestSubscriber implements EventSubscriberInterface
{
    private readonly bool $isMaintenance;

    public function __construct(
        private readonly Environment $twig,
        #[Autowire('%env(bool:APP_MAINTENANCE)%')]
        bool $isMaintenance
    )
    {
        $this->isMaintenance = $isMaintenance;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if ($this->isMaintenance) {
            $response = new Response($this->twig->render('maintenance.html.twig'));
            $event->setResponse($response);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 9999],
        ];
    }
}
