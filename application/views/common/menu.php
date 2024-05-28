<div class="nav d-flex flex-column align-items-start bg-light"  style="height: 100vh;width="70px"">
    <nav class="nav flex-column">
        <a class="nav-link <?php echo ($this->router->fetch_class() == 'Main' && $this->router->fetch_method() == 'index') ? 'active' : null;?>" href="<?php echo base_url('main');?>">
            <i class="bi bi-house-door-fill"></i>
        </a>
        <a class="nav-link <?php echo ($this->router->fetch_class() == 'Directmsg' && $this->router->fetch_method() == 'index') ? 'active' : null;?>" href="<?php echo base_url('direct_msg'); ?>">
            <i class="bi bi-chat-fill"></i>
        </a>
        <a class="nav-link <?php echo ($this->router->fetch_class() == 'Search' && $this->router->fetch_method() == 'index') ? 'active' : null;?>" href="<?php echo base_url('search');?>">
            <i class="bi bi-search"></i>
        </a>
        <a class="nav-link <?php echo ($this->router->fetch_class() == 'Notification' && $this->router->fetch_method() == 'index') ? 'active' : null;?>" href="<?php echo base_url('notification');?>">
            <i class="bi bi-bell-fill notification-icon"></i>
            <span class="notification-badge"></span> <!-- Bolinha adicionada manualmente -->
        </a>
        <a class="nav-link <?php echo ($this->router->fetch_class() == 'Family' && $this->router->fetch_method() == 'index') ? 'active' : null;?>" href="<?php echo base_url('family_menu');?>">
            <i class="bi bi-people-fill"></i>
        </a>
        <a class="nav-link <?php echo ($this->router->fetch_class() == 'Profile' && $this->router->fetch_method() == 'index') ? 'active' : null;?>" href="<?php echo base_url('profile'); ?>">
            <?php
                if(!empty($path)): ?>
                    <img class="profile-pict" src="<?php echo $path;?>" width="32" height="32">
                <?php else: ?>
                    <i class="bi bi-person-circle"></i>
                <?php endif; 
            ?>
        </a>
    </nav>
</div>

