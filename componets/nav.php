<?php
if (isset($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = 'home';
}
$is_logged = (isset($_SESSION['login_code']) && $_SESSION['login_code'] !== '');
if ($is_logged) :
    $activePageLi = 'class="nav-item nav-active rounded"';
    $activePageLink = 'class="nav-link text-white"';
endif;
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark border-bottom border-secondary sticky-top">
    <div class="container-fluid">
        <a href="index.php" class="navbar-brand d-flex align-items-center">
            <?php if (file_exists('./files/photos/logo.png')): ?>
                <img src="./files/photos/logo.png" height="32" class="me-2">
            <?php endif; ?>
            <span class="fw-bold">Księgarnia ZSE</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo $page === 'library' ? 'active' : ''; ?>" href="?page=library">
                        <i class="bi bi-book-half me-1"></i> Moja biblioteka
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $page === 'orders' ? 'active' : ''; ?>" href="?page=orders">
                        <i class="bi bi-bag-check me-1"></i> Historia zamówień
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $page === 'favorites' ? 'active' : ''; ?>" href="?page=favorites">
                        <i class="bi bi-heart me-1"></i> Ulubione
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $page === 'recommendations' ? 'active' : ''; ?>" href="?page=recommendations">
                        <i class="bi bi-star me-1"></i> Rekomendacje
                    </a>
                </li>
                <?php if (isset($_SESSION['level']) && $_SESSION['level'] >= 3): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page === 'bestsellers' ? 'active' : ''; ?>" href="?page=bestsellers">
                            <i class="bi bi-award me-1"></i> Bestsellery
                        </a>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $page === 'help' && (!isset($_GET['task']) || $_GET['task'] != 'contact') ? 'active' : ''; ?>" href="?page=help">
                        <i class="bi bi-question-circle me-1"></i> FAQ
                    </a>
                </li>
                <?php if (isset($_SESSION['level']) && $_SESSION['level'] >= 4): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page === 'admin' ? 'active' : ''; ?>" href="?page=admin">
                            <i class="bi bi-shield-lock me-1"></i> Panel admin
                        </a>
                    </li>
                <?php endif; ?>
            </ul>

            <ul class="navbar-nav ms-auto">
                <?php if ($is_logged): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="?page=profile" title="Mój profil">
                            <i class="bi bi-person-circle me-1"></i>
                            <span class="d-lg-inline d-none"><?php echo isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : 'Profil'; ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-sm btn-danger ms-2 text-white" href="?page=logout" title="Wyloguj się">
                            <i class="bi bi-box-arrow-right"></i>
                            <span class="d-lg-inline d-none ms-1">Wyloguj</span>
                        </a>
                    </li>
                <?php elseif ($page != 'login'): ?>
                    <li class="nav-item">
                        <a class="nav-link btn btn-sm btn-primary ms-2 text-white" href="?page=login" title="Zaloguj się">
                            <i class="bi bi-box-arrow-in-right"></i>
                            <span class="d-lg-inline d-none ms-1">Zaloguj</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>