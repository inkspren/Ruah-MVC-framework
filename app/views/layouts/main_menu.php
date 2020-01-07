<?php
use Core\Router;
use Core\H;
use App\Models\Users;

    $menu = Router::getMenu('menu_acl');
    $currentPage = H::currentPage();
?>
<nav class="navbar navbar-expand navbar-dark bg-dark">
<a class="navbar-brand" href="<?=PROOT?>home"><?=MENU_BRAND?></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main_menu" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="main_menu">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav mr-auto">
                <?php foreach($menu as $key => $val):
                    $active = ''; ?>
                    <?php if(is_array($val)): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"><?=$key?></a>
                        <div class="dropdown-menu">
                            <?php foreach($val as $k => $v): 
                                $active = ($v == $currentPage)? 'active':'';?>
                                <?php if($k == 'seperator'): ?>
                                    <div class="dropdown-divider"></div>
                                <?php else: ?>
                                    <a class="dropdown-item <?=$active?>" href="<?=$v?>"><?=$k?></a>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </li>
                    <?php else: 
                        $active = ($val == $currentPage)? 'active':'';?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?=$val?>"><?=$key?><span class="sr-only">(current)</span></a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>

            </ul>
            <ul class="navbar-nav ml-auto">
                <?php if(Users::currentUser()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?=$val?>">Hello <?=Users::currentUser()->fname?><span class="sr-only">(current)</span></a>
                    </li>
                <?php endif; ?>
                    
            </ul>
</nav>