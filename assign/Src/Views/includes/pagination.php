<?php if($pages > 1): ?>
  <nav aria-label="..." style="display: flex; justify-content: center;">
    <ul class="pagination">
      <li class="page-item <?php echo $pageNo == 1 ? 'disabled' : ''; ?>">
        <a class="page-link" <?php echo $pageNo > 1 ? 'href=' .$url.'?page='.($pageNo - 1) : ''; ?>>Previous</a>
      </li>

      <?php
        $start = max(1, $pageNo - 4);
        $end = min($pages, $pageNo + 5);
        if ($start > 1) {
            echo '<li class="page-item"><a class="page-link" href="' . "{$url}?page=1" . '">1</a></li>';
            if ($start > 2) {
                echo '<li class="page-item"><span class="page-link">...</span></li>';
            }
        }
        for ($i = $start; $i <= $end; $i++) {
            echo '<li class="page-item ' . ($pageNo == $i ? 'active' : '') . '"><a class="page-link" href="' . "{$url}?page={$i}" . '">' . $i . '</a></li>';
        }
        if ($end < $pages) {
            if ($end < $pages - 1) {
                echo '<li class="page-item"><span class="page-link">...</span></li>';
            }
            echo '<li class="page-item"><a class="page-link" href="' . "{$url}?page={$pages}" . '">' . $pages . '</a></li>';
        }
      ?>
      
      <li class="page-item <?php echo $pageNo == $pages ? 'disabled' : ''; ?>">
        <a class="page-link" <?php echo $pageNo < $pages ? 'href=' .$url.'?page='.($pageNo + 1) : ''; ?>>Next</a>
      </li>
    </ul>
  </nav>
<?php endif; ?>