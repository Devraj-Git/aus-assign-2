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
                        <li class="nav-item">
                            <div class="header-search-form">
                                <form id="searchByip" action="<?= url('access') ?>" method="post">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <button class="btn">
                                                <i class="ti-search"></i>
                                            </button>
                                        </div>
                                        
                                        <?php $search = get_session('search'); $placeholder = ($search == null || $search == '') ? null :$search;?>
                                        <input type="text" name="ipaddress" class="form-control" placeholder='Search By IP address...' value="<?php echo htmlspecialchars($placeholder, ENT_QUOTES, 'UTF-8'); ?>" required oninput="toggleCloseButton()">
                                        <div class="input-group-append">
                                            <button id="closeBtn" class="btn header-search-close-btn" type="button" onclick="closeForm()">
                                                <i data-feather="x"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
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
                <a href="/">
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
                    <h2>Access page</h2>
                </div>

                <div class="row">
                    <?php if (!empty($access_log['data'])) { ?>
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">List of all access logs :-</h6>
                                    <form id="viewType" style="margin:20px;" action="<?= url('changeView') ?>" method="POST">
                                        <div class="form-check form-check-inline">
                                            <b> Select the view type :- </b>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" name="changeView" type="radio" id="inlineCheckbox1" value="table" <?php if(get_session('view') === 'table') echo "checked"; ?>>
                                            <label class="form-check-label" for="inlineCheckbox1">Table</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" name="changeView" type="radio" id="inlineCheckbox2" value="list" <?php if(get_session('view') === 'list') echo "checked"; ?>>
                                            <label class="form-check-label" for="inlineCheckbox2">List</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" name="changeView" type="radio" id="inlineCheckbox3" value="json" <?php if(get_session('view') === 'json') echo "checked"; ?>>
                                            <label class="form-check-label" for="inlineCheckbox3">Json</label>
                                        </div>
                                    </form>
                                    <?php if(get_session('view') === 'table'){ ?>
                                        <div class="table-responsive">
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                        <th scope="col">#</th>
                                                        <th scope="col">Username</th>
                                                        <th scope="col">Roles</th>
                                                        <th scope="col">Action</th>
                                                        <th scope="col">Ip Address</th>
                                                        <th scope="col">Description</th>
                                                        <th scope="col">Time</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                            $counter = 1;
                                                            foreach ($access_log['data'] as $log) { ?>
                                                                <tr>
                                                                    <th scope="row"><?= (($access_log['pageNo']-1)*10)+ $counter++; ?></th>
                                                                    <td>
                                                                        <?php 
                                                                        if(isset($log->user_id)){
                                                                            echo $log->user()->first()->username;
                                                                        } else { ?> 
                                                                            <div class="bg-info" style="padding: 2px;text-align: center;border-radius: 10px;">
                                                                                None
                                                                            </div> 
                                                                        <?php } ?></td>
                                                                    <td><?php if(isset($log->user_id)){echo $log->user()->first()->user_type()->first()->type;} 
                                                                    else {?> 
                                                                        <div class="bg-info" style="padding: 2px;text-align: center;border-radius: 10px;">
                                                                            None
                                                                        </div> 
                                                                    <?php } ?></td>
                                                                    <td><?= $log->action; ?></td>
                                                                    <td><?= $log->ip_address; ?></td>
                                                                    <td>
                                                                        <div class="bg-<?php 
                                                                        if ($log->identifier==='Logged-In-User') echo 'success'; 
                                                                        elseif($log->identifier==='Non-logged-in User') echo 'info'; 
                                                                        else echo 'warning'; 
                                                                        
                                                                        ?> 
                                                                        " style="padding: 2px;text-align: center;border-radius: 10px;">
                                                                        <?= $log->identifier ?>
                                                                        </div> 
                                                                    </td>
                                                                    <td><?= dt_format($log->created_at); ?></td>
                                                                </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                                <?php view('includes/pagination', ['pageNo' => $access_log['pageNo'], 'pages' => $access_log['pages'], 'url' => url('access')]); ?>
                                        </div>
                                    <?php } elseif(get_session('view') === 'list'){ ?>
                                        <div class="list-container">
                                            <ul>
                                                <?php
                                                    $counter = 1;
                                                    foreach ($access_log['data'] as $log) { ?>
                                                        <li>
                                                            <strong>#<?= (($access_log['pageNo']-1)*10)+ $counter++; ?></strong><br>
                                                            <strong>Username:</strong> <?php if(isset($log->user_id)){echo $log->user()->first()->username; } else { echo "None" ; } ?><br>
                                                            <strong>Roles:</strong> <?php if(isset($log->user_id)){echo $log->user()->first()->user_type()->first()->type; } else { echo "None" ; } ?><br>
                                                            <strong>Action:</strong> <?= $log->action; ?><br>
                                                            <strong>Ip Address:</strong> <?= $log->ip_address; ?><br>
                                                            <strong>Description:</strong> <?= $log->identifier; ?><br>
                                                            <strong>Time:</strong> <?= dt_format($log->created_at); ?><br>
                                                        </li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                        <?php view('includes/pagination', ['pageNo' => $access_log['pageNo'], 'pages' => $access_log['pages'], 'url' => url('access')]); ?>
                                    <?php } elseif(get_session('view') === 'json'){ ?>
                                        <div class="json-list">
                                            <?php
                                                $counter = 1;
                                                foreach ($access_log['data'] as $log) { ?>
                                                    <pre>
{
    "#": <?= (($access_log['pageNo']-1)*10)+ $counter++; ?>,
    "username": "<?php if(isset($log->user_id)){echo $log->user()->first()->username; } else { echo "None" ; } ?>",
    "roles": "<?php if(isset($log->user_id)){echo $log->user()->first()->user_type()->first()->type; } else { echo "None" ; } ?>",
    "action": "<?= $log->action; ?>",
    "ip_address": "<?= $log->ip_address; ?>",
    "description": "<?= $log->identifier; ?>",
    "time": "<?= dt_format($log->created_at); ?>"
}
                                                    </pre>
                                            <?php } ?>
                                            <?php view('includes/pagination', ['pageNo' => $access_log['pageNo'], 'pages' => $access_log['pages'], 'url' => url('access')]); ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php } else{  ?>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2000 2000" style="width: 100%;  height: 300px;"><circle cx="994.49" cy="992.66" r="800" fill="#3555e7"/><path fill="#121a49" d="m1589.62,1527.28H399.37c146.45,162.92,358.82,265.38,595.12,265.38s448.68-102.47,595.13-265.38Z"/><rect width="62.88" height="163.23" x="875.41" y="1083.36" fill="#fff" rx="31.44" ry="31.44"/><path fill="#0e1e65" d="m805.88,1549.25h-375.06c-15.58,0-27.72-11.82-26.04-25.36l40.38-325.16c1.44-11.62,12.67-20.42,26.04-20.42h294.29c13.37,0,24.59,8.8,26.04,20.42l40.38,325.16c1.68,13.54-10.45,25.36-26.04,25.36Z"/><path fill="#ffdb22" d="m755.37,1549.25h-327.85c-13.62,0-24.23-11.82-22.76-25.36l35.3-325.16c1.26-11.62,11.07-20.42,22.76-20.42h257.25c11.69,0,21.5,8.8,22.76,20.42l35.3,325.16c1.47,13.54-9.14,25.36-22.76,25.36Z"/><path fill="none" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="12" d="m682.56,1291.21v60.22c0,45.62-36.99,82.61-82.61,82.61h0c-45.62,0-82.61-36.99-82.61-82.61v-60.91"/><rect width="86.27" height="313.48" x="932.15" y="1073.96" fill="#3b7eff" rx="37.68" ry="37.68"/><path fill="#121a49" d="m1018.42,1349.76v-34.46c-40-13.2-73.16-4.9-86.27.57v33.89c0,.36.02.72.03,1.08,7.25,19.41,15.09,13.38,15.09,13.38l65.59,5.25c3.53-5.73,5.56-12.48,5.56-19.71Z"/><path fill="#3b7eff" d="m1240.35,1550.08c-23.28-125.25-133.11-220.08-265.11-220.08s-241.82,94.84-265.11,220.08h530.22Z"/><path fill="#fff" d="m710.13,1550.08h530.22c-2.87-15.46-7.07-30.45-12.46-44.85h-505.3c-5.39,14.41-9.58,29.4-12.46,44.85Z"/><path fill="none" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="12" d="m942.06,1371.37s-81.34.43-140.33,114.21"/><circle cx="1022.73" cy="739.63" r="387.99" fill="#38daff"/><path fill="#3b7eff" d="m793.78,426.37c-38.79,28.4-72.11,63.82-98.08,104.4,28.47-11.56,59.6-17.94,92.22-17.94,135.52,0,245.39,109.86,245.39,245.39s-109.86,245.39-245.39,245.39c-18.98,0-37.45-2.16-55.19-6.24,31.83,35.79,70.19,65.66,113.22,87.72,155.66-27.44,273.91-163.34,273.91-326.87,0-181.36-145.45-328.73-326.07-331.86Z"/><ellipse cx="1100.23" cy="418.39" fill="#0e1e65" rx="34.87" ry="87.47" transform="rotate(-78.09 1100.232 418.388)"/><rect width="62.88" height="194.58" x="1098.89" y="225.03" fill="#fff" rx="27.46" ry="27.46" transform="rotate(17.45 1130.105 322.207)"/><path fill="#0e1e65" d="m1063.88,1281.51c-54.26,0-107.84-8.03-159.24-23.87-20.24-6.24-31.59-27.7-25.35-47.94,6.24-20.24,27.7-31.59,47.94-25.35,44.08,13.58,90.06,20.47,136.66,20.47,62.57,0,123.26-12.25,180.36-36.4,55.18-23.34,104.74-56.75,147.31-99.32,42.57-42.57,75.99-92.13,99.32-147.31,24.15-57.1,36.4-117.79,36.4-180.36,0-55.25-9.61-109.27-28.57-160.59-18.32-49.58-44.87-95.4-78.91-136.18-68.47-82.01-163.58-138.22-267.83-158.27-20.8-4-34.41-24.1-30.41-44.9,4-20.8,24.11-34.42,44.9-30.41,121.57,23.38,232.45,88.88,312.22,184.43,39.67,47.52,70.61,100.93,91.98,158.75,22.11,59.85,33.32,122.82,33.32,187.16,0,72.89-14.29,143.63-42.46,210.24-27.2,64.32-66.14,122.07-115.73,171.66-49.59,49.59-107.34,88.52-171.66,115.73-66.61,28.17-137.34,42.46-210.24,42.46Z"/><g><circle cx="846.89" cy="200.93" r="145.47" fill="#ffdb22"/><path d="m843.31,251.06c-2.27,0-4.01-.74-5.23-2.22-1.22-1.48-1.83-3.53-1.83-6.15,0-6.1.74-11.81,2.22-17.13,1.48-5.32,3.83-10.77,7.06-16.35,3.22-5.58,7.63-11.6,13.21-18.05,4.36-5.23,7.85-9.85,10.46-13.86,2.62-4.01,4.49-7.89,5.62-11.64,1.13-3.75,1.7-7.54,1.7-11.38,0-7.32-2.62-13.12-7.85-17.39-5.23-4.27-12.47-6.41-21.71-6.41-8.02,0-15.39,1.18-22.1,3.53-6.72,2.35-13.21,5.89-19.49,10.59-2.62,1.75-5.01,2.62-7.19,2.62s-3.97-.61-5.36-1.83c-1.4-1.22-2.31-2.79-2.75-4.71-.44-1.92-.18-3.92.78-6.02.96-2.09,2.66-4.01,5.1-5.75,6.8-5.4,14.82-9.68,24.06-12.82,9.24-3.14,18.57-4.71,27.99-4.71,9.94,0,18.7,1.7,26.29,5.1,7.59,3.4,13.47,8.2,17.66,14.39,4.19,6.19,6.28,13.47,6.28,21.84,0,5.41-.78,10.59-2.35,15.56-1.57,4.97-4.05,9.98-7.45,15.04-3.4,5.06-8.15,10.81-14.26,17.26-5.41,5.58-9.77,10.77-13.08,15.56-3.31,4.8-5.8,9.42-7.46,13.86-1.66,4.45-2.75,9.11-3.27,13.99-.18,2.09-.87,3.79-2.09,5.1-1.22,1.31-2.88,1.96-4.97,1.96Zm-.26,48.13c-4.36,0-7.89-1.35-10.59-4.05-2.71-2.7-4.05-6.15-4.05-10.33s1.35-7.63,4.05-10.33,6.23-4.05,10.59-4.05,7.8,1.35,10.33,4.05c2.53,2.71,3.79,6.15,3.79,10.33s-1.27,7.63-3.79,10.33c-2.53,2.71-5.97,4.05-10.33,4.05Z"/></g><g><path fill="#91e242" d="m184.76,541.58h358.03s76.04-21.12,73.93-99.28c-2.11-78.15-87.66-83.43-87.66-83.43,0,0-57.03-137.3-177.43-117.23-120.4,20.07-134.09,102.57-134.09,102.57,0,0-113.72,6.67-109.88,98.1,3.85,91.43,77.1,99.28,77.1,99.28Z"/><path fill="none" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="12" d="m248.14,436.71s-41.73-28.74-30.6-92.5"/><path fill="none" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="12" d="m434.28,421.9s24.93-71.31,94.78-63.03"/></g><g><path fill="#00c28f" d="m1505.98,950.25h297.04s63.09-17.52,61.34-82.37c-1.75-64.84-72.73-69.22-72.73-69.22,0,0-47.32-113.91-147.21-97.26-99.89,16.65-111.25,85.09-111.25,85.09,0,0-94.35,5.53-91.16,81.39,3.19,75.86,63.96,82.37,63.96,82.37Z"/><path fill="none" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="12" d="m1558.57,863.24s-34.62-23.85-25.39-76.74"/><path fill="none" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="12" d="m1713,850.96s18.27-50.5,78.63-52.29"/></g><g><path fill="#ffdb22" d="m1334.26,1390.07h243.22c9.97,0,17.28-9.89,14.83-20.08l-18.05-75.07c-2.29-9.54-10.44-16.22-19.78-16.22h-243.22c-9.97,0-17.28,9.89-14.83,20.08l18.05,75.07c2.29,9.54,10.44,16.22,19.78,16.22Z"/><rect width="376.84" height="277.2" x="1192.7" y="1278.13" fill="#0e1e65" rx="20" ry="20"/><rect width="264.47" height="275.22" x="1192.7" y="1280.11" fill="#ffdb22" rx="20" ry="20"/><path fill="#0e1e65" d="m1431.58,1411.76c10.28,0,19.25-9.11,21.77-22.11l4.39-22.61v-66.93c0-11.05-8.95-20-20-20h-225.04c-11.05,0-20,8.95-20,20v111.65h238.88Z"/><path fill="#ffdb22" d="m1412.93,1390.04h-243.22c-9.97,0-17.28-9.89-14.83-20.08l18.05-75.07c2.29-9.54,10.44-16.22,19.78-16.22h243.22c9.97,0,17.28,9.89,14.83,20.08l-18.05,75.07c-2.29,9.54-10.44,16.22-19.78,16.22Z"/></g><g><path fill="#00c28f" d="m992.88,1603.03c96.69,55.53,122.69,156.55,127.54,180.05,34.61-5.47,68.48-13.15,101.46-22.91-54.46-114.66-168.3-153.68-225.43-166.36-5.6-1.24-8.55,6.36-3.57,9.21Z"/><path fill="#91e242" d="m1144,1779c-68.72-115.6-200.83-136.89-261.82-140.36-5.66-.32-7.4,7.57-2.12,9.64,98.46,38.53,143.02,117.17,155.65,143.63,36.81-1.87,72.96-6.23,108.29-12.91Z"/><path fill="#91e242" d="m1115.59,1783.85c39.03-5.92,77.13-14.68,114.09-26.02-11.59-111.12,53.4-236.53,76.28-276.68,2.41-4.22-2.03-9.04-6.44-7.02-165.32,75.84-182.23,267.16-183.93,309.72Z"/><path fill="#00c28f" d="m1209.67,1763.68c34.13-9.51,67.34-21.21,99.48-34.97-.31-74.94,97.41-228.22,125.86-271.24,2.61-3.94-1.22-8.98-5.72-7.51-166.08,54.26-212.82,276.08-219.62,313.73Z"/><path fill="#91e242" d="m1291.27,1734.49s59.93-171.57,108.11-199.78c0,0,71.68,36.43,76.38,75.21,3.55,29.27-2.28,115.45-5.35,155.96-.4,5.29-7.68,6.39-9.63,1.46l-59.05-149.19-18,72.49c-31.06,18.02-61.92,33.15-92.46,43.85Z"/></g><path fill="#fff" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="6" d="m1407.75,1597.83s-7.04,12.13-6.02,20.32"/><g><ellipse cx="949.45" cy="719.31" fill="#fff" stroke="#fff" stroke-miterlimit="10" stroke-width="12" rx="57.29" ry="73.77"/><path fill="none" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="12" d="m1047.27,660.27s-84.76,5.79-120.04-42.12"/><path stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="12" d="m949.45,793.08c8.15,0,15.89-2.21,22.91-6.16,7.34-11.79,11.75-26.76,11.75-43.05,0-.73-.03-1.46-.04-2.19l-29.85-12.16,21.82-22.02c-9.44-19.52-26.25-32.51-45.42-32.51-12.91,0-24.75,5.89-33.99,15.7-2.88,8.8-4.47,18.47-4.47,28.62,0,40.74,25.65,73.77,57.29,73.77Z"/><ellipse cx="725.85" cy="719.31" fill="#fff" stroke="#fff" stroke-miterlimit="10" stroke-width="12" rx="57.29" ry="73.77"/><path fill="none" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="12" d="m644.74,655.64s84.76,5.79,120.04-42.12"/><path stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="12" d="m725.85,793.08c8.15,0,15.89-2.21,22.91-6.16,7.34-11.79,11.75-26.76,11.75-43.05,0-.73-.03-1.46-.04-2.19l-29.85-12.16,21.82-22.02c-9.44-19.52-26.25-32.51-45.42-32.51-12.91,0-24.75,5.89-33.99,15.7-2.88,8.8-4.47,18.47-4.47,28.62,0,40.74,25.65,73.77,57.29,73.77Z"/><path fill="none" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="12" d="m848.71,747.55s-60.54-6.39-58.84,29.44c1.7,35.83,64.64,22.73,64.64,22.73"/><path fill="none" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="12" d="m975.8,881.86s-79.11-94.69-198.98-13.19"/><path fill="none" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="12" d="m759.82,845.95s28.56,22.85,3.26,46.51"/><path fill="none" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="12" d="m995.33,864.07s-30.41,11.84-16.69,47.38"/><path fill="none" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="12" d="m919.54,897.46s-37.39-30.94-88.95-2.58"/><path fill="#ffdb22" d="m1133.51,931.35s76.86,98.8,58.26,150.4l3.42,4,58.75,64.27s93.34-205.64-80.41-338.72l-40.01,120.04Z"/><path fill="#121a49" d="m1194,1072.72c-.47,3.14-1.2,6.16-2.23,9.03l3.42,4,58.75,64.27s7-15.43,13.6-40.58c-35.86-32.62-63.15-36.59-73.54-36.73Z"/><path fill="#ffdb22" d="m412.6,755.77s9.77,69.12,121.65,105.86l69.26,118.82s-207.04,60.03-287.2-250.58l96.29,25.9Z"/><path fill="#121a49" d="m412.6,755.77l-96.29-25.9c1.57,6.1,3.2,12.04,4.87,17.86,14.11,11.49,45.08,29.12,94.36,18.52-2.38-6.52-2.95-10.49-2.95-10.49Z"/><g><rect width="121.7" height="429.13" x="1017.13" y="957.21" fill="#0e1e65" rx="53.16" ry="53.16" transform="rotate(-38.34 1077.965 1171.77)"/><rect width="166.94" height="63.14" x="947.44" y="1082.53" fill="#ffdb22" rx="15.74" ry="15.74" transform="rotate(-38.03 1030.975 1114.132)"/><path fill="#0e1e65" d="m847.92,426.29c-183.32,0-331.93,148.61-331.93,331.93s148.61,331.93,331.93,331.93,331.93-148.61,331.93-331.93-148.61-331.93-331.93-331.93Zm0,577.32c-135.52,0-245.39-109.86-245.39-245.39s109.86-245.39,245.39-245.39,245.39,109.86,245.39,245.39-109.86,245.39-245.39,245.39Z"/><path fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="12" d="m790.73,473.74s161.38-41.18,257.09,75.68"/><ellipse cx="1086.42" cy="594.02" fill="#fff" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="12" rx="14.59" ry="36.92" transform="rotate(-26.9 1086.278 593.982)"/></g><g><path fill="#fa7dff" d="m1185.13,1076.01s69.55,10.6,80.49,68.1c0,0,3.9,15.92-13.8,24.44-17.7,8.52-74.89-59.12-74.89-59.12,0,0-11.23-28.25,8.2-33.42Z"/><path fill="#fa7dff" d="m1106.86,1124.27l-43.56-38.57c-10.52-9.31-10.55-25.72-.06-35.07h0c8.23-7.34,20.47-7.95,29.39-1.47l65,47.23s16.16-6.98,29.28,3c13.12,9.98,82.6,60.64,71.21,121.53-11.39,60.89-40.76,27.85-56.11,49.46-8.13,11.45-16.66,33.91-22.89,52.52-5.25,15.68-23.13,23.11-37.94,15.75h0c-12.59-6.25-18.36-21.06-13.3-34.18l16.33-42.4-35.52,27.37c-13.02,10.03-31.73,7.46-41.55-5.72h0c-8.81-11.81-7.52-28.32,3.01-38.62l15.74-17.96-26.35,17.12c-12.28,5.34-26.61.74-33.51-10.74h0c-7.39-12.31-5.08-28.11,5.52-37.79l75.29-71.46Z"/><path fill="#fff" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="12" d="m1141.9,1175.8s-42.13,37.05-60.18,54.77"/><line x1="1176.46" x2="1144.22" y1="1216.61" y2="1262.07" fill="#fff" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="12"/></g><g><path fill="#fa7dff" d="m412.6,755.77s-85.35,14.04-110.19-46.45c0,0-8.93-8.65,20.54-28.78,0,0-33.79-58.96-5.03-112.16,28.76-53.21,59.68-51.77,59.68-51.77,0,0-2.72-13.16,11.34-14.52,0,0-.45-17.69,18.15-22.23,13.29-3.24,56.89-15.51,80.73-22.25,9.95-2.82,20.64-.48,28.51,6.23l66.83,56.94c12.08,10.29,13.71,28.35,3.68,40.64h0c-8.35,10.23-22.53,13.58-34.57,8.15l-57.16-25.74,3.63,53.53,48.09,4.08s15.88,15.88,1.81,53.53c-14.06,37.66-69.41,42.65-69.41,42.65,0,0-.45,17.24-51.27,32.67,0,0-.76,23.29-15.35,25.48Z"/><path fill="#fff" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="12" d="m485.94,514.26s3.34,23.31,13.46,31.44"/><path fill="none" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="12" d="m405.2,721.63s-55.52-5.15-82.25-41.09"/><path fill="#fa7dff" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="12" d="m356.7,580.69l17.38,6.87c4.6,1.82,8.32,5.33,10.4,9.81l16.57,35.64c5.71,12.28,20.43,17.42,32.54,11.36h0c10.34-5.17,15.53-16.96,12.37-28.08l-12.7-44.6c-2.67-9.36-8.21-17.65-15.86-23.68l-39.82-31.42"/><path fill="#fa7dff" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="12" d="m521.93,652.67s-51.37,18.29-78.95-15.67c-11.72-15.74-7.32-38.99,15.94-37.09"/><path fill="#fa7dff" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="12" d="m390.24,502.21s91.69-1.81,104.85,41.62l2.93,42.94c.93,13.59-9.39,25.33-22.98,26.16h0c-10.75.65-20.68-5.78-24.46-15.87l-7.77-20.72c-2.87-12.75-2.24-10.85-2.24-10.85"/></g></g><g><path fill="#00c28f" d="m466.36,1593.94c29.25,25.71,60.38,49.31,93.17,70.59-1.56-36.74.25-152.87,76.41-240.49,3.48-4-.85-9.96-5.72-7.88-40.58,17.33-130.74,67.18-163.86,177.78Z"/><path fill="#91e242" d="m408.02,1537.2c36.52,39.31,76.94,74.93,120.64,106.27,5.45-66.66,2.04-202.22-91.38-322.57-3.23-4.16-9.85-1.19-8.88,3.99,8.06,42.71,20.93,143.82-20.39,212.31Z"/><path fill="#00c28f" d="m516.5,1582.3c4.08,10.99,11.14,32.91,16,63.9,30.96,21.93,63.54,41.71,97.54,59.13-2.72-40.27-19.99-113.5-107.91-129.68-3.83-.71-6.99,2.99-5.63,6.65Z"/><path fill="#91e242" d="m578.41,1676.41c28.87,17.61,58.96,33.43,90.08,47.34,14.23-106.47,97.24-219.95,123.81-253.98,2.85-3.66-.39-8.9-4.94-7.98-131.44,26.51-191.8,166.3-208.96,214.63Z"/><path fill="#00c28f" d="m426.8,1556.73c-4.03-77.35-31.24-257.18-200.49-347.61l-63.09,7.95c-15.47,2.09-29.19,11.01-37.37,24.31l-80.05,130.18c-2.81,4.57,2.56,9.75,7.03,6.78l157.26-104.57c9.28-6.17,21.62-5.2,29.7,2.47,3.99,3.79,8.13,8.48,11.59,14.01,40.29,100.51,100.35,190.95,175.41,266.49Z"/></g></svg>
                    <div style="display: flex;    justify-content: center;    width: 100%;">
                        <b> <h1>Result Not Found !! </h1></b>
                    </div>
                    <?php } ?>

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
<style>
    .list-container ul {
        list-style: none;
        padding: 0;
    }

    .list-container ul li {
        border: 1px solid #ccc;
        padding: 15px;
        margin-bottom: 10px;
        border-radius: 5px;
    }

    .json-list {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .json-list pre {
        background-color: #f8f9fa;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        width: 48%;
        margin-right: 2%;
        box-sizing: border-box;
        font-family: 'Courier New', Courier, monospace;
    }

    .json-list pre:nth-child(2n) {
        margin-right: 0;
    }

    @media(min-width: 768px) {
        .list-container ul {
            display: flex;
            flex-wrap: wrap;
        }

        .list-container ul li {
            width: 48%;
            margin-right: 2%;
        }

        .list-container ul li:nth-child(2n) {
            margin-right: 0; 
        }
    }
    @media(max-width: 768px) {
        .json-list pre {
            width: 100%; /* Single column on smaller screens */
            margin-right: 0;
        }
    }

</style>

<script>
    const radioButtons = document.querySelectorAll('input[name="changeView"]');
    radioButtons.forEach(radio => {
        radio.addEventListener('change', function() {
            document.getElementById('viewType').submit();
        });
    });
    function closeForm() {
        document.getElementById('searchByip').reset();
        const inputField = document.querySelector('input[name="ipaddress"]');
        inputField.value = '';
        toggleCloseButton();
    }
    function toggleCloseButton() {
        const inputField = document.querySelector('input[name="ipaddress"]');
        const closeButton = document.getElementById('closeBtn');
        if (inputField.value.trim() !== '') {
            closeButton.style.display = 'block';
        } else {
            closeButton.style.display = 'none';
            window.location.href = "<?= url('access?session_clear=search') ?>";
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
        const inputField = document.querySelector('input[name="ipaddress"]');
        const closeButton = document.getElementById('closeBtn');
        if (inputField.value.trim() !== '') {
            closeButton.style.display = 'block';
        } else {
            closeButton.style.display = 'none';
        }
    });
    window.addEventListener('load', function() {
        const url = new URL(window.location.href);
        if (url.searchParams.has('session_clear')) {
            url.searchParams.delete('session_clear');
            window.history.replaceState({}, document.title, url.toString());
        }
    });
</script>
<?php
    view('includes/footer');
?>