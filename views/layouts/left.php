<?php
use app\components\AppHelper;
use dmstr\widgets\Menu;
?>

<?php
$headItems = [];
$headItems[] = [
    'label' => 'Home',
    'icon' => 'home',
    'url' => ['/site/index'],
];

$headResults = AppHelper::getMenuList();
if ($headResults) {
    foreach ($headResults as $headResult) {
        $detailItems = [];
        $submenuList = AppHelper::getSubmenuList($headResult['menuaccessid']);
        if ($submenuList) {
            foreach ($submenuList as $submenu) {
                $detailItems[] = [
                    'label' => $submenu['description'],
                    'icon' => $submenu['menuicon'],
                    'url' => ['/'.$submenu['menuurl']],
                ];
            }
        }
        $headItems[] = [
            'label' => $headResult['description'],
            'icon' => $headResult['menuicon'],
            'url' => '#',
            'items' => $detailItems
        ];
    }
}
?>
<aside class="main-sidebar">

    <section class="sidebar">
        <?= Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
                'items' => $headItems
            ]
        ) ?>

    </section>

</aside>
