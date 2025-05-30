<?= "<?php\n"; ?>

declare(strict_types=1);

namespace <?= $namespace; ?>;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/{_locale}/admin/')]
class <?= $class_name; ?> extends AbstractController
{
    #[Route('', name: 'admin_dashboard')]
    public function index(): Response
    {
        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // return $this->redirectToRoute('admin_user_crud_index');

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirectToRoute('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @LightAdmin/page/content.html.twig)
        //
        return $this->render('some/path/my-dashboard.html.twig', [
            'page_title' => '<? $site_title; ?>',
            'page_pretitle' => 'Overview',
        ]);
    }

}