<div class="faq-section">
    <div class="faq-header">
        <h1>Frequently Asked Questions</h1>
        <p>Find answers to common questions about our Help & Feedback system.</p>
    </div>

    <div class="faq-search">
        <input 
            type="text" 
            id="faq-search-input" 
            placeholder="Search FAQs..." 
            class="faq-search-input"
        >
        <span id="search-result-count" class="search-result-count"></span>
    </div>

    <div class="faq-container">
        <?php if (empty($faqs)): ?>
            <div class="empty-state">
                <p>No FAQs available at the moment.</p>
            </div>
        <?php else: ?>
            <div class="accordion" id="faq-accordion">
                <?php foreach ($faqs as $index => $item): ?>
                    <div class="accordion-item" data-faq-id="<?php echo $item['id']; ?>">
                        <button 
                            class="accordion-button" 
                            type="button" 
                            data-bs-toggle="collapse" 
                            data-bs-target="#collapse-<?php echo $index; ?>"
                            onclick="toggleAccordion(this)"
                        >
                            <span class="accordion-question">
                                <?php echo htmlspecialchars($item['question']); ?>
                            </span>
                            <span class="accordion-icon">+</span>
                        </button>
                        <div 
                            id="collapse-<?php echo $index; ?>" 
                            class="accordion-collapse collapse" 
                            data-bs-parent="#faq-accordion"
                        >
                            <div class="accordion-body">
                                <?php echo nl2br(htmlspecialchars($item['answer'])); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="faq-footer">
        <p>Can't find your answer? <a href="/dashboard/help">Submit a question</a> to our support team.</p>
    </div>
</div>

<link rel="stylesheet" href="/css/app/help-section.css">
<link rel="stylesheet" href="/css/app/faq-section.css">
<script src="/js/app/help-section.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('faq-search-input');
    const accordion = document.getElementById('faq-accordion');
    const resultCount = document.getElementById('search-result-count');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const keyword = this.value.trim();

            if (keyword.length === 0) {
                // Show all FAQs
                const items = accordion.querySelectorAll('.accordion-item');
                items.forEach(item => {
                    item.style.display = 'block';
                });
                resultCount.textContent = '';
                return;
            }

            // Search FAQs
            fetch('/dashboard/help/faq-search?q=' + encodeURIComponent(keyword))
                .then(response => response.json())
                .then(data => {
                    const items = accordion.querySelectorAll('.accordion-item');
                    let visibleCount = 0;

                    items.forEach(item => {
                        const question = item.querySelector('.accordion-question').textContent.toLowerCase();
                        if (question.includes(keyword.toLowerCase())) {
                            item.style.display = 'block';
                            visibleCount++;
                        } else {
                            item.style.display = 'none';
                        }
                    });

                    resultCount.textContent = visibleCount + ' result' + (visibleCount !== 1 ? 's' : '');
                });
        });
    }
});

function toggleAccordion(button) {
    const icon = button.querySelector('.accordion-icon');
    if (icon) {
        icon.textContent = button.getAttribute('aria-expanded') === 'true' ? '+' : '−';
    }
}
</script>
