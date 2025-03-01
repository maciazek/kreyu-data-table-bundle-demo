<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class LocaleSubscriber implements EventSubscriberInterface
{
    public function __construct(
        #[Autowire(env: 'APP_DEFAULT_LOCALE')] private string $defaultLocale,
        private UrlGeneratorInterface $router,
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        // try to see if the locale has been set as a _locale routing parameter
        if ($locale = $request->query->get('_locale')) {
            // store new locale in session
            $request->getSession()->set('_locale', $locale);

            // remove locale from URL
            $queryInputBag = $request->query;
            $queryInputBag->remove('_locale');
            $event->setResponse(
                new RedirectResponse(
                    $this->router->generate(
                        $request->attributes->get('_route'),
                        array_merge(
                            $request->attributes->get('_route_params'),
                            $queryInputBag->all(),
                        ),
                    ),
                ),
            );
        } else { // if no explicit locale has been set on this request, use one from the session
            $request->setLocale($request->getSession()->get('_locale', $this->defaultLocale));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // must be registered before (i.e. with a higher priority than) the default Locale listener
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }
}
