<?php

declare(strict_types=1);

namespace Tervis\LightAdminBundle\Maker;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Doctrine\DoctrineHelper;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Util\UseStatementGenerator;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Tervis\LightAdminBundle\Contracts\AbstractCrudController;

/**
 * Generates the PHP class needed to define a CRUD controller.
 *
 */
class MakeCrudController extends AbstractMaker
{
    private string $className;
    private string $entityClass;

    public function __construct(
        private ?DoctrineHelper $doctrineHelper = null,
    ) {}

    public static function getCommandName(): string
    {
        return 'make:lightadmin:crud';
    }

    public static function getCommandDescription(): string
    {
        return 'Creates a new LightAdmin CRUD controller class';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->setHelp(<<<EOF
The <info>%command.name%</info> command creates a new LightAdmin CRUD controller
class to manage some Doctrine entity in your application.

Follow the steps shown by the command to select the Doctrine entity and the
location and namespace of the generated class.

This command never changes or overwrites an existing class, so you can run it
safely as many times as needed to create multiple CRUD controllers.
EOF);
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
        $dependencies->addClassDependency(FormInterface::class, 'symfony/form');
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command): void
    {
        if (null === $this->doctrineHelper) {
            throw new \LogicException('Somehow the DoctrineHelper service is missing from MakerBundle.');
        }

        $entities = $this->doctrineHelper->getEntitiesForAutocomplete();

        $question = new Question('The class name of the entity you want to generate CRUD');
        $question->setAutocompleterValues($entities);
        $question->setValidator(function ($choice) use ($entities) {
            return Validator::entityExists($choice, $entities);
        });

        $this->entityClass = $io->askQuestion($question);

        $this->className = Str::asClassName(\sprintf('%s CrudController', $this->entityClass));;
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        if (null === $this->doctrineHelper) {
            throw new \LogicException('Somehow the DoctrineHelper service is missing from MakerBundle.');
        }

        $entityClassDetails = $generator->createClassNameDetails(
            $this->entityClass,
            'Entity\\'
        );
        $entityDoctrineDetails = $this->doctrineHelper->createDoctrineDetails($entityClassDetails->getFullName());

        $classDetails = $generator->createClassNameDetails(
            $this->className,
            'Controller\\Admin\\',
        );

        $repositoryClassDetails = $entityDoctrineDetails->getRepositoryClass() ? $generator->createClassNameDetails('\\' . $entityDoctrineDetails->getRepositoryClass(), '') : null;

        // use App\Entity\Category;
        // use App\Repository\CategoryRepository;
        $useStatements = new UseStatementGenerator([
            $entityClassDetails->getFullName(),
            EntityManagerInterface::class,
            Route::class,
            Request::class,
            Response::class,
            AbstractCrudController::class
        ]);

        $variables = new MakerLightAdminVariables(
            useStatements: $useStatements,
            entityClassDetails: $entityClassDetails,
            repositoryClassDetails: $repositoryClassDetails,
        );
        $generator->generateClass(
            $classDetails->getFullName(),
            __DIR__ . '/skeletons/CrudController.tpl.php',
            [
                'variables' => $variables,
            ]
        );

        $generator->writeChanges();

        $this->writeSuccessMessage($io);
    }
}
