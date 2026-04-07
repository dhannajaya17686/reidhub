<link href="/css/app/user/edu-archive/archive.css" rel="stylesheet">

<style>
  .archive-edit-wrap { max-width: 820px; margin: 20px auto; }
  .archive-edit-card { background: #fff; border: 1px solid #dbe3ef; border-radius: 14px; padding: 22px; }
  .archive-edit-grid { display: grid; gap: 14px; }
  .archive-edit-label { display: block; font-weight: 600; margin-bottom: 6px; color: #1f2937; }
  .archive-edit-input, .archive-edit-select, .archive-edit-textarea {
    width: 100%; border: 1px solid #dbe3ef; border-radius: 10px; padding: 10px 12px; background: #f8fafc;
    font-size: 0.95rem; color: #334155;
  }
  .archive-edit-textarea { min-height: 120px; resize: vertical; }
  .archive-edit-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
  .archive-edit-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 12px; }
  .archive-edit-hidden { display: none; }
  @media (max-width: 720px) { .archive-edit-row { grid-template-columns: 1fr; } }
</style>

<main class="archive-edit-wrap">
  <div class="archive-edit-card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
      <h1 style="margin:0; font-size:1.5rem;">Edit Submission</h1>
      <a href="/dashboard/edu-archive/my-submissions" class="archive-btn archive-btn-secondary">Back</a>
    </div>

    <?php if (isset($_GET['error'])): ?>
      <div style="background:#FEE2E2; color:#991B1B; padding:12px; border-radius:8px; margin-bottom:14px;">
        Please check your inputs and try again.
      </div>
    <?php endif; ?>

    <form action="/dashboard/edu-archive/update" method="POST" enctype="multipart/form-data" class="archive-edit-grid">
      <input type="hidden" name="id" value="<?= (int)$resource['id'] ?>">

      <div>
        <label class="archive-edit-label" for="edit-title">Title</label>
        <input id="edit-title" name="title" class="archive-edit-input" required value="<?= htmlspecialchars($resource['title'] ?? '') ?>">
      </div>

      <div>
        <label class="archive-edit-label" for="edit-description">Description</label>
        <textarea id="edit-description" name="description" class="archive-edit-textarea"><?= htmlspecialchars($resource['description'] ?? '') ?></textarea>
      </div>

      <div class="archive-edit-row">
        <div>
          <label class="archive-edit-label" for="edit-subject">Subject / Module</label>
          <select id="edit-subject" name="subject" class="archive-edit-select" required>
            <option value="">All Subjects</option>
            <option value="CS" <?= ($resource['subject'] ?? '') === 'CS' ? 'selected' : '' ?>>Computer Science</option>
            <option value="IS" <?= ($resource['subject'] ?? '') === 'IS' ? 'selected' : '' ?>>Information Systems</option>
            <option value="SE" <?= ($resource['subject'] ?? '') === 'SE' ? 'selected' : '' ?>>Software Engineering</option>
          </select>
        </div>
        <div>
          <label class="archive-edit-label" for="edit-year">Year / Level</label>
          <select id="edit-year" name="year_level" class="archive-edit-select" required>
            <option value="1" <?= (string)($resource['year_level'] ?? '') === '1' ? 'selected' : '' ?>>Year 1</option>
            <option value="2" <?= (string)($resource['year_level'] ?? '') === '2' ? 'selected' : '' ?>>Year 2</option>
            <option value="3" <?= (string)($resource['year_level'] ?? '') === '3' ? 'selected' : '' ?>>Year 3</option>
            <option value="4" <?= (string)($resource['year_level'] ?? '') === '4' ? 'selected' : '' ?>>Year 4</option>
            <option value="5" <?= (string)($resource['year_level'] ?? '') === '5' ? 'selected' : '' ?>>Post Graduate</option>
          </select>
        </div>
      </div>

      <div class="archive-edit-row">
        <div>
          <label class="archive-edit-label" for="edit-type">Content Type</label>
          <select id="edit-type" name="type" class="archive-edit-select" onchange="toggleEditTypeFields()" required>
            <option value="video" <?= ($resource['type'] ?? '') === 'video' ? 'selected' : '' ?>>Video</option>
            <option value="note" <?= ($resource['type'] ?? '') === 'note' ? 'selected' : '' ?>>Note</option>
          </select>
        </div>
        <div>
          <label class="archive-edit-label" for="edit-tags">Tags (comma separated)</label>
          <input id="edit-tags" name="tags" class="archive-edit-input" value="<?= htmlspecialchars($resource['tags'] ?? '') ?>">
        </div>
      </div>

      <div id="edit-video-field" class="<?= ($resource['type'] ?? '') === 'video' ? '' : 'archive-edit-hidden' ?>">
        <label class="archive-edit-label" for="edit-video-link">YouTube Video URL</label>
        <input id="edit-video-link" name="video_link" class="archive-edit-input" value="<?= htmlspecialchars($resource['video_link'] ?? '') ?>">
      </div>

      <div id="edit-note-field" class="<?= ($resource['type'] ?? '') === 'note' ? '' : 'archive-edit-hidden' ?>">
        <label class="archive-edit-label" for="edit-note-file">Replace Note File (Optional)</label>
        <input id="edit-note-file" type="file" name="note_file" class="archive-edit-input" accept=".pdf,.doc,.docx,.ppt,.pptx">
        <small style="color:#64748b;">Leave empty to keep current file.</small>
      </div>

      <div class="archive-edit-actions">
        <a href="/dashboard/edu-archive/my-submissions" class="archive-btn archive-btn-secondary">Cancel</a>
        <button type="submit" class="archive-btn archive-btn-primary">Save Changes</button>
      </div>
    </form>
  </div>
</main>

<script>
function toggleEditTypeFields() {
  const type = document.getElementById('edit-type').value;
  const videoField = document.getElementById('edit-video-field');
  const noteField = document.getElementById('edit-note-field');

  if (type === 'video') {
    videoField.classList.remove('archive-edit-hidden');
    noteField.classList.add('archive-edit-hidden');
  } else {
    noteField.classList.remove('archive-edit-hidden');
    videoField.classList.add('archive-edit-hidden');
  }
}
</script>
