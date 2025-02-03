<?php
/** @var UserSession $userSession */
/** @var string $page */
global $rootURL;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="<?= $rootURL?>/favicon.ico" type="image/vnd.microsoft.icon" />
    <title>CLASSify</title>
    <script type="text/javascript" src="<?= $rootURL?>/js/jquery.min.js"></script>
    <script type="text/javascript" src="<?= $rootURL?>/js/popper.min.js"></script>
    <link type="text/css" rel="stylesheet" href="<?= $rootURL?>/css/floating-labels.css">
    <link type="text/css" rel="stylesheet" href="<?= $rootURL?>/css/dataTables.bootstrap4.min.css">
    <link type="text/css" rel="stylesheet" href="<?= $rootURL?>/css/responsive.bootstrap4.min.css">
    <link type="text/css" rel="stylesheet" href="<?= $rootURL?>/css/buttons.bootstrap4.min.css">
    <link type="text/css" rel="stylesheet" href="<?= $rootURL?>/css/daterangepicker.css">
    <link type="text/css" rel="stylesheet" href="<?= $rootURL?>/css/font-awesome.min.css">
    <link type="text/css" rel="stylesheet" href="<?= $rootURL?>/css/toastify.min.css">
    <link type="text/css" rel="stylesheet" href="<?= $rootURL?>/css/bootstrap.min.css">
    <link type="text/css" rel="stylesheet" href="<?= $rootURL?>/css/global.css">
    <link type="text/css" rel="stylesheet" href="<?= $rootURL?>/css/bootstrap-select.css">
</head>
<body>
<nav class="navbar navbar-dark sticky-top navbar-expand-lg flex-md-nowrap p-0">
    <a class="navbar-brand col-2 col-sm-2 col-md-2 mr-0" href="<?= $rootURL ?>/">CLASSify <br> (Not HIPAA Compliant)</a>
    <ul class="navbar-nav mr-auto">
    </ul>

    <?php if (!is_null($userSession)) : ?>
        <form class="form-inline my-2 my-lg-0 mr-2">
            <?php if (!is_null($userSession->getUser())) : ?>
                <?php if (is_null($userSession->getUser()->getFullName()) || empty($userSession->getUser()->getFullName())) : ?>
                    <span class="responsive-text" id="login-user"><?php echo $userSession->getUser()->getEPPN(); ?></span>
                <?php else : ?>
                    <span class="responsive-text" id="login-user"><?php echo $userSession->getUser()->getFullName(); ?></span>
                <?php endif; ?>
            <?php endif; ?>
            <a class="btn btn-sm btn-primary my-2 my-sm-0 ml-2" href="<?= $rootURL ?>/logout">Logout</a>
        </form>
    <?php endif; ?>
</nav>
<div class="container-fluid">
    <div class="row">
        <?php include_once __DIR__ . '/_menu.php'; ?>

        <main role="main" class="col-12 col-md-11 bg-faded py-3 flex-grow-1">