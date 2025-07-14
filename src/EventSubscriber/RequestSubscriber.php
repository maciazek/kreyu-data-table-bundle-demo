<?php

namespace App\EventSubscriber;

use App\Enum\Asynchronicity;
use App\Enum\DataTableIconTheme;
use App\Enum\DataTableTheme;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RequestSubscriber implements EventSubscriberInterface
{
    public function __construct(
        #[Autowire(env: 'APP_DEFAULT_LOCALE')] private string $defaultLocale,
        private UrlGeneratorInterface $router,
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        // save uri so it can return to the correct datatable
        if (str_starts_with($request->attributes->getString('_route'), 'app_homepage')
            || str_starts_with($request->attributes->getString('_route'), 'app_action')
            || str_starts_with($request->attributes->getString('_route'), 'app_experimental')
        ) {
            $request->getSession()->set('_redirect_to', $request->getUri());
        }

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

        // try to see if asynchronicity option has been set as a _asynchronicity routing parameter
        if ($asynchronicity = $request->query->getEnum('_asynchronicity', Asynchronicity::class)) {
            // store new asynchronicity option in session
            $request->getSession()->set('_asynchronicity', $asynchronicity);

            // remove asynchronicity option from URL
            $queryInputBag = $request->query;
            $queryInputBag->remove('_asynchronicity');
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
        } else { // if no explicit asynchronicity option has been set on this request, use one from the session
            $request->getSession()->set('_asynchronicity', $request->getSession()->get('_asynchronicity', Asynchronicity::SYNC));
        }

        // try to see if the data table theme has been set as a routing parameter
        if ($dataTableTheme = $request->query->getEnum('_data_table_theme', DataTableTheme::class)) {
            // store new data table theme in session
            $request->getSession()->set('_data_table_theme', $dataTableTheme);

            // remove data table theme from URL
            $queryInputBag = $request->query;
            $queryInputBag->remove('_data_table_theme');
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
        } else { // if no explicit data table theme has been set on this request, use one from the session
            $request->getSession()->set('_data_table_theme', $request->getSession()->get('_data_table_theme', DataTableTheme::BOOTSTRAP_5_CUSTOM));
        }

        // try to see if the data table icon theme has been set as a routing parameter
        if ($dataTableIconTheme = $request->query->getEnum('_data_table_icon_theme', DataTableIconTheme::class)) {
            // store new data table icon theme in session
            $request->getSession()->set('_data_table_icon_theme', $dataTableIconTheme);

            // remove data table icon theme from URL
            $queryInputBag = $request->query;
            $queryInputBag->remove('_data_table_icon_theme');
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
        } else { // if no explicit data table icon theme has been set on this request, use one from the session
            $request->getSession()->set('_data_table_icon_theme', $request->getSession()->get('_data_table_icon_theme', DataTableIconTheme::BOOTSTRAP_ICONS_WEBFONT));
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
