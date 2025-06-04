<?= "<?php\n" ?>

declare(strict_types=1);

namespace <?= $class_data->getNamespace(); ?>;

<?= $class_data->getUseStatements(); ?>

<?= $class_data->getClassDeclaration(); ?> implements MenuFactoryInterface
{

     /**
     * Configure main menu items 
     *
     * @return array
     */
     public static function configureMenuItems(): array
     {
          return [
               MenuBuilder::linkToDashboard('Dashboard', 'bi:home')->setRouteName('<?= $dashboard_path ?>'),
          ];
     }

     /**
     * Configure user menu items
     *
     * @param UserInterface $user
     * @return array
     */
     public static function configureUserMenuItems(UserInterface $user): array
     {
          return [
               MenuBuilder::linkToLogout('Logout', 'bi:power')->setRouteName('app_logout'),
          ]
     }

     /**
     * Creates the main menu
     *
     * @return MainMenu
     */
     public static function createMainMenu(): MainMenu
     {
          return (new MainMenu(self::configureMenuItems()));
     }


     /**
     * Creates the menu of user actions displayed as a dropdown at the top
     * of the page and associated to the name of the currently logged in user.
     *
     * @return UserMenu
     */
     public static function createUserMenu(UserInterface $user): UserMenu
     {
          return (new UserMenu())
               ->setName($user->getUserIdentifier())
               ->setItems(self::configureUserMenuItems($user))
          ;
     }
}