<link href="/css/app/user/edu-archive/archive.css" rel="stylesheet">

<style>
  .submissions-page {
    max-width: 1080px;
    margin: 0 auto;
    padding: 28px 20px 36px;
  }

  .submissions-head {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 14px;
    margin-bottom: 16px;
  }

  .submissions-title {
    margin: 0;
    font-size: clamp(2rem, 1.6rem + 1vw, 2.7rem);
    line-height: 1.1;
    color: #0f172a;
  }

  .submissions-subtitle {
    margin: 7px 0 0;
    color: #64748b;
    font-size: 0.95rem;
  }

  .submissions-back {
    text-decoration: none;
    color: #1d63d8;
    font-weight: 600;
    font-size: 0.95rem;
    margin-top: 8px;
  }

  .submissions-alert {
    border-radius: 12px;
    padding: 12px 14px;
    font-size: 0.92rem;
    margin-bottom: 14px;
    border: 1px solid transparent;
  }

  .submissions-alert.success {
    background: #dcfce7;
    border-color: #bbf7d0;
    color: #166534;
  }

  .submissions-alert.error {
    background: #fee2e2;
    border-color: #fecaca;
    color: #991b1b;
  }

  .submissions-list {
    display: grid;
    gap: 12px;
  }

  .submission-card {
    display: grid;
    grid-template-columns: 84px minmax(0, 1fr) auto;
    gap: 16px;
    align-items: center;
    background: #fff;
    border: 1px solid #dbe3ef;
    border-radius: 14px;
    padding: 14px;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
  }

  .submission-card:hover {
    border-color: #bfd2f4;
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
  }

  .submission-type-pill {
    width: 84px;
    height: 64px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.82rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    border: 1px solid #dbe3ef;
  }

  .submission-type-pill.video {
    background: #0f172a;
    color: #fff;
    border-color: #0f172a;
  }

  .submission-type-pill.note {
    background: #f8fafc;
    color: #334155;
  }

  .submission-main h3 {
    margin: 0 0 4px;
    font-size: 1.75rem;
    line-height: 1.15;
    color: #0f172a;
    overflow-wrap: anywhere;
  }

  .submission-meta {
    margin: 0;
    color: #64748b;
    font-size: 0.95rem;
  }

  .submission-feedback {
    margin-top: 8px;
    font-size: 0.86rem;
    color: #b91c1c;
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 8px;
    padding: 8px 10px;
  }

  .submission-side {
    display: flex;
    align-items: center;
    gap: 14px;
    justify-content: flex-end;
    min-width: 220px;
  }

  .submission-status {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 5px 12px;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.03em;
  }

  .submission-status.pending {
    background: #fef3c7;
    color: #b45309;
  }

  .submission-status.approved {
    background: #dcfce7;
    color: #166534;
  }

  .submission-status.rejected {
    background: #fee2e2;
    color: #991b1b;
  }

  .submission-actions {
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }

  .submission-action-link {
    text-decoration: none;
    color: #1d63d8;
    font-size: 0.92rem;
    font-weight: 600;
  }

  .submission-delete-btn {
    background: none;
    border: none;
    color: #dc2626;
    cursor: pointer;
    font-size: 0.92rem;
    font-weight: 600;
    padding: 0;
  }

  .submission-request-btn {
    background: none;
    border: none;
    color: #b45309;
    cursor: pointer;
    font-size: 0.92rem;
    font-weight: 700;
    padding: 0;
  }

  .submission-requested-note {
    color: #b45309;
    background: #fef3c7;
    border-radius: 999px;
    padding: 5px 10px;
    font-size: 0.78rem;
    font-weight: 700;
    white-space: nowrap;
  }

  .submissions-empty {
    background: #fff;
    border: 1px solid #dbe3ef;
    border-radius: 14px;
    padding: 28px;
    text-align: center;
    color: #64748b;
  }

  .submissions-empty a {
    display: inline-block;
    margin-top: 12px;
    text-decoration: none;
  }

  @media (max-width: 900px) {
    .submission-card {
      grid-template-columns: 1fr;
      align-items: start;
    }

    .submission-side {
      min-width: 0;
      justify-content: flex-start;
    }

    .submission-main h3 {
      font-size: 1.25rem;
    }
  }
</style>

<?php
  $yearLabels = [
    '1' => 'Year 1',
    '2' => 'Year 2',
    '3' => 'Year 3',
    '4' => 'Year 4',
    '5' => 'Post Graduate'
  ];
?>

<main class="submissions-page">
  <header class="submissions-head">
    <div>
      <h1 class="submissions-title">My Submissions</h1>
      <p class="submissions-subtitle">Track approval status and manage your pending uploads.</p>
    </div>
    <a href="/dashboard/edu-archive" class="submissions-back">Back to Archive</a>
  </header>

  <?php if (isset($_GET['success'])): ?>
    <div class="submissions-alert success">Action completed successfully.</div>
  <?php endif; ?>
  <?php if (isset($_GET['error'])): ?>
    <div class="submissions-alert error">Unable to complete that action for this submission.</div>
  <?php endif; ?>

  <?php if (empty($resources)): ?>
    <section class="submissions-empty">
      You have not uploaded any resources yet.
      <br>
      <a href="/dashboard/edu-archive/upload" class="archive-btn archive-btn-primary">Upload Your First Resource</a>
    </section>
  <?php else: ?>
    <section class="submissions-list">
      <?php foreach ($resources as $res): ?>
        <article class="submission-card">
          <div class="submission-type-pill <?= $res['type'] === 'video' ? 'video' : 'note' ?>">
            <?= $res['type'] === 'video' ? 'Video' : 'Note' ?>
          </div>

          <div class="submission-main">
            <h3><?= htmlspecialchars($res['title']) ?></h3>
            <p class="submission-meta">
              <?= htmlspecialchars($res['subject']) ?> •
              <?= htmlspecialchars($yearLabels[(string)($res['year_level'] ?? '')] ?? ('Year ' . $res['year_level'])) ?> •
              <?= date('M j, Y', strtotime($res['created_at'])) ?>
            </p>
            <?php if ($res['status'] === 'rejected' && !empty($res['admin_feedback'])): ?>
              <p class="submission-feedback"><strong>Admin Feedback:</strong> <?= htmlspecialchars($res['admin_feedback']) ?></p>
            <?php endif; ?>
          </div>

          <div class="submission-side">
            <span class="submission-status <?= htmlspecialchars($res['status']) ?>">
              <?= htmlspecialchars($res['status']) ?>
            </span>

            <div class="submission-actions">
              <?php if ($res['status'] === 'pending'): ?>
                <a href="/dashboard/edu-archive/edit?id=<?= (int)$res['id'] ?>" class="submission-action-link">Edit</a>
                <form action="/dashboard/edu-archive/delete" method="POST" onsubmit="return confirm('Are you sure you want to delete this submission?');">
                  <input type="hidden" name="id" value="<?= (int)$res['id'] ?>">
                  <button type="submit" class="submission-delete-btn">Delete</button>
                </form>
              <?php elseif ($res['status'] === 'approved'): ?>
                <a href="<?= $res['type'] === 'video' ? htmlspecialchars($res['video_link']) : htmlspecialchars($res['file_path']) ?>" target="_blank" class="submission-action-link">View</a>
                <?php if ((int)($res['removal_requested'] ?? 0) === 1): ?>
                  <span class="submission-requested-note">Removal requested</span>
                <?php else: ?>
                  <form
                    action="/dashboard/edu-archive/request-removal"
                    method="POST"
                    onsubmit="const reason = prompt('Why should this approved resource be removed?'); if (!reason || !reason.trim()) return false; this.removal_reason.value = reason.trim(); return true;"
                  >
                    <input type="hidden" name="id" value="<?= (int)$res['id'] ?>">
                    <input type="hidden" name="removal_reason" value="">
                    <button type="submit" class="submission-request-btn">Request Removal</button>
                  </form>
                <?php endif; ?>
              <?php else: ?>
                <span style="font-size: 0.9rem; color: #94a3b8;">No actions</span>
              <?php endif; ?>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </section>
  <?php endif; ?>
</main>
