<?php

declare(strict_types=1);

namespace Tervis\Bundle\LightAdminBundle\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class SwitchButton
{
    /**
     * The url to send form
     */
    public string $url;

    /**
     * The ID attribute for the switch button and its associated label.
     */
    public string $id;

    public string $token = 'switchButton';

    /**
     * The label text displayed next to the switch.
     */
    public string $label = '';

    /**
     * The initial boolean value of the switch (true for on, false for off).
     */
    public bool $value = false;

    /**
     * Whether the switch is disabled.
     */
    public bool $disabled = false;

    /**
     * Additional CSS classes to apply to the main container div.
     */
    public string $class = '';
}
