<?php if(isset($_SESSION['user_id'])): ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container">
    <a class="navbar-brand" href="index.php">Network</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Şahıslar</a></li>
        <li class="nav-item"><a class="nav-link" href="companies.php">Şirketler</a></li>
        <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <li class="nav-item"><a class="nav-link" href="add_user.php">Kullanıcı Ekle</a></li>
        <?php endif; ?>
      </ul>
      <span class="navbar-text me-3 text-light">
        <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['username']); ?> 
        <span class="badge bg-secondary"><?php echo $_SESSION['department']; ?></span>
      </span>
      <a href="logout.php" class="btn btn-outline-danger btn-sm">Çıkış</a>
    </div>
  </div>
</nav>
<?php endif; ?>