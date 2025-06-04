<?= "<?php\n" ?>

declare(strict_types=1);

namespace <?= $class_data->getNamespace(); ?>;

<?= $class_data->getUseStatements(); ?>

#[Route('/{_locale}/admin/')]
<?= $class_data->getClassDeclaration(); ?>
{
    #[Route('', name: '<?= $route_name ?>')]
    public function <?= $method_name ?>(): Response
    {
        return $this->render('<?= $template_name ?>', [
            'page_title' => '<?= $class_data->getClassName() ?>',
            'page_pretitle' => 'Overview',
        ]);
    }
}