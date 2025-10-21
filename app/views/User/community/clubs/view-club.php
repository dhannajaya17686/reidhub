<link rel="stylesheet" href="/css/globals.css">
<link rel="stylesheet" href="/css/app/user/community/blog-view.css">

<!-- Breadcrumb Navigation -->
<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="breadcrumb__list">
    <li class="breadcrumb__item">
      <a href="/dashboard" class="breadcrumb__link">Dashboard</a>
    </li>
    <li class="breadcrumb__item">
      <a href="/dashboard/community" class="breadcrumb__link">Community</a>
    </li>
    <li class="breadcrumb__item">
      <a href="/dashboard/community/clubs" class="breadcrumb__link">Clubs</a>
    </li>
    <li class="breadcrumb__item breadcrumb__item--current" aria-current="page">
      <?= htmlspecialchars($data['club']['name'] ?? 'Club') ?>
    </li>
  </ol>
</nav>

<main class="blog-view-main" role="main" aria-label="Club Details">
  
  <div class="blog-container">
    <header class="blog-header">
      <h1 class="blog-title"><?= htmlspecialchars($data['club']['name'] ?? 'Technology Club') ?></h1>
      
      <div class="blog-image">
        <img src="<?= htmlspecialchars($data['club']['image_path'] ?? 'https://via.placeholder.com/900x400/4A90E2/ffffff?text=Club') ?>" 
             alt="<?= htmlspecialchars($data['club']['name'] ?? 'Club') ?>">
      </div>

      <div class="blog-meta">
        <span class="blog-author">Category: <?= htmlspecialchars($data['club']['category'] ?? 'General') ?></span>
        <span class="blog-separator">•</span>
        <span class="blog-published">50 members</span>
        <span class="blog-separator">•</span>
        <span class="blog-views">Meets Weekly</span>
      </div>
    </header>

    <article class="blog-content">
      <p><?= nl2br(htmlspecialchars($data['club']['description'] ?? 'Join our amazing club!')) ?></p>
    </article>

    <div class="blog-interactions">
      <button class="btn btn--primary">Join Club</button>
      <button class="btn btn--secondary">Contact</button>
    </div>

    <?php if (isset($data['isOwner']) && $data['isOwner']): ?>
    <div class="blog-actions">
      <button class="btn btn--primary" onclick="window.location.href='/dashboard/community/clubs/edit?id=<?= $data['club']['id'] ?? 1 ?>'">Edit</button>
      <button class="btn btn--danger" id="delete-club-btn" data-club-id="<?= $data['club']['id'] ?? 1 ?>">Delete</button>
    </div>
    <?php else: ?>
    <div class="blog-report">
      <button class="btn btn--outline" id="report-club-btn">Report</button>
    </div>
    <?php endif; ?>
  </div>

</main>

<script type="module" src="/js/app/community/club-view.js"></script>
