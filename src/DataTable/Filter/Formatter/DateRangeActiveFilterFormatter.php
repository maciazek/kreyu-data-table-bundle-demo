<?php

namespace App\DataTable\Filter\Formatter;

use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatorInterface;

class DateRangeActiveFilterFormatter
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function __invoke(FilterData $data)
    {
        $value = $data->getValue();

        $dateFrom = $value['from'];
        $dateTo = $value['to'];
        $dateFormat = $this->translator->trans('date_format', [], 'messages');

        if ($dateFrom !== null && $dateTo === null) {
            return new TranslatableMessage('After %date%', ['%date%' => $dateFrom->format($dateFormat)], 'KreyuDataTable');
        }

        if ($dateFrom === null && $dateTo !== null) {
            return new TranslatableMessage('Before %date%', ['%date%' => $dateTo->format($dateFormat)], 'KreyuDataTable');
        }

        if ($dateFrom == $dateTo) {
            return $dateFrom->format($dateFormat);
        }

        return $dateFrom->format($dateFormat).' - '.$dateTo->format($dateFormat);
    }
}
