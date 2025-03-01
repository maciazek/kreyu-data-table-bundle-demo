<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class SourceCodeModal
{
    public string $id = 'sourceCodeModal';
    public array $sourceCodes;

    public function mount(array $classes): void
    {
        $this->sourceCodes = array_map(function($item) {
            $item = new \ReflectionClass($item);

            return [
                'id' => $item->getShortName(),
                'name' => $item->getShortName().'.php',
                'content' => highlight_file($item->getFileName(), true),
            ];
        }, $classes);
    }
}
