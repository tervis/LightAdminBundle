<?php

declare(strict_types=1);

namespace Tervis\Bundle\LightAdminBundle\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class Alert
{
    public string $type = 'success';
    public string $message;
    public bool $withCloseButton = false;
}
