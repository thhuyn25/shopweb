<?php
$adminName = isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Admin';
?>

<div id="topbar">
    <button class="btn btn-gradient d-md-none me-3" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>
    <form class="search-form d-flex align-items-center">
        <input type="text" class="form-control" placeholder="Tìm kiếm...">
        <button type="submit" class="btn ms-2"><i class="fas fa-search"></i></button>
    </form>
</div>