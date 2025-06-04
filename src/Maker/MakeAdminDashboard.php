<?php

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
use Symfony\Bundle\MakerBundle\Util\ClassSource\Model\ClassData;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

use Tervis\LightAdminBundle\Contracts\DashboardControllerInterface;
use Tervis\LightAdminBundle\Contracts\MenuFactoryInterface;
use Tervis\LightAdminBundle\Dto\MainMenu;
use Tervis\LightAdminBundle\Dto\UserMenu;
use Tervis\LightAdminBundle\Menu\MenuBuilder;

/**
 * Generates the PHP class needed to define a Dashboard controller.
 *
 */
class MakeAdminDashboard extends AbstractMaker
{

    private ClassData $controllerClassData;
    private ClassData $menuFactoryClassData;
    private string $twigTemplatePath;

    public static function getCommandName(): string
    {
        return 'make:light-admin:dashboard';
    }

    public static function getCommandDescription(): string
    {
        return 'Creates a new LightAdmin Dashboard controller class';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->addArgument('controller-class', InputArgument::OPTIONAL, \sprintf('Choose a name for your controller class (e.g. <fg=yellow>%sController</>)', 'Dashboard'))
            ->setHelp(<<<EOF
The <info>%command.name%</info> command creates a new LightAdmin Dashboard controller and Menu factory to handle menus.

Follow the steps shown by the command to select the Doctrine entity and the
location and namespace of the generated class.

This command never changes or overwrites an existing class, so you can run it
safely as many times as needed to create multiple CRUD controllers.
EOF);
    }


    public function interact(InputInterface $input, ConsoleStyle $io, Command $command): void
    {
        $controllerClass = $input->getArgument('controller-class');
        $controllerClassName = \sprintf('Controller\Admin\%s', $controllerClass);

        $this->controllerClassData = ClassData::create(
            class: $controllerClassName,
            suffix: 'Controller',
            extendsClass: AbstractController::class,
            useStatements: [
                Response::class,
                Route::class,
                UserMenu::class,
                MenuBuilder::class,
                DashboardControllerInterface::class,
                UserInterface::class,
            ]
        );

        $this->menuFactoryClassData = ClassData::create(
            class: 'Menu\Menu',
            suffix: 'Factory',
            //extendsClass: AbstractController::class,
            useStatements: [
                Response::class,
                Route::class,
                UserMenu::class,
                MainMenu::class,
                MenuBuilder::class,
                MenuFactoryInterface::class,
                UserInterface::class,
            ]
        );


        $templateName = $this->controllerClassData->getClassName(relative: true, withoutSuffix: true);

        // Convert the Twig template name into a file path where it will be generated.
        $this->twigTemplatePath = \sprintf('%s%s', Str::asFilePath($templateName), '/index.html.twig');
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $controllerPath = $generator->generateClassFromClassData(
            $this->controllerClassData,
            \dirname(__DIR__) . '/Maker/skeletons/Dashboard.tpl.php',
            [
                'route_path' => Str::asRoutePath($this->controllerClassData->getClassName(relative: true, withoutSuffix: true)),
                'route_name' => Str::AsRouteName($this->controllerClassData->getClassName(relative: true, withoutSuffix: true)),
                'method_name' => 'index',
                'with_template' => true,
                'template_name' => $this->twigTemplatePath,
            ],
            true
        );




        //     return str_starts_with($routeName, 'app_') ? $routeName : 'app_'.$routeName;

        $generator->generateClassFromClassData(
            $this->menuFactoryClassData,
            \dirname(__DIR__) . '/Maker/skeletons/MenuFactory.tpl.php',
            [
                'dashboard_path' => Str::asRouteName($this->controllerClassData->getClassName(relative: true, withoutSuffix: true)),
            ],
            false
        );


        $generator->generateTemplate(
            $this->twigTemplatePath,
            \dirname(__DIR__) . '/Maker/skeletons/twig_dashboard.tpl.php',
            [
                'controller_path' => $controllerPath,
                'root_directory' => $generator->getRootDirectory(),
                'class_name' => $this->controllerClassData->getClassName(),
            ]
        );

        $generator->writeChanges();

        $this->writeSuccessMessage($io);
        $io->text('Next: Open your new controller class and add some pages!');
    }

    public function configureDependencies(DependencyBuilder $dependencies): void {}
}
