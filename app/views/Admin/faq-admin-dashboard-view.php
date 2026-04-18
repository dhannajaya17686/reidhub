<div class="faq-admin-section">
    <div class="faq-admin-header">
        <h1>FAQ Management</h1>
        <a href="/dashboard/admin/faq/add" class="btn btn-primary">+ Add New FAQ</a>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <div class="faq-admin-table">
        <?php if (empty($faqs)): ?>
            <div class="empty-state">
                <p>No FAQs yet. <a href="/dashboard/admin/faq/add">Create the first one</a></p>
            </div>
        <?php else: ?>
            <table class="faq-table">
                <thead>
                    <tr>
                        <th>Question</th>
                        <th>Order</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($faqs as $item): ?>
                        <tr class="faq-table-row">
                            <td class="faq-question">
                                <strong><?php echo htmlspecialchars($item['question']); ?></strong>
                            </td>
                            <td class="faq-order">
                                <?php echo $item['display_order']; ?>
                            </td>
                            <td class="faq-status">
                                <?php if ($item['is_active']): ?>
                                    <span class="badge badge-active">Active</span>
                                <?php else: ?>
                                    <span class="badge badge-inactive">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="faq-actions">
                                <a href="/dashboard/admin/faq/edit?id=<?php echo $item['id']; ?>" class="btn btn-small btn-secondary">Edit</a>
                                <button class="btn btn-small btn-danger" onclick="deleteFAQ(<?php echo $item['id']; ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<link rel="stylesheet" href="/css/app/help-section.css">
<link rel="stylesheet" href="/css/app/faq-admin-section.css">
<script src="/js/app/help-section.js"></script>

<script>
function deleteFAQ(faqId) {
    if (!confirm('Are you sure you want to delete this FAQ?')) return;

    const formData = new FormData();
    formData.append('faq_id', faqId);

    fetch('/dashboard/admin/faq/delete', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('FAQ deleted successfully!');
            location.reload();
        } else {
            alert('Error: ' + (data.error || 'Failed to delete FAQ'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to delete FAQ');
    });
}
</script>
