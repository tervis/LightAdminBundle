{% extends '@LightAdmin/_layout.html.twig' %}

{% block title %}Hello <?= $class_name ?>!{% endblock %}

{% block body %}

<div class="example-wrapper">
    <h1>Hello {{ page_title }}! ✅</h1>

    This friendly message is coming from:
    <ul>
        <li>Your controller at <code><?= $root_directory ?>/<?= $controller_path ?></code></li>
        <li>Your template at <code><?= $root_directory ?>/<?= $relative_path ?></code></li>
    </ul>
</div>
{% endblock %}