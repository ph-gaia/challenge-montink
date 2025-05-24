<!doctype html>
<html>

<head>
    <title><?= $this->view->title ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,minimum-scale=1">

    <?php include_once 'Common/styles.php'; ?>
</head>

<body>

    <div id="wrapper">
    <?php include_once 'Partials/menu_vertical.php'; ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include_once 'Partials/menu_horizontal_top.php'; ?>
                <div class="container-fluid">
                    <?= $this->getContent(); ?>
                </div>
            </div>
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span> Montink &copy; <?= date('Y'); ?></span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <?php include_once 'Common/scripts.php'; ?>
</body>

</html>