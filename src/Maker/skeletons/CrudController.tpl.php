<?php

use Tervis\Bundle\LightAdminBundle\Maker\MakerLightAdminVariables;

/** @var MakerLightAdminVariables $variables */
/** @var string $namespace */
/** @var string $class_name */
echo "<?php\n";
?>

namespace <?php echo $namespace; ?>;

<?php echo $variables->useStatements; ?>

#[Route('/{_locale}/admin/<?php strtolower($class_name); ?>/crud', name: 'admin_<?php strtolower($entity_class_name); ?>_crud_')]
class <?php echo $class_name; ?> extends AbstractCrudController
{
public function __construct()
{
$this->configureAdmin(<?php strtolower($variables->entityClassDetails->getShortName()); ?>::class, <?= $entity_class_name; ?>FormType::class, 'admin_<?= strtolower($variables->entityClassDetails->getShortName()); ?>_crud_index');
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