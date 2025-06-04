<?php

declare(strict_types=1);


namespace Tervis\LightAdminBundle\Maker;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\InflectorFactory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Doctrine\DoctrineHelper;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Bundle\MakerBundle\Renderer\FormTypeRenderer;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Util\ClassSource\Model\ClassData;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Validator\Validation;

use Tervis\LightAdminBundle\Contracts\AbstractCrudController;
use Tervis\LightAdminBundle\Dto\Action;
use Tervis\LightAdminBundle\Dto\Crud;
use Tervis\LightAdminBundle\Field\Field;
use Tervis\LightAdminBundle\Form\CrudFormType;
use Tervis\LightAdminBundle\Options\PageAction;

final class MakeAdminCrudController extends AbstractMaker
{

    private Inflector $inflector;
    private string $controllerClassName;
    private bool $generateTests = false;

    public function __construct(private DoctrineHelper $doctrineHelper, private FormTypeRenderer $formTypeRenderer)
    {
        $this->inflector = InflectorFactory::create()->build();
    }

    public static function getCommandName(): string
    {
        return 'make:light-admin:crud';
    }

    public static function getCommandDescription(): string
    {
        return 'Create CRUD for Doctrine entity class';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->addArgument('entity-class', InputArgument::OPTIONAL, \sprintf('The class name of the entity to create CRUD (e.g. <fg=yellow>%s</>)', Str::asClassName(Str::getRandomTerm())))
            ->setHelp($this->getHelpFileContents('MakeCrud.txt'))
        ;

        $inputConfig->setArgumentAsNonInteractive('entity-class');
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command): void
    {
        if (null === $input->getArgument('entity-class')) {
            $argument = $command->getDefinition()->getArgument('entity-class');

            $entities = $this->doctrineHelper->getEntitiesForAutocomplete();

            $question = new Question($argument->getDescription());
            $question->setAutocompleterValues($entities);

            $value = $io->askQuestion($question);

            $input->setArgument('entity-class', $value);
        }

        $defaultControllerClass = Str::asClassName(\sprintf('%s CrudController', $input->getArgument('entity-class')));

        $this->controllerClassName = $io->ask(
            \sprintf('Choose a name for your controller class (e.g. <fg=yellow>%s</>)', $defaultControllerClass),
            $defaultControllerClass
        );
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $entityClassDetails = $generator->createClassNameDetails(
            Validator::entityExists($input->getArgument('entity-class'), $this->doctrineHelper->getEntitiesForAutocomplete()),
            'Entity\\'
        );

        $entityDoctrineDetails = $this->doctrineHelper->createDoctrineDetails($entityClassDetails->getFullName());

        $repositoryVars = [];
        $repositoryClassName = EntityManagerInterface::class;

        if (null !== $entityDoctrineDetails->getRepositoryClass()) {
            $repositoryClassDetails = $generator->createClassNameDetails(
                '\\' . $entityDoctrineDetails->getRepositoryClass(),
                'Repository\\',
                'Repository'
            );

            $repositoryClassName = $repositoryClassDetails->getFullName();

            $repositoryVars = [
                'repository_full_class_name' => $repositoryClassName,
                'repository_class_name' => $repositoryClassDetails->getShortName(),
                'repository_var' => lcfirst($this->inflector->singularize($repositoryClassDetails->getShortName())),
            ];
        }

        $controllerClassDetails = $generator->createClassNameDetails(
            $this->controllerClassName,
            'Controller\\Admin\\Crud',
            'CrudController'
        );

        $controllerClassData = ClassData::create(
            class: \sprintf('Controller\Admin\Crud\%s', $this->controllerClassName),
            suffix: 'CrudController',
            extendsClass: AbstractCrudController::class,
            useStatements: [
                $entityClassDetails->getFullName(),
                $repositoryClassName,
                AbstractController::class,
                Request::class,
                Response::class,
                JsonResponse::class,
                Route::class,
                AbstractCrudController::class,
                CrudFormType::class,
                PageAction::class,
                Crud::class,
                Action::class,
                Field::class,
                PropertyAccessorInterface::class,
            ],
        );

        $entityVarPlural = lcfirst($this->inflector->pluralize($entityClassDetails->getShortName()));
        $entityVarSingular = lcfirst($this->inflector->singularize($entityClassDetails->getShortName()));

        $entityTwigVarPlural = Str::asTwigVariable($entityVarPlural);
        $entityTwigVarSingular = Str::asTwigVariable($entityVarSingular);

        $routeName = Str::asRouteName($controllerClassDetails->getRelativeNameWithoutSuffix());
        $templatesPath = Str::asFilePath($controllerClassDetails->getRelativeNameWithoutSuffix());

        if (EntityManagerInterface::class !== $repositoryClassName) {
            $controllerClassData->addUseStatement(EntityManagerInterface::class);
        }

        $generator->generateController(
            $controllerClassData->getFullClassName(),
            \dirname(__DIR__) . '/Maker/skeletons/CrudController.tpl.php',
            array_merge(
                [
                    'class_data' => $controllerClassData,
                    'entity_class_name' => $entityClassDetails->getShortName(),
                    //'form_class_name' => $formClassDetails->getShortName(),
                    'route_path' => Str::asRoutePath($controllerClassDetails->getRelativeNameWithoutSuffix()) . '/crud',
                    'route_name' => $routeName . '_crud',
                    'templates_path' => $templatesPath,
                    'entity_var_plural' => $entityVarPlural,
                    'entity_twig_var_plural' => $entityTwigVarPlural,
                    'entity_var_singular' => $entityVarSingular,
                    'entity_twig_var_singular' => $entityTwigVarSingular,
                    'entity_identifier' => $entityDoctrineDetails->getIdentifier(),
                ],
                $repositoryVars
            )
        );

        // $this->formTypeRenderer->render(
        //     $formClassDetails,
        //     $entityDoctrineDetails->getFormFields(),
        //     $entityClassDetails
        // );

        $generator->writeChanges();

        $this->writeSuccessMessage($io);

        $io->text(\sprintf('Next: Check your new CRUD by going to <fg=yellow>/en/admin%s/crud/</>', Str::asRoutePath($controllerClassDetails->getRelativeNameWithoutSuffix())));
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
        $dependencies->addClassDependency(
            Route::class,
            'router'
        );

        $dependencies->addClassDependency(
            AbstractType::class,
            'form'
        );

        $dependencies->addClassDependency(
            Validation::class,
            'validator'
        );

        $dependencies->addClassDependency(
            TwigBundle::class,
            'twig-bundle'
        );

        $dependencies->addClassDependency(
            DoctrineBundle::class,
            'orm'
        );

        $dependencies->addClassDependency(
            CsrfTokenManager::class,
            'security-csrf'
        );
    }
}
