<!DOCTYPE html><html lang="en"><head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Filedash - File Manager Dashboard</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="<?php echo url('assets/images/my-logo.png') ?>">

    <!-- Plugin styles -->
    <link rel="stylesheet" href="<?php echo url('assets/css/bundle.css') ?>" type="text/css">

    <!-- App styles -->
    <link rel="stylesheet" href="<?php echo url('assets/css/app.min.css') ?>" type="text/css">

    <style>
        .alert {
        border-color: #9d0000 !important;
        color: red;
        border: 1px solid #880000;
    }
    </style>

</head>
<body class="error-page bg-white" style="background: url(<?php echo url('assets/images/image1.jpg') ?>)">
    <div>
    <?php view('includes/notification') ?>
    <h4 class="mb-0 font-weight-normal"><?php echo $data; ?></h4>
    <div class="my-4">
        <?php foreach (str_split($code) as $digit): ?>
            <span class="error-page-item font-weight-bold"><?php echo $digit; ?></span>
        <?php endforeach; ?>
    </div>
    <a href="<?= url('') ?>" class="btn bg-white btn-lg">Go Home</a>
</div>

<!-- Plugin scripts -->
<script src="<?php echo url('assets/js/bundle.js') ?>"></script>

<!-- App scripts -->
<script src="<?php echo url('assets/js/app.min.js') ?>"></script>


</body></html>