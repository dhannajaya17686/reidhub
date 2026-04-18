<link href="/css/app/user/edu-archive/archive.css" rel="stylesheet">

<style>
  .submission-page {
    max-width: 980px;
    margin: 0 auto;
    padding: 28px 20px 36px;
  }

  .submission-head h1 {
    margin: 0 0 6px;
    font-size: clamp(1.8rem, 1.3rem + 1vw, 2.3rem);
    color: #111827;
  }

  .submission-head p {
    margin: 0 0 22px;
    color: #64748b;
    font-size: 0.95rem;
  }

  .submission-card {
    background: #fff;
    border: 1px solid #dbe3ef;
    border-radius: 14px;
    padding: 20px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    margin-bottom: 26px;
  }

  .submission-form-grid {
    display: grid;
    gap: 14px;
  }

  .submission-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
  }

  .submission-label {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
    color: #1f2937;
    font-size: 0.9rem;
  }

  .submission-input,
  .submission-select,
  .submission-textarea {
    width: 100%;
    border: 1px solid #dbe3ef;
    border-radius: 10px;
    background: #f8fafc;
    padding: 10px 12px;
    color: #334155;
    font-size: 0.95rem;
    outline: none;
  }

  .submission-input:focus,
  .submission-select:focus,
  .submission-textarea:focus {
    border-color: #1d63d8;
    box-shadow: 0 0 0 3px rgba(29, 99, 216, 0.12);
    background: #fff;
  }

  .submission-textarea {
    min-height: 110px;
    resize: vertical;
  }

  .submission-hidden { display: none; }

  .submission-actions {
    display: flex;
    justify-content: flex-end;
    margin-top: 4px;
  }

  .submission-btn {
    border: none;
    background: #1d63d8;
    color: #fff;
    border-radius: 10px;
    padding: 10px 18px;
    font-weight: 700;
    font-size: 0.9rem;
    cursor: pointer;
  }

  .status-wrap h2 {
    margin: 0 0 12px;
    color: #111827;
    font-size: 1.4rem;
  }

  .status-table-wrap {
    overflow-x: auto;
    border: 1px solid #dbe3ef;
    border-radius: 12px;
    background: #fff;
  }

  .status-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 760px;
  }

  .status-table th,
  .status-table td {
    border-bottom: 1px solid #e8eef7;
    padding: 12px 10px;
    text-align: left;
    font-size: 0.9rem;
    color: #334155;
  }

  .status-table th {
    font-size: 0.82rem;
    color: #64748b;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    background: #f8fafc;
  }

  .status-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 999px;
    padding: 4px 10px;
    font-size: 0.74rem;
    font-weight: 700;
    text-transform: uppercase;
  }

  .status-pending { background: #fef3c7; color: #b45309; }
  .status-approved { background: #dcfce7; color: #166534; }
  .status-rejected { background: #fee2e2; color: #991b1b; }

  .status-action-link {
    color: #1d63d8;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.84rem;
  }

  .status-action-sep {
    color: #94a3b8;
    margin: 0 6px;
  }

  .status-delete-btn {
    border: none;
    background: none;
    color: #dc2626;
    padding: 0;
    font-size: 0.84rem;
    font-weight: 600;
    cursor: pointer;
  }

  .status-empty {
    padding: 16px;
    color: #64748b;
    font-size: 0.92rem;
  }

  @media (max-width: 760px) {
    .submission-row { grid-template-columns: 1fr; }
  }
</style>

<main class="submission-page">
  <div class="submission-head">
    <h1>Academic Resources</h1>
    <p>Submit and manage your academic resources</p>
  </div>

  <?php if (isset($_GET['success'])): ?>
    <div style="background:#dcfce7; color:#166534; border:1px solid #bbf7d0; padding:12px; border-radius:10px; margin-bottom:14px;">
      Resource submitted successfully. It is now pending review.
    </div>
  <?php endif; ?>

  <?php if (isset($_GET['error'])): ?>
    <div style="background:#fee2e2; color:#991b1b; border:1px solid #fecaca; padding:12px; border-radius:10px; margin-bottom:14px;">
      Upload failed. Please check the form and try again.
    </div>
  <?php endif; ?>

  <section class="submission-card">
    <form action="/dashboard/edu-archive/upload" method="POST" enctype="multipart/form-data" class="submission-form-grid">
      <div>
        <label class="submission-label" for="upload-title">Title</label>
        <input id="upload-title" type="text" name="title" class="submission-input" placeholder="Enter resource title" required>
      </div>

      <div>
        <label class="submission-label" for="upload-description">Description</label>
        <textarea id="upload-description" name="description" class="submission-textarea" placeholder="Describe this resource"></textarea>
      </div>

      <div class="submission-row">
        <div>
          <label class="submission-label" for="upload-subject">Subject/Module</label>
          <select id="upload-subject" name="subject" class="submission-select" required>
            <option value="">All Subjects</option>
            <option value="CS">Computer Science</option>
            <option value="IS">Information Systems</option>
            <option value="SE">Software Engineering</option>
          </select>
        </div>

        <div>
          <label class="submission-label" for="upload-type">Content Type</label>
          <select id="upload-type" name="type" class="submission-select" onchange="toggleUploadFields()" required>
            <option value="video">Video</option>
            <option value="note">Note</option>
          </select>
        </div>
      </div>

      <div class="submission-row">
        <div>
          <label class="submission-label" for="upload-year">Year</label>
          <select id="upload-year" name="year_level" class="submission-select" required>
            <option value="1">Year 1</option>
            <option value="2">Year 2</option>
            <option value="3">Year 3</option>
            <option value="4">Year 4</option>
            <option value="5">Post Graduate</option>
          </select>
        </div>

        <div>
          <label class="submission-label" for="upload-tags">Tags</label>
          <input id="upload-tags" type="text" name="tags" class="submission-input" placeholder="database, os, algorithms">
        </div>
      </div>

      <div id="videoField">
        <label class="submission-label" for="upload-video-link">YouTube Link</label>
        <input id="upload-video-link" type="url" name="video_link" class="submission-input" placeholder="Enter YouTube link (for videos)">
      </div>

      <div id="fileField" class="submission-hidden">
        <label class="submission-label" for="upload-note-file">Note File (PDF, DOC, DOCX, PPT, PPTX)</label>
        <input id="upload-note-file" type="file" name="note_file" class="submission-input" accept=".pdf,.doc,.docx,.ppt,.pptx">
      </div>

      <div class="submission-actions">
        <button type="submit" class="submission-btn">Submit</button>
      </div>
    </form>
  </section>

  <section class="status-wrap">
    <h2>Submission Status</h2>
    <div class="status-table-wrap">
      <?php if (empty($resources ?? [])): ?>
        <div class="status-empty">No submissions yet.</div>
      <?php else: ?>
        <table class="status-table">
          <thead>
            <tr>
              <th>Title</th>
              <th>Subject/Module</th>
              <th>Content Type</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($resources as $res): ?>
              <tr>
                <td><?= htmlspecialchars($res['title']) ?></td>
                <td><?= htmlspecialchars($res['subject']) ?></td>
                <td><?= ucfirst(htmlspecialchars($res['type'])) ?></td>
                <td>
                  <span class="status-badge status-<?= htmlspecialchars($res['status']) ?>">
                    <?= htmlspecialchars($res['status']) ?>
                  </span>
                </td>
                <td>
                  <?php if ($res['status'] === 'pending'): ?>
                    <a class="status-action-link" href="/dashboard/edu-archive/edit?id=<?= (int)$res['id'] ?>">Edit</a>
                    <span class="status-action-sep">|</span>
                    <form action="/dashboard/edu-archive/delete" method="POST" style="display:inline;" onsubmit="return confirm('Delete this submission?');">
                      <input type="hidden" name="id" value="<?= (int)$res['id'] ?>">
                      <button type="submit" class="status-delete-btn">Delete</button>
                    </form>
                  <?php elseif ($res['status'] === 'approved'): ?>
                    <a class="status-action-link" href="<?= $res['type'] === 'video' ? htmlspecialchars($res['video_link']) : htmlspecialchars($res['file_path']) ?>" target="_blank">View</a>
                  <?php else: ?>
                    <span style="color:#94a3b8; font-size:0.84rem;">Edit | Delete</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </section>
</main>

<script>
function toggleUploadFields() {
  const type = document.getElementById('upload-type').value;
  const videoField = document.getElementById('videoField');
  const fileField = document.getElementById('fileField');

  if (type === 'video') {
    videoField.classList.remove('submission-hidden');
    fileField.classList.add('submission-hidden');
  } else {
    fileField.classList.remove('submission-hidden');
    videoField.classList.add('submission-hidden');
  }
}
</script>
