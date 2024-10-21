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
                                    <a href="logout" class="list-group-item text-danger"  style="text-align:center;">Sign Out!</a>
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
            <div class="content" style="padding-right: 0;">
                <div class="page-header">
                    <h2>Permission page</h2>
                </div>
                <div class="row">
                    <?php if ($all_users) { ?>
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">List of Users :-</h6>
                                    <div class="table-responsive">
                                        <form action="<?= url('update-role') ?>" method="post" id='roleForm'>
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">Username</th>
                                                    <th scope="col">Roles</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                        $counter = 1; 
                                                        foreach ($all_users as $all_user) { ?>
                                                            <tr>
                                                                <th scope="row"><?= $counter++; ?></th>
                                                                <td><?= $all_user->username; ?></td>
                                                                <td>
                                                                    <select name="user_type_<?= $all_user->id ?>" class="form-control" data-initial-value="<?= $all_user->user_type()->first()->role; ?>">
                                                                        <?php foreach ($users_type as $type) { 
                                                                            if($all_user->user_type()->first()->type === 'admin'){
                                                                                if ($type->type === 'admin'){ ?>
                                                                                    <option value="<?= $type->role ?>" <?= $all_user->user_type()->first()->role == $type->role ? 'selected' : '' ?>>
                                                                                        <?= $type->type ?>
                                                                                    </option> <?php
                                                                                }
                                                                            }
                                                                            else {
                                                                            ?>
                                                                            <option value="<?= $type->role ?>" <?= $all_user->user_type()->first()->role == $type->role ? 'selected' : '' ?>>
                                                                                <?= $type->type ?>
                                                                            </option>
                                                                        <?php }} ?>
                                                                    </select>
                                                                </td>
                                                                <td> 
                                                                    <button type="submit" class="btn btn-success btn-uppercase">
                                                                        <i class="ti-check-box mr-2"></i> Save
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php }  ?>
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

<script>
document.querySelectorAll('select').forEach(function(select) {
    select.addEventListener('change', function() {
        if (this.value !== this.getAttribute('data-initial-value')) {
            this.setAttribute('data-modified', 'true');
        } else {
            this.removeAttribute('data-modified');
        }
    });
});

document.getElementById('roleForm').addEventListener('submit', function(event) {
    let changesMade = false;
    document.querySelectorAll('select').forEach(function(select) {
        if (select.hasAttribute('data-modified')) {
            changesMade = true;
        } else {
            select.removeAttribute('name');
        }
    });
    if (!changesMade) {
        event.preventDefault();
        swal("No changes were made !", "Please update at least one role before submitting.", "warning");
    }
});

</script>

<?php
    view('includes/footer');
?>