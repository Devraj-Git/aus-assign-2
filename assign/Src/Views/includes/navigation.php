<ul>
    <?php if(check_user_access('admin')){ ?>
    <li>
        <a href="<?= url('permission') ?>">
            <i class="nav-link-icon ti-layout-accordion-list"></i>
            <span class="nav-link-label">Permission</span>
        </a>
    </li>
    <?php } if(check_user_access('admin','moderator')) {?>
    <li>
        <a href="<?= url('access') ?>">
            <i class="nav-link-icon ti-layout-cta-left"></i>
            <span class="nav-link-label">Access-Log</span>
        </a>
    </li>
    <?php } ?>
    <li>
        <a href="<?= url('discord/connect') ?>">
            <i class="nav-link-icon ti-link"></i>
            <span class="nav-link-label">Discord-Link</span>
            <span class="badge badge-warning">New</span>
        </a>
    </li>
</ul>