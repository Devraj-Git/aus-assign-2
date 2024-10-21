
<?php
    view('user/includes/header');
?>

<!-- begin::preloader-->
<div class="preloader">
    <div class="preloader-icon"></div>
</div>
<!-- end::preloader -->


<div class="form-wrapper">
    <?php view('includes/notification'); ?>
    <!-- logo -->
    <div id="logo">
        <img src="<?php echo url('assets/images/logo-dark.png') ?>" alt="image">
    </div>
    <!-- ./ logo -->

    
    <h5>Create account</h5>

    <!-- form -->
    <form action="<?= url('register') ?>" method="post">
        <div class="form-group">
            <input type="text" class="form-control" name="username" placeholder="Username*" required autofocus>
        </div>
        <div class="form-group">
            <input type="password" class="form-control" name="password" placeholder="Password*" required>
        </div>
        <div class="form-group">
            <input type="text" class="form-control" name="user_describe" placeholder="Describe yourself in single word.">
        </div>
        <div class="form-group">
            <textarea name="custom_data" class="form-control" placeholder="Some custom data of a theme of your choice (e.g. a short list of favourite Formula 1 drivers)."></textarea>
        </div>
        <!-- <div class="form-group">
            <input type="email" class="form-control" placeholder="Email" required>
        </div> -->
        <button class="btn btn-primary btn-block">Register</button>
        <hr>
        <p class="text-muted">Already have an account?</p>
        <a href="<?= url('login') ?>" class="btn btn-outline-light btn-sm">Sign in!</a>
    </form>
    <!-- ./ form -->


</div>


<?php
    view('user/includes/footer');
?>