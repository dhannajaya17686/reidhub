<link rel="stylesheet" href="/css/app/globals.css">
<link rel="stylesheet" href="/css/app/user/community/community.css">
<link rel="stylesheet" href="/css/app/user/community/blog-view.css">

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="breadcrumb__list">
    <li class="breadcrumb__item">
      <a href="/dashboard/community/admin?tab=blog-posts" class="breadcrumb__link">Reported Blogs</a>
    </li>
    <li class="breadcrumb__item breadcrumb__item--current" aria-current="page">
      <?= htmlspecialchars($data['blog']['title'] ?? 'Blog') ?>
    </li>
  </ol>
</nav>

<main class="blog-view-main" role="main" aria-label="Blog Post">
  <div class="blog-container">
    <header class="blog-header">
      <h1 class="blog-title"><?= htmlspecialchars($data['blog']['title'] ?? 'Untitled Blog') ?></h1>

      <?php if (!empty($data['hasReports'])): ?>
      <div class="reported-tag" style="background: #FEE2E2; border: 1px solid #FCA5A5; color: #DC2626; padding: 8px 12px; border-radius: 6px; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 12px; font-size: 0.875rem; font-weight: 500;">
        <span>This post has been reported</span>
      </div>
      <?php endif; ?>

      <div class="blog-image">
        <img src="<?= htmlspecialchars($data['blog']['image_path'] ?? '/assets/placeholders/product.jpeg') ?>" alt="<?= htmlspecialchars($data['blog']['title'] ?? 'Blog image') ?>">
      </div>

      <div class="blog-meta">
        <span class="blog-author">By <?= htmlspecialchars(($data['blog']['first_name'] ?? '') . ' ' . ($data['blog']['last_name'] ?? '')) ?></span>
        <span class="blog-separator">|</span>
        <span class="blog-published"><?= !empty($data['blog']['created_at']) ? date('F j, Y', strtotime($data['blog']['created_at'])) : '-' ?></span>
        <span class="blog-separator">|</span>
        <span class="blog-views"><?= number_format((int)($data['blog']['views'] ?? 0)) ?> views</span>
      </div>
    </header>

    <article class="blog-content">
      <?= nl2br(htmlspecialchars($data['blog']['content'] ?? '')) ?>
    </article>

    <div class="blog-actions" id="blog-actions">
      <a href="/dashboard/community/admin?tab=blog-posts" class="btn btn--secondary">Back to Reported Blogs</a>
    </div>
  </div>
</main>
