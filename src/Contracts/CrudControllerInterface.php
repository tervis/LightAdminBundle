<?php

declare(strict_types=1);

namespace Tervis\LightAdminBundle\Contracts;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
interface CrudControllerInterface
{
    /*
    public static function getEntityFqcn(): string;

    public function configureCrud(Crud $crud): Crud;

    public function configureAssets(Assets $assets): Assets;

    public function configureActions(Actions $actions): Actions;

    public function configureFilters(Filters $filters): Filters;
*/
    // /**
    //  * @return FieldInterface[]|string[]
    //  *
    //  * @psalm-return iterable<FieldInterface|string>
    //  */
    // public function configureFields(string $pageName): iterable;

    // /** @return KeyValueStore|Response */
    // public function index(AdminContext $context);

    // /** @return KeyValueStore|Response */
    // public function detail(AdminContext $context);

    // /** @return KeyValueStore|Response */
    // public function edit(AdminContext $context);

    // /** @return KeyValueStore|Response */
    // public function new(AdminContext $context);

    // /** @return KeyValueStore|Response */
    // public function delete(AdminContext $context);
    /*
    public function autocomplete(AdminContext $context): JsonResponse;

    public function configureResponseParameters(KeyValueStore $responseParameters): KeyValueStore;

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder;

    public function createEntity(string $entityFqcn);

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void;

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void;

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void;

    public function createEditFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface;

    public function createEditForm(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormInterface;

    public function createNewFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface;

    public function createNewForm(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormInterface;
    */
}
