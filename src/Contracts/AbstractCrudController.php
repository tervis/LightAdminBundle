<?php

declare(strict_types=1);

namespace Tervis\LightAdminBundle\Contracts;

use Tervis\LightAdminBundle\Dto\Crud;
use Tervis\LightAdminBundle\Dto\Pagination;
use Tervis\LightAdminBundle\Form\CrudFormType;
use Tervis\LightAdminBundle\Options\PageAction;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

abstract class AbstractCrudController extends AbstractController
{
    /**
     * Undocumented function
     *
     * @return Crud
     */
    abstract public function configureCrud(): Crud;

    /**
     * Undocumented function
     *
     * @return iterable
     */
    abstract public function configureFields(): iterable;

    protected string $entityClass;
    protected string $entityShortName;
    protected string $formTypeClass;

    protected string $listTemplate;
    protected string $editTemplate;
    protected string $newTemplate;
    protected string $detailsTemplate;

    protected string $redirectRoute;

    protected int $itemsPerPage = 10; // For pagination

    /**
     * Override in child controllers to define entity and form.
     *
     * @param string $entityClass
     * @param string $formTypeClass
     * @param string $redirectRoute
     * @return void
     */
    protected function configureAdmin(string $entityClass, string $formTypeClass, string $redirectRoute): void
    {
        $this->entityClass = $entityClass;
        $this->entityShortName = (new \ReflectionClass($this->entityClass))->getShortName();
        $this->formTypeClass = $formTypeClass;
        $this->redirectRoute = $redirectRoute;

        // Default templates, can be overridden
        $this->listTemplate = '@LightAdmin/listView.html.twig';
        $this->editTemplate = '@LightAdmin/editView.html.twig';
        $this->newTemplate = '@LightAdmin/editView.html.twig';
        $this->detailsTemplate = '@LightAdmin/detailView.html.twig';
    }

    protected function getFields(string $action): array
    {
        return array_filter([...$this->configureFields()], function ($item) use ($action) {
            if (empty($item->showOnPages)) {
                return true;
            }

            if ($action === 'form') {
                return ($item->hasPage(PageAction::Edit->value) || $item->hasPage(PageAction::New->value));
            }
            return $item->hasPage($action);
        });
    }

    /**
     * Allows child controllers to customize the QueryBuilder for the list action.
     * For example, to add WHERE clauses, ORDER BY, or JOINs.
     *
     * The default alias for the root entity in the QueryBuilder is 'e'.
     *
     * @param QueryBuilder $queryBuilder The QueryBuilder instance for the list query.
     * @return void
     */
    protected function alterListQueryBuilder(QueryBuilder $queryBuilder): void
    {
        // Default implementation does nothing.
        // Child controllers can override this method to add their customizations.
        // Example:
        // $rootAlias = $queryBuilder->getRootAliases()[0];
        // $queryBuilder->andWhere($rootAlias . '.isActive = :isActive')->setParameter('isActive', true);
        // $queryBuilder->orderBy($rootAlias . '.createdAt', 'DESC');
    }

    protected function addSearchConditions(): void {}

    /**
     * Generic list action with basic pagination.
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    protected function listAction(Request $request, EntityManagerInterface $entityManager, array $context = []): Response
    {
        if (!isset($this->entityClass)) {
            throw new \LogicException('Entity class not configured. Call configureAdmin() in your controller.');
        }

        $crud = $this->configureCrud();

        $repository = $entityManager->getRepository($this->entityClass);
        // The default alias 'e' is used here.
        // Child controllers should be aware if they need to reference it or get it via $queryBuilder->getRootAliases()[0]
        $queryBuilder = $repository->createQueryBuilder('e');

        // Call the hook method to allow customization
        $this->alterListQueryBuilder($queryBuilder);

        // Pagination
        $page = $request->query->getInt('page', 1);
        $offset = ($page - 1) * $this->itemsPerPage;

        // Clone the QueryBuilder for counting to avoid issues with setMaxResults/setFirstResult
        // and to avoid altering the original QueryBuilder's DQL parts used for fetching items.
        $countQueryBuilder = clone $queryBuilder;

        // Get the root alias dynamically for the count query
        $rootAliases = $countQueryBuilder->getRootAliases();
        if (empty($rootAliases)) {
            // This should theoretically not happen if QueryBuilder is constructed correctly
            throw new \LogicException('QueryBuilder does not have a root alias for counting.');
        }
        $rootAlias = $rootAliases[0];

        // Reset parts not needed or problematic for COUNT.
        // OrderBy is not needed for count and can sometimes cause issues if not properly handled by DB with complex queries.
        $countQueryBuilder->resetDQLPart('orderBy');
        // GroupBy should also be handled carefully for counts. For complex cases with GroupBy,
        // Doctrine's Paginator tool is more robust. This implementation assumes a simple count on the root entity.

        $totalItems = (int)$countQueryBuilder
            ->select("COUNT(DISTINCT {$rootAlias}.id)") // Using DISTINCT for robustness if joins in alterListQueryBuilder cause duplication of root entity rows.
            ->getQuery()
            ->getSingleScalarResult();

        $items = $queryBuilder
            ->setMaxResults($this->itemsPerPage)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();

        $totalPages = 1; // Default to 1 page
        if ($this->itemsPerPage > 0 && $totalItems > 0) {
            $totalPages = (int)ceil($totalItems / $this->itemsPerPage);
        }
        $totalPages = max(1, $totalPages); // Ensure at least one page


        $crud->setCollection($items);

        $pagination = new Pagination(page: $page, totalPages: $totalPages, itemsPerPage: $this->itemsPerPage, totalItems: $totalItems);

        $defaultContext = [
            'crud' => $crud,
            'pagination' => $pagination,
            'entityName' => $this->entityShortName,
            'redirectRoute' => $this->redirectRoute,
            'page_title' => 'Index',
            'page_pretitle' => $this->entityShortName,
        ];

        $context = array_merge($defaultContext, $context);

        return $this->render($this->listTemplate, $context);
    }

    /**
     * Generic create/edit action.
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param [type] $entity
     * @return Response
     */
    protected function handleFormAction(Request $request, EntityManagerInterface $entityManager, $entity = null, array $context = []): Response
    {
        if (!isset($this->formTypeClass)) {
            throw new \LogicException('Form type class not configured. Call configureAdmin() in your controller.');
        }

        $withCrudFields = true;

        $isNew = $entity === null;
        if ($isNew) {
            $entity = new ($this->entityClass)();
        }

        $crud = $this->configureCrud(); // Get the CRUD configuration
        $fields = $this->getFields('form'); // Get fields configured for 'form' action

        // Use a generic form type and pass fields
        // This requires a custom 'AdminFormType' that can iterate over fields
        if ($this->formTypeClass instanceof CrudFormType) {
            $form = $this->createForm(CrudFormType::class, $entity, [
                'crud_fields' => $fields, // Pass the Field objects to the form type
            ]);
        } else {
            $withCrudFields = false;
            $form = $this->createForm($this->formTypeClass, $entity);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($entity);
            $entityManager->flush();

            $this->addFlash('success', 'Item ' . ($isNew ? 'created' : 'updated') . ' successfully.');

            return $this->redirectToRoute($this->redirectRoute, [], Response::HTTP_SEE_OTHER);
        }

        $defaultContext = [
            'form' => $form->createView(),
            'item' => $entity,
            'isNew' => $isNew,
            'crud' => $crud,
            'entityName' => $this->entityShortName,
            'redirectRoute' => $this->redirectRoute,
            'page_title' => $isNew ? 'Create new' : 'Edit',
            'page_pretitle' => $this->entityShortName,
        ];

        // Pass fields to the Twig template for dynamic rendering
        $context = array_merge($defaultContext, $context, $withCrudFields ? ['crud_fields' => $fields,] : []);

        return $this->render($isNew ? $this->newTemplate : $this->editTemplate, $context);
    }

    /**
     * Generic item details action
     *
     * @param EntityManagerInterface $entityManager
     * @param int|string $id
     * @return Response
     */
    protected function detailsAction(EntityManagerInterface $entityManager, $id, array $context = []): Response
    {
        if (!isset($this->entityClass)) {
            throw new \LogicException('Entity class not configured. Call configureAdmin() in your controller.');
        }

        $entity = $entityManager->getRepository($this->entityClass)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No ' . (new \ReflectionClass($this->entityClass))->getShortName() . ' found for id ' . $id);
        }

        $crud = $this->configureCrud();

        $defaultContext = [
            'item' => $entity,
            'crud' => $crud,
            'entityName' => $this->entityShortName,
            'redirectRoute' => $this->redirectRoute,
            'page_title' => 'Details',
            'page_pretitle' => $this->entityShortName,
        ];
        $context = array_merge($defaultContext, $context);

        return $this->render($this->detailsTemplate, $context);
    }

    /**
     * Generic delete action
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param int|string $id
     * @return Response
     */
    protected function deleteAction(Request $request, EntityManagerInterface $entityManager, $id): Response
    {
        if (!isset($this->entityClass)) {
            throw new \LogicException('Entity class not configured. Call configureAdmin() in your controller.');
        }

        $entity = $entityManager->getRepository($this->entityClass)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException(sprintf('No %s found for id %s', $this->entityShortName, $id));
        }

        if ($this->isCsrfTokenValid('delete' . $entity->getId(), $request->request->get('_token'))) {
            $entityManager->remove($entity);
            $entityManager->flush();
            $this->addFlash('success', 'Item deleted successfully.');
        } else {
            $this->addFlash('error', 'Invalid CSRF token.');
        }

        return $this->redirectToRoute($this->redirectRoute, [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Generic delete action handling JSON payload.
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    protected function batchDeleteAction(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!isset($this->entityClass)) {
            throw new \LogicException('Entity class not configured. Call configureAdmin() in your controller.');
        }

        $data = $this->getJsonPayload($request);

        $token = $data['_token'] ?? null;
        $items = $data['items'] ?? [];

        if (empty($items)) {
            return new JsonResponse(['message' => 'No items provided for deletion.'], Response::HTTP_BAD_REQUEST);
        }

        $repository = $entityManager->getRepository($this->entityClass);

        if ($this->isCsrfTokenValid(PageAction::BatchDelete->value, $token)) {
            try {
                $entityManager->wrapInTransaction(function ($em) use ($repository, $items) {
                    foreach ($items as $item) {
                        $entity = $repository->find($item);
                        if ($entity) {
                            $em->remove($entity);
                        }
                    }
                });

                // return a JSON success response
                return new JsonResponse(['message' => 'Items deleted successfully.', 'status' => 'success'], Response::HTTP_OK);
            } catch (\Exception $e) {
                // Log the exception for debugging
                // $this->logger->error('Batch deletion failed: ' . $e->getMessage());

                // Return a JSON error response
                return new JsonResponse(
                    ['message' => 'An error occurred during deletion: ' . $e->getMessage(), 'status' => 'error'],
                    Response::HTTP_INTERNAL_SERVER_ERROR // 500 status code for server errors
                );
            }
        } else {
            // Return a JSON error response for invalid CSRF token
            return new JsonResponse(['message' => 'Invalid CSRF token.', 'status' => 'error'], Response::HTTP_FORBIDDEN); // 403 status code
        }
    }

    /**
     * Toggle boolean fields
     * 
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param PropertyAccessorInterface $propertyAccessor
     * @param string|int $id
     * @return JsonResponse
     */
    protected function toggleAction(Request $request, EntityManagerInterface $entityManager, PropertyAccessorInterface $propertyAccessor, $id): JsonResponse
    {
        $data = $this->getJsonPayload($request);

        $token = $data['_token'] ?? null;
        $propertyPath = $data['propertyName'] ?? null;

        $privateToken = sprintf('%s-switch-button-%s', $propertyPath, $id);

        $entity = $entityManager->getRepository($this->entityClass)->find($id);

        $status = JsonResponse::HTTP_INTERNAL_SERVER_ERROR;
        $data = 'Unable to toggle value: ' . $propertyPath;

        if ($this->isCsrfTokenValid($privateToken, $token) === true) {
            if ($entity) {
                // Use PropertyAccessor to handle getters/setters
                if ($propertyAccessor->isReadable($entity, $propertyPath) && $propertyAccessor->isWritable($entity, $propertyPath)) {
                    $currentValue = $propertyAccessor->getValue($entity, $propertyPath);
                    $propertyAccessor->setValue($entity, $propertyPath, !$currentValue);
                    $entityManager->flush();

                    $status = JsonResponse::HTTP_OK;
                    $data = 'Success';
                } else {
                    $data = 'Property ' . $propertyPath . ' is not readable or writable.';
                }
            } else {
                $status = JsonResponse::HTTP_NOT_FOUND;
                $data = 'Entity not found.';
            }
        } else {
            $data = 'Invalid CSRF token.';
        }

        return $this->json($data, $status);
    }

    /**
     * Helper method to get JSON payload from the request body.
     *
     * @param Request $request
     * @return array
     * @throws BadRequestHttpException If the request body is not valid JSON.
     */
    private function getJsonPayload(Request $request): array
    {
        $content = $request->getContent();

        if (empty($content)) {
            return []; // No content in the request body
        }

        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new BadRequestHttpException('Invalid JSON payload: ' . json_last_error_msg());
        }

        if (!is_array($data)) {
            throw new BadRequestHttpException('Invalid JSON payload: Expected a JSON object or array.');
        }

        return $data;
    }
}
