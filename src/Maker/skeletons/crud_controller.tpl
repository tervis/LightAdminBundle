<?= "<?php\n"; ?>

declare(strict_types=1);

namespace <?= $namespace; ?>;

use Tervis\Bundle\LightAdminBundle\Contracts\AbstractCrudController;
use Tervis\Bundle\LightAdminBundle\Dto\Action;
use Tervis\Bundle\LightAdminBundle\Dto\Crud;
use Tervis\Bundle\LightAdminBundle\Field\Field;
use Tervis\Bundle\LightAdminBundle\Options\PageAction;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/{_locale}/admin/<?= strtolower($entity_class_name); ?>/crud', name: 'admin_<?= strtolower($entity_class_name); ?>_crud_')]
class <?= $class_name; ?>CrudController extends AbstractCrudController
{
    public function __construct()
    {
        $this->configureAdmin(<?= $entity_class_name; ?>::class, <?= $entity_class_name; ?>FormType::class, 'admin_<?= strtolower($entity_class_name); ?>_crud_index');
    }

    public function getCrudPath(string $action): string
    {
        return sprintf('admin_<?= strtolower($entity_class_name); ?>_crud_%s', $action);
    }

    public function configureFields(string $pageName): iterable
    {
        yield Field::new(propertyName: 'email')->showOn(PageAction::List->value, PageAction::Details->value, PageAction::Edit->value, PageAction::New->value);
    }

    public function configureCrud(): Crud
    {
        return (new Crud())
            ->setPageTitle('<?= $entity_class_name; ?>')
            ->setPagePreTitle('<?= $entity_class_name; ?> - Management')
            ->setEntityName($this->entityShortName)
            //->disableaction(PageAction::BatchDelete->value)
            ->setActions([
                PageAction::List->value => Action::new($this->getCrudPath(PageAction::List->value))->setFields($this->getFields(PageAction::List->value)),
                PageAction::Details->value => Action::new($this->getCrudPath(PageAction::Details->value))->setFields($this->getFields(PageAction::Details->value))->setOptions([PageAction::Edit->value, PageAction::Delete->value]),
                PageAction::Edit->value => Action::new($this->getCrudPath(PageAction::Edit->value))->setFields($this->getFields(PageAction::Edit->value))->setOptions([]),
                PageAction::New->value => Action::new($this->getCrudPath(PageAction::New->value)),
                PageAction::Delete->value => Action::new($this->getCrudPath(PageAction::Delete->value)),
                PageAction::BatchDelete->value => Action::new($this->getCrudPath(PageAction::BatchDelete->value)),
            ]);
    }

    #[Route(name: PageAction::List->value, methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        return $this->listAction($request, $entityManager);
    }

    #[Route('/new', name: PageAction::New->value, methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        return $this->handleFormAction($request, $entityManager);
    }

    #[Route('/{id}/edit', name: PageAction::Edit->value, methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, <?= $class_name; ?> $entity): Response
    {
        return $this->handleFormAction($request, $entityManager, $entity);
    }

    #[Route('/{id}', name: PageAction::Details->value, methods: ['GET'])]
    public function details(EntityManagerInterface $entityManager, <?= $class_name; ?> $entity): Response
    {
        return $this->detailsAction($entityManager, $entity->getId());
    }

    #[Route('/{id}', name: PageAction::Delete->value, methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, <?= $class_name; ?> $entity): Response
    {
        return $this->deleteAction($request, $entityManager, $entity->getId());
    }

    #[Route('/batch/delete', name: PageAction::BatchDelete->value, methods: ['POST'])]
    public function batchDelete(Request $request): JsonResponse
    {
        return $this->batchDeleteAction($request, $this->entityManager);
    }
}