<?php

use Symfony\UX\Autocomplete\Maker\MakerAutocompleteVariables;

/** @var MakerAutocompleteVariables $variables */
/** @var string $namespace */
/** @var string $class_name */
echo "<?php\n";
?>

namespace <?php echo $namespace; ?>;

<?php echo $variables->useStatements; ?>

#[Route('/{_locale}/admin/<?php strtolower($class_name); ?>/crud', name: 'admin_<?php strtolower($entity_class_name); ?>_crud_')]
class <?php echo $class_name; ?> extends AbstractCrudController
{

}