<?php

namespace Tervis\Bundle\LightAdminBundle\Maker;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Tervis\Bundle\LightadminBundle\Maker\ClassMaker;
use function Symfony\Component\String\u;

/**
 * Generates the PHP class needed to define a Dashboard controller.
 *
 */
// #[AsCommand(
//     name: 'make:light-admin:dashboard',
//     description: 'Creates a new LightAdmin Dashboard class',
// )]
class MakeAdminDashboard extends Command
{


    // private function getSiteTitle(string $projectDir): string
    // {
    //     $guessedTitle = (new AsciiSlugger())
    //         ->slug(basename($projectDir), ' ')
    //         ->title(true)
    //         ->trim()
    //         ->toString();

    //     return '' === $guessedTitle ? 'LightAdmin' : $guessedTitle;
    // }

    // private function getCommandHelp(): string
    // {
    //     return <<<'HELP'
    //         The <info>%command.name%</info> command creates a new LightAdmin Dashboard class
    //         in your application. Follow the steps shown by the command to configure the
    //         name and location of the new class.

    //         This command never changes or overwrites an existing class, so you can run it
    //         safely as many times as needed to create multiple dashboards.
    //         HELP;
    // }
}
