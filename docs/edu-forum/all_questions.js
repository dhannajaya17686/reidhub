/**
 * EDU FORUM: ALL QUESTIONS JAVASCRIPT
 * -----------------------------------
 * This file controls the main questions page.
 * It makes the search filters work automatically and adds a clear button to the search box.
 */

document.addEventListener('DOMContentLoaded', () => {
    
    // 1. Make filters work automatically
    // When a user picks a new filter (like changing the category), 
    // this automatically reloads the page to show the new results without them clicking a 'Search' button.
    const filterForm = document.querySelector('.search-form');
    if (filterForm) {
        const selects = filterForm.querySelectorAll('select');
        selects.forEach(select => {
            select.addEventListener('change', () => {
                filterForm.submit();
            });
        });
    }

    // 2. Add an 'X' to clear the search
    // If the user has typed something in the search box, this creates a small 'X' button next to it so they can quickly clear their search.
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput && searchInput.value.trim() !== '') {
        const clearBtn = document.createElement('button');
        clearBtn.innerHTML = '&times;';
        clearBtn.className = 'clear-search-btn';
        clearBtn.type = 'button';
        clearBtn.style.cssText = 'border:none; background:none; cursor:pointer; margin-left:-30px; color:#64748b;';
        
        // Put the 'X' button on the screen right next to the search box
        searchInput.parentNode.insertBefore(clearBtn, searchInput.nextSibling);

        clearBtn.addEventListener('click', () => {
            searchInput.value = '';
            filterForm.submit(); // Reload the page with an empty search to show all questions again
        });
    }

    // 3. Remember where the user was reading
    // When the user clicks to go to the next page of questions, 
    // this tells the browser to start at the top of the question list instead of the very top of the webpage.
    const paginationLinks = document.querySelectorAll('.pagination a');
    paginationLinks.forEach(link => {
        link.addEventListener('click', () => {
            sessionStorage.setItem('forumScrollPosition', 'feedTop');
        });
    });
});
