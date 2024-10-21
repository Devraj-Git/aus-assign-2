<?php
    view('includes/header');
?>

<!-- Layout wrapper -->
<div class="layout-wrapper">
    <!-- Header -->
    <div class="header d-print-none">
        <div class="header-container">
            <div class="header-body">
                <div class="header-body-left">
                    <ul class="navbar-nav">
                        <li class="nav-item navigation-toggler">
                            <a href="#" class="nav-link">
                                <i class="ti-menu"></i>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="header-body-right">
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link profile-nav-link dropdown-toggle" title="User menu" data-toggle="dropdown">
                                <span class="mr-2 d-sm-inline d-none"><?php echo ucfirst($user->username); ?></span>
                                <figure class="avatar avatar-sm">
                                    <img src = "<?php echo url('assets/images/person.png') ?>" class="rounded-circle" alt="avatar">
                                </figure>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-big">
                                <div class="text-center py-4" data-background-image="../../assets/media/image/image1.jpg">
                                    <figure class="avatar avatar-lg mb-3 border-0">
                                        <img src = "<?php echo url('assets/images/person.png') ?>" class="rounded-circle" alt="image">
                                    </figure>
                                    <h5 class="mb-0"><?php echo ucfirst($user->username); ?></h5>
                                </div>
                                <div class="list-group list-group-flush">
                                    <a href="<?= url('logout') ?>" class="list-group-item text-danger"  style="text-align:center;">Sign Out!</a>
                                </div>
                                
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <ul class="navbar-nav ml-auto">
                <li class="nav-item header-toggler">
                    <a href="#" class="nav-link">
                        <i class="ti-arrow-down"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <!-- ./ Header -->

    <!-- Content wrapper -->
    <div class="content-wrapper">
        <!-- begin::navigation -->
        <div class="navigation">
            <div class="logo">
                <a href="<?= url('') ?>">
                    <img src = "<?php echo url('assets/images/my-logo.png') ?>" alt="logo">
                </a>
            </div>
            <?php view('includes/navigation'); ?>
        </div>
        <!-- end::navigation -->

        <!-- Content body -->
        <div class="content-body">
            <!-- Content -->
            <div class="content">
                <div class="page-header">
                    <h2>Home page</h2>
                </div>
                
                <div class="row">
                    <p>Decrypted Custom Data  => <b>"<?php echo decrypt($user->custom_data); ?>"</b></p>
                    <!-- <div class="col-md-6">
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Alias eligendi explicabo impedit ipsa laudantium maiores numquam quidem repudiandae voluptate. Aspernatur inventore maiores quaerat. Aliquam at ea iusto porro repellendus suscipit.</p>
                    </div>  -->
                    <?php if ($synonyms) { ?>
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Synonyms for <?php echo $user->user_describe; ?></h6>
                                    <ul>
                                        <?php
                                        foreach ($synonyms as $synonym) {
                                                echo "<li>" . htmlspecialchars($synonym) . "</li>";
                                            }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <?php } else {
                        echo '<p>' . $error . '</p>';
                    } ?>
                </div>

            </div>
            <!-- ./ Content -->

            <!-- Footer -->
            <footer class="content-footer d-print-none">
                <div>   
                    © 2024 Assignment 
                </div>
            </footer>
            <!-- ./ Footer -->
        </div>
        <!-- ./ Content body -->

        
    </div>
    <!-- ./ Content wrapper -->
</div>
<!-- ./ Layout wrapper -->


<?php
    view('includes/footer');
?>