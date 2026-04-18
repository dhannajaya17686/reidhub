<div class="help-admin-section">
    <div class="help-admin-header">
        <h1>Feedbacks and Complains Management</h1>
        <p>View and respond to user complains and feedbacks</p>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div class="admin-stats">
        <div class="stat-card">
            <div class="stat-value"><?php echo $totalQuestions; ?></div>
            <div class="stat-label">Total Questions</div>
        </div>
        <div class="stat-card stat-pending">
            <div class="stat-value"><?php echo $pendingCount; ?></div>
            <div class="stat-label">Pending</div>
        </div>
        <div class="stat-card stat-bug">
            <div class="stat-value"><?php echo $bugReportCount; ?></div>
            <div class="stat-label">Academic Issues</div>
        </div>
        <div class="stat-card stat-feature">
            <div class="stat-value"><?php echo $featureRequestCount; ?></div>
            <div class="stat-label">Infrastructure Issues</div>
        </div>
    </div>

    <div class="admin-filters">
        <div class="filter-group">
            <label for="status-filter">Status:</label>
            <select id="status-filter">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="replied">Replied</option>
                <option value="resolved">Resolved</option>
                <option value="closed">Closed</option>
            </select>
        </div>

        <div class="filter-group">
            <label for="category-filter">Category:</label>
            <select id="category-filter">
                <option value="">All Categories</option>
                <option value="academic_issues">Academic Issues</option>
                <option value="extracurricular_issues">Extracurricular Issues</option>
                <option value="sports_issues">Sports Issues</option>
                <option value="infrastructure_issues">Infrastructure Issues</option>
                <option value="other_issues">Other Issues</option>
                <option value="feedbacks">Feedbacks</option>
            </select>
        </div>

        <button id="apply-filters" class="btn btn-secondary">Apply Filters</button>
    </div>

    <div class="admin-questions-list" id="questions-container">
        <div class="loading">Loading questions...</div>
    </div>

    <div id="pagination" class="pagination"></div>
</div>

<link rel="stylesheet" href="/css/app/help-section.css">
<script src="/js/app/help-section.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusFilter = document.getElementById('status-filter');
    const categoryFilter = document.getElementById('category-filter');
    const applyBtn = document.getElementById('apply-filters');
    const questionsContainer = document.getElementById('questions-container');
    const paginationDiv = document.getElementById('pagination');

    function loadQuestions(page = 1) {
        const status = statusFilter.value;
        const category = categoryFilter.value;
        
        const url = '/dashboard/admin/help/questions-api?page=' + page + 
                    (status ? '&status=' + status : '') + 
                    (category ? '&category=' + category : '');

        console.log('Fetching URL:', url);
        
        fetch(url)
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }
                return response.text();
            })
            .then(text => {
                console.log('Response text:', text);
                const data = JSON.parse(text);
                console.log('Parsed data:', data);
                if (data.success) {
                    console.log('Questions count:', data.data.length);
                    renderQuestions(data.data);
                    renderPagination(data.page, Math.ceil(data.data.length));
                } else {
                    questionsContainer.innerHTML = '<p class="alert alert-error">' + (data.error || 'Error loading questions') + '</p>';
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                questionsContainer.innerHTML = '<p class="alert alert-error">Failed to load questions. Error: ' + error.message + '</p>';
            });
    }

    function renderQuestions(questions) {
        if (questions.length === 0) {
            questionsContainer.innerHTML = '<p class="no-results">No questions found.</p>';
            return;
        }

        let html = '<div class="questions-grid">';
        questions.forEach(question => {
            const categoryDisplay = question.category.replace(/_/g, ' ').toUpperCase();
            const statusDisplay = question.status.charAt(0).toUpperCase() + question.status.slice(1);
            const imageHtml = question.image_path ? `<div style="margin-top: 10px; margin-bottom: 10px;"><img src="${escapeHtml(question.image_path)}" alt="Question Image" style="max-width: 100%; max-height: 200px; border-radius: 6px;"></div>` : '';
            
            html += `
                <div class="admin-question-card">
                    <div class="question-header">
                        <h3>${escapeHtml(question.subject)}</h3>
                        <span class="badge badge-${question.status}">${statusDisplay}</span>
                    </div>
                    <div class="question-meta">
                        <span class="category-badge">${categoryDisplay}</span>
                        <span class="date">${new Date(question.created_at).toLocaleDateString()}</span>
                    </div>
                    <p class="question-preview">${escapeHtml(question.message).substring(0, 100)}...</p>
                    ${imageHtml}
                    <a href="/dashboard/admin/help/question?id=${question.id}" class="btn btn-small">View & Reply</a>
                </div>
            `;
        });
        html += '</div>';
        questionsContainer.innerHTML = html;
    }

    function renderPagination(currentPage, totalPages) {
        // Simple pagination
        paginationDiv.innerHTML = '';
    }

    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    applyBtn.addEventListener('click', () => loadQuestions(1));

    // Load questions on page load
    loadQuestions();
});
</script>
