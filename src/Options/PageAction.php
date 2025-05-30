<?php

declare(strict_types=1);

namespace Tervis\Bundle\LightAdminBundle\Options;

enum PageAction: string
{
    case List = 'index';
    case Details = 'details';
    case Edit = 'edit';
    case New = 'new';
    case Delete = 'delete';
    case BatchDelete = 'batch_delete';
}
