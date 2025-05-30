<?php

namespace Tervis\Bundle\LightAdminBundle\Maker;

use Symfony\Bundle\MakerBundle\Util\ClassNameDetails;
use Symfony\Bundle\MakerBundle\Util\UseStatementGenerator;

/**
 * @internal
 */
class MakerLightAdminVariables
{
    public function __construct(
        public UseStatementGenerator $useStatements,
        public ClassNameDetails $entityClassDetails,
        public ?ClassNameDetails $repositoryClassDetails = null,
    ) {}
}
