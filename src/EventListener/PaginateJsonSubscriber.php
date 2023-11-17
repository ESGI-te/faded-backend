<?php

namespace App\EventListener;

use ApiPlatform\Doctrine\Orm\Paginator;
use ApiPlatform\Symfony\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener]
final class PaginateJsonSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['normalizePagination', EventPriorities::PRE_RESPOND],
        ];
    }

    public function normalizePagination(
        ViewEvent $event
    ): void {
        $method = $event->getRequest()->getMethod();

        if ($method !== Request::METHOD_GET) {
            return;
        }
        if (($data = $event->getRequest()->attributes->get('data')) && $data instanceof Paginator) {
            $json = json_decode($event->getControllerResult(), true);
            $pagination = [
                'first' => 1,
                'current' => $data->getCurrentPage(),
                'last' => $data->getLastPage(),
                'previous' => $data->getCurrentPage() - 1 <= 0 ? 1 : $data->getCurrentPage() - 1,
                'next' => $data->getCurrentPage() + 1 > $data->getLastPage() ? $data->getLastPage() : $data->getCurrentPage() + 1,
                'totalItems' => $data->getTotalItems(),
                'parPage' => count($data)
            ];
            $res = [
                "data" => $json,
                "pagination" => $pagination
            ];
            $event->setControllerResult(json_encode($res));
        }
    }
}
