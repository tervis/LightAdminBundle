<?= "<?php\n" ?>

declare(strict_types=1);

namespace <?= $class_data->getNamespace(); ?>;

<?= $class_data->getUseStatements(); ?>


#[Route('/{_locale}/admin/<?= $entity_var_singular ?>/crud', name: 'admin_<?= $entity_var_singular ?>_crud_')]
<?= $class_data->getClassDeclaration(); ?>

{
    public function __construct()
    {
        $this->configureAdmin(<?= $entity_class_name ?>::class, CrudFormType::class, 'admin_<?= $entity_var_singular ?>_crud_index');
    }

    public function getCrudPath(string $action): string
    {
        return sprintf('admin_<?= $entity_var_singular ?>_crud_%s', $action);
    }

    public function configureFields(): iterable
    {
        yield Field::new(propertyName: 'name');
    }

    public function configureCrud(): Crud
    {
        return (new Crud())
            ->setPageTitle('<?= $entity_class_name ?>')
            ->setPagePreTitle('<?= $entity_class_name ?> - Management')
            //->setEntityName($this->entityShortName)
            //->disableaction(PageAction::BatchDelete->value)
            ->setActions([
                PageAction::List->value => Action::new($this->getCrudPath(PageAction::List->value))->setFields($this->getFields(PageAction::List->value))->setOptions([PageAction::Details->value, PageAction::Edit->value, PageAction::Delete->value]),
                PageAction::Details->value => Action::new($this->getCrudPath(PageAction::Details->value))->setFields($this->getFields(PageAction::Details->value))->setOptions([PageAction::Edit->value, PageAction::Delete->value]),
                PageAction::Edit->value => Action::new($this->getCrudPath(PageAction::Edit->value))->setFields($this->getFields(PageAction::Edit->value))->setOptions([]),
                PageAction::New->value => Action::new($this->getCrudPath(PageAction::New->value)),
                PageAction::Delete->value => Action::new($this->getCrudPath(PageAction::Delete->value)),
                PageAction::BatchDelete->value => Action::new($this->getCrudPath(PageAction::BatchDelete->value)),
            ])
        ;
    }

    #[Route(name: PageAction::List->value, methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        return $this->listAction($request, $entityManager, []);
    }

    #[Route('/new', name: PageAction::New->value, methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        return $this->handleFormAction($request, $entityManager, []);
    }

    #[Route('/{id}/edit', name: PageAction::Edit->value, methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, <?= $entity_class_name ?> $<?= $entity_var_singular ?>): Response
    {
        return $this->handleFormAction($request, $entityManager, $<?= $entity_var_singular ?>, []);
    }

    #[Route('/{id}', name: PageAction::Details->value, methods: ['GET'])]
    public function details(EntityManagerInterface $entityManager, <?= $entity_class_name ?> $<?= $entity_var_singular ?>): Response
    {
        return $this->detailsAction($entityManager, $<?= $entity_var_singular ?>->getId(), []);
    }

    #[Route('/{id}', name: PageAction::Delete->value, methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, <?= $entity_class_name ?> $<?= $entity_var_singular ?>): Response
    {
        return $this->deleteAction($request, $entityManager, $<?= $entity_var_singular ?>->getId(), []);
    }

    #[Route('/batch/delete', name: PageAction::BatchDelete->value, methods: ['POST'])]
    public function batchDelete(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        return $this->batchDeleteAction($request, $entityManager);
    }

    #[Route('/toggle/{id}', name: 'toggle', methods: ['POST'])]
    public function toggle(Request $request, EntityManagerInterface $entityManager, PropertyAccessorInterface $propertyAccessor, int $id): JsonResponse
    {
        return $this->toggleAction($request, $entityManager, $propertyAccessor, $id);
    }
}