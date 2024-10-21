<?php

namespace App\System\Core;

class Views {
    
    public function __construct(string $view, ?array $data = null) {
        if(!empty($data)) {
            extract($data);
        }
        
        include  BASEPATH . "/src/Views/$view.php";
    }

}