/**
 * userhelp.js — Codecheck Validation & Feature Snippets
 * Lost & Found Module | ReidHub
 *
 * Covers all codecheck task scenarios:
 *  1.  Text field → DB → table with "High Value" tag (>100000)
 *  2.  Phone number: 10 digits, must start with 07
 *  3.  Age field validation
 *  4.  Date of registration — no future dates
 *  5.  Image file-type validation (jpg/png/webp only)
 *  6.  Vehicle chassis number format (text-digits-text)
 *  7.  PDF-only file upload
 *  8.  NIC validation (old 9-digit + V / new 12-digit)
 *  9.  Telephone with country-code dropdown + 9-digit number
 * 10.  District search with partial matching
 * 11.  Cascading / dependent dropdown
 * 12.  Conditional text field (offered/rejected status)
 * 13.  Textarea character limit based on dropdown selection
 * 14.  Time-period (months) from start & end date
 * 15.  Discount percentage (0–80) — mirrors server-side PHP rule
 * 16.  Summary card — highest value in a column
 * 17.  Pin announcement with auto-expiry
 */

'use strict';

/* ─────────────────────────────────────────────
   1. HIGH-VALUE TAG  (threshold: 100 000)
   Show a "High Value" badge next to a table cell
   when the numeric value exceeds 100 000.
───────────────────────────────────────────── */
function renderHighValueTag(value, cellElement) {
  if (parseFloat(value) > 100000) {
    const tag = document.createElement('span');
    tag.className = 'badge-high-value';
    tag.textContent = 'High Value';
    tag.style.cssText =
      'margin-left:8px;padding:2px 8px;background:#ef4444;color:#fff;' +
      'border-radius:999px;font-size:11px;font-weight:600;';
    cellElement.appendChild(tag);
  }
}

// Usage in a table render loop:
// document.querySelectorAll('[data-value]').forEach(cell => {
//   renderHighValueTag(cell.dataset.value, cell);
// });


/* ─────────────────────────────────────────────
   2. PHONE NUMBER — 10 digits, starts with 07
───────────────────────────────────────────── */
function validatePhone(phone) {
  const stripped = phone.replace(/\s/g, '');
  const phoneRegex = /^07\d{8}$/;
  return phoneRegex.test(stripped);
}

function attachPhoneValidation(inputId, errorId) {
  const input = document.getElementById(inputId);
  const error = document.getElementById(errorId);
  if (!input || !error) return;

  input.addEventListener('blur', () => {
    if (input.value && !validatePhone(input.value)) {
      error.textContent = 'Phone number must be 10 digits and start with 07';
      error.style.display = 'block';
    } else {
      error.textContent = '';
      error.style.display = 'none';
    }
  });

  // Block non-numeric keys
  input.addEventListener('keypress', (e) => {
    if (!/[0-9]/.test(e.key)) e.preventDefault();
  });
}


/* ─────────────────────────────────────────────
   3. AGE FIELD VALIDATION  (1–120)
───────────────────────────────────────────── */
function validateAge(age) {
  const num = parseInt(age, 10);
  return !isNaN(num) && num >= 1 && num <= 120;
}

function attachAgeValidation(inputId, errorId) {
  const input = document.getElementById(inputId);
  const error = document.getElementById(errorId);
  if (!input || !error) return;

  input.addEventListener('input', () => {
    if (input.value && !validateAge(input.value)) {
      error.textContent = 'Age must be between 1 and 120';
      error.style.display = 'block';
    } else {
      error.textContent = '';
      error.style.display = 'none';
    }
  });
}


/* ─────────────────────────────────────────────
   4. DATE FIELD — no future dates
   (e.g. date of company registration / date found)
───────────────────────────────────────────── */
function restrictFutureDates(inputId, errorId) {
  const input = document.getElementById(inputId);
  const error = document.getElementById(errorId);
  if (!input) return;

  const today = new Date().toISOString().split('T')[0];
  input.setAttribute('max', today);

  input.addEventListener('change', () => {
    if (input.value > today) {
      if (error) {
        error.textContent = 'Date cannot be in the future';
        error.style.display = 'block';
      }
      input.value = '';
    } else {
      if (error) error.style.display = 'none';
    }
  });
}


/* ─────────────────────────────────────────────
   5. IMAGE FILE-TYPE VALIDATION
   Allowed: jpg, jpeg, png, webp
───────────────────────────────────────────── */
function validateImageFile(file) {
  const ALLOWED_TYPES = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
  const MAX_SIZE_MB = 5;

  if (!ALLOWED_TYPES.includes(file.type)) {
    alert('Invalid file type. Please upload a JPG, PNG, or WebP image.');
    return false;
  }
  if (file.size > MAX_SIZE_MB * 1024 * 1024) {
    alert(`File too large. Maximum size is ${MAX_SIZE_MB}MB.`);
    return false;
  }
  return true;
}

function attachImageValidation(inputId) {
  const input = document.getElementById(inputId);
  if (!input) return;
  input.addEventListener('change', () => {
    if (input.files[0] && !validateImageFile(input.files[0])) {
      input.value = '';
    }
  });
}


/* ─────────────────────────────────────────────
   6. VEHICLE CHASSIS NUMBER
   Format: first char = letter, last char = letter,
           middle chars = digits only
   Example valid: A123456B
───────────────────────────────────────────── */
function validateChassisNumber(chassis) {
  // First and last must be letters; everything in between must be digits
  const chassisRegex = /^[A-Za-z][0-9]+[A-Za-z]$/;
  return chassisRegex.test(chassis.trim());
}

function attachChassisValidation(inputId, errorId) {
  const input = document.getElementById(inputId);
  const error = document.getElementById(errorId);
  if (!input || !error) return;

  input.addEventListener('blur', () => {
    if (input.value && !validateChassisNumber(input.value)) {
      error.textContent =
        'Chassis number: first & last characters must be letters; middle characters must be digits (e.g. A12345B)';
      error.style.display = 'block';
    } else {
      error.textContent = '';
      error.style.display = 'none';
    }
  });
}


/* ─────────────────────────────────────────────
   7. PDF-ONLY FILE UPLOAD
───────────────────────────────────────────── */
function validatePdfFile(file) {
  if (file.type !== 'application/pdf') {
    alert('Only PDF files are allowed.');
    return false;
  }
  const MAX_MB = 10;
  if (file.size > MAX_MB * 1024 * 1024) {
    alert(`PDF too large. Maximum size is ${MAX_MB}MB.`);
    return false;
  }
  return true;
}

function attachPdfValidation(inputId) {
  const input = document.getElementById(inputId);
  if (!input) return;
  input.addEventListener('change', () => {
    if (input.files[0] && !validatePdfFile(input.files[0])) {
      input.value = '';
    }
  });
}


/* ─────────────────────────────────────────────
   8. NIC VALIDATION
   Old format : 9 digits + V or X  (e.g. 912345678V)
   New format : 12 digits           (e.g. 200112345678)
───────────────────────────────────────────── */
function validateNIC(nic) {
  const oldNIC = /^\d{9}[VvXx]$/;
  const newNIC = /^\d{12}$/;
  return oldNIC.test(nic.trim()) || newNIC.test(nic.trim());
}

function attachNICValidation(inputId, errorId) {
  const input = document.getElementById(inputId);
  const error = document.getElementById(errorId);
  if (!input || !error) return;

  input.addEventListener('blur', () => {
    if (input.value && !validateNIC(input.value)) {
      error.textContent =
        'Invalid NIC. Use old format (9 digits + V/X) or new format (12 digits).';
      error.style.display = 'block';
    } else {
      error.textContent = '';
      error.style.display = 'none';
    }
  });
}


/* ─────────────────────────────────────────────
   9. TELEPHONE — country code dropdown + 9-digit number
───────────────────────────────────────────── */
function attachTelephoneValidation(countryCodeSelectId, numberInputId, errorId) {
  const numberInput = document.getElementById(numberInputId);
  const error = document.getElementById(errorId);
  if (!numberInput || !error) return;

  // Block non-numeric input
  numberInput.addEventListener('keypress', (e) => {
    if (!/[0-9]/.test(e.key)) e.preventDefault();
  });

  numberInput.addEventListener('blur', () => {
    const stripped = numberInput.value.replace(/\s/g, '');
    if (stripped && !/^\d{9}$/.test(stripped)) {
      error.textContent = 'Local number must be exactly 9 digits';
      error.style.display = 'block';
    } else {
      error.textContent = '';
      error.style.display = 'none';
    }
  });
}

/* HTML reference:
  <select id="country-code">
    <option value="+94">+94 (Sri Lanka)</option>
    <option value="+1">+1 (USA)</option>
    <option value="+44">+44 (UK)</option>
  </select>
  <input type="text" id="local-number" maxlength="9" placeholder="712345678">
  <span id="tel-error" class="form-error"></span>

  attachTelephoneValidation('country-code', 'local-number', 'tel-error');
*/


/* ─────────────────────────────────────────────
   10. DISTRICT SEARCH — partial matching
───────────────────────────────────────────── */
function setupDistrictSearch(searchInputId, listContainerSelector, itemSelector, districtAttr) {
  const input = document.getElementById(searchInputId);
  if (!input) return;

  input.addEventListener('input', () => {
    const query = input.value.trim().toLowerCase();
    const items = document.querySelectorAll(`${listContainerSelector} ${itemSelector}`);

    items.forEach((item) => {
      const district = (item.getAttribute(districtAttr) || item.textContent).toLowerCase();
      item.style.display = district.includes(query) ? '' : 'none';
    });
  });
}

/* Usage:
  setupDistrictSearch(
    'district-search',     // <input id="district-search">
    '#farmer-list',        // container
    '.farmer-row',         // each row element
    'data-district'        // attribute holding district name
  );
*/


/* ─────────────────────────────────────────────
   11. CASCADING / DEPENDENT DROPDOWN
   Parent selection filters child options.
───────────────────────────────────────────── */
const CASCADE_DATA = {
  // parentValue: [childOptions]
  electronics: ['Mobile', 'Laptop', 'Tablet', 'Camera'],
  clothing:    ['Shirt', 'Pants', 'Shoes', 'Hat'],
  furniture:   ['Chair', 'Table', 'Sofa', 'Shelf'],
};

function setupCascadingDropdown(parentId, childId, data) {
  const parent = document.getElementById(parentId);
  const child  = document.getElementById(childId);
  if (!parent || !child) return;

  parent.addEventListener('change', () => {
    const options = data[parent.value] || [];
    child.innerHTML = '<option value="">-- Select --</option>';
    options.forEach((opt) => {
      const el = document.createElement('option');
      el.value = opt.toLowerCase().replace(/\s+/g, '_');
      el.textContent = opt;
      child.appendChild(el);
    });
    child.disabled = options.length === 0;
  });
}

/* Usage:
  setupCascadingDropdown('category-select', 'subcategory-select', CASCADE_DATA);
*/


/* ─────────────────────────────────────────────
   12. CONDITIONAL TEXT FIELD
   Show extra text field only when status = "offered"
───────────────────────────────────────────── */
function setupConditionalField(triggerSelectId, targetFieldWrapperId, triggerValue) {
  const select  = document.getElementById(triggerSelectId);
  const wrapper = document.getElementById(targetFieldWrapperId);
  if (!select || !wrapper) return;

  function toggle() {
    const show = select.value === triggerValue;
    wrapper.style.display = show ? 'block' : 'none';
    const inputs = wrapper.querySelectorAll('input, textarea');
    inputs.forEach((el) => {
      show ? el.setAttribute('required', 'required') : el.removeAttribute('required');
      if (!show) el.value = '';
    });
  }

  select.addEventListener('change', toggle);
  toggle(); // run on load
}

/* Usage:
  setupConditionalField('interview-status', 'offer-details-wrapper', 'offered');
*/


/* ─────────────────────────────────────────────
   13. TEXTAREA CHAR LIMIT — changes by dropdown value
───────────────────────────────────────────── */
const TEXTAREA_LIMITS = {
  short:   100,
  medium:  300,
  long:    500,
  default: 500,
};

function setupDynamicTextareaLimit(dropdownId, textareaId, counterId, limitsMap) {
  const dropdown = document.getElementById(dropdownId);
  const textarea = document.getElementById(textareaId);
  const counter  = document.getElementById(counterId);
  if (!dropdown || !textarea) return;

  function applyLimit() {
    const limit = limitsMap[dropdown.value] || limitsMap.default;
    textarea.setAttribute('maxlength', limit);
    if (counter) counter.textContent = `${textarea.value.length} / ${limit}`;
  }

  dropdown.addEventListener('change', () => {
    textarea.value = '';
    applyLimit();
  });

  textarea.addEventListener('input', () => {
    const limit = parseInt(textarea.getAttribute('maxlength'), 10);
    if (counter) counter.textContent = `${textarea.value.length} / ${limit}`;
  });

  applyLimit();
}

/* Usage:
  setupDynamicTextareaLimit('type-select', 'description', 'desc-counter', TEXTAREA_LIMITS);
*/


/* ─────────────────────────────────────────────
   14. TIME PERIOD IN MONTHS (from start & end date)
───────────────────────────────────────────── */
function calcMonthsBetween(startDateStr, endDateStr) {
  const start = new Date(startDateStr);
  const end   = new Date(endDateStr);
  if (isNaN(start) || isNaN(end) || end < start) return null;
  return (end.getFullYear() - start.getFullYear()) * 12 +
         (end.getMonth() - start.getMonth());
}

function setupTimePeriodCalculator(startId, endId, outputId) {
  const startEl  = document.getElementById(startId);
  const endEl    = document.getElementById(endId);
  const outputEl = document.getElementById(outputId);
  if (!startEl || !endEl || !outputEl) return;

  function update() {
    const months = calcMonthsBetween(startEl.value, endEl.value);
    if (months === null) {
      outputEl.textContent = '';
    } else {
      outputEl.textContent = `${months} month${months !== 1 ? 's' : ''}`;
    }
  }

  startEl.addEventListener('change', update);
  endEl.addEventListener('change', update);
}

/* Usage:
  setupTimePeriodCalculator('contract-start', 'contract-end', 'time-period-display');

  HTML:
  <input type="date" id="contract-start">
  <input type="date" id="contract-end">
  <span id="time-period-display"></span>
*/


/* ─────────────────────────────────────────────
   15. DISCOUNT PERCENTAGE VALIDATION (0–80)
   Mirrors the PHP server-side rule:
     if ($discount < 0 || $discount > 80) → reject
───────────────────────────────────────────── */
function validateDiscount(value) {
  const num = parseFloat(value);
  return !isNaN(num) && num >= 0 && num <= 80;
}

function attachDiscountValidation(inputId, errorId) {
  const input = document.getElementById(inputId);
  const error = document.getElementById(errorId);
  if (!input || !error) return;

  input.addEventListener('blur', () => {
    if (input.value !== '' && !validateDiscount(input.value)) {
      error.textContent = 'Discount must be between 0 and 80 (%)';
      error.style.display = 'block';
    } else {
      error.textContent = '';
      error.style.display = 'none';
    }
  });
}


/* ─────────────────────────────────────────────
   16. SUMMARY CARD — highest value in a column
───────────────────────────────────────────── */
function getHighestColumnValue(tableId, columnIndex) {
  const rows = document.querySelectorAll(`#${tableId} tbody tr`);
  let max = -Infinity;
  rows.forEach((row) => {
    const cell = row.cells[columnIndex];
    if (!cell) return;
    const val = parseFloat(cell.textContent.replace(/[^0-9.]/g, ''));
    if (!isNaN(val) && val > max) max = val;
  });
  return max === -Infinity ? 0 : max;
}

function renderHighestValueCard(tableId, columnIndex, cardElementId, label) {
  const card = document.getElementById(cardElementId);
  if (!card) return;
  const highest = getHighestColumnValue(tableId, columnIndex);
  card.innerHTML = `
    <div class="summary-card">
      <p class="card-label">${label}</p>
      <p class="card-value">${highest.toLocaleString()}</p>
    </div>`;
}

/* Usage:
  // After table is rendered:
  renderHighestValueCard('transactions-table', 3, 'highest-value-card', 'Highest Transaction');
*/


/* ─────────────────────────────────────────────
   17. PIN ANNOUNCEMENT WITH AUTO-EXPIRY
   Saves expiry timestamp to localStorage.
   Pinned items appear at top; expired ones are removed.
───────────────────────────────────────────── */
const PinManager = {
  STORAGE_KEY: 'pinnedAnnouncements',

  pin(announcementId, durationHours) {
    const pins = this._load();
    pins[announcementId] = Date.now() + durationHours * 3600 * 1000;
    this._save(pins);
  },

  unpin(announcementId) {
    const pins = this._load();
    delete pins[announcementId];
    this._save(pins);
  },

  isPinned(announcementId) {
    const pins = this._load();
    if (!pins[announcementId]) return false;
    if (Date.now() > pins[announcementId]) {
      this.unpin(announcementId); // expired — auto-remove
      return false;
    }
    return true;
  },

  sortWithPinnedFirst(listContainerSelector, itemSelector, idAttr) {
    const container = document.querySelector(listContainerSelector);
    if (!container) return;

    const items = Array.from(container.querySelectorAll(itemSelector));
    const pinned   = items.filter((el) => this.isPinned(el.getAttribute(idAttr)));
    const unpinned = items.filter((el) => !this.isPinned(el.getAttribute(idAttr)));

    // Mark pinned visually
    pinned.forEach((el) => el.classList.add('is-pinned'));
    unpinned.forEach((el) => el.classList.remove('is-pinned'));

    // Re-append in correct order
    [...pinned, ...unpinned].forEach((el) => container.appendChild(el));
  },

  _load()       { return JSON.parse(localStorage.getItem(this.STORAGE_KEY) || '{}'); },
  _save(data)   { localStorage.setItem(this.STORAGE_KEY, JSON.stringify(data)); },
};

/* Usage:
  PinManager.pin('ann-42', 3);  // pin for 3 hours
  PinManager.sortWithPinnedFirst('#announcements-list', '.announcement-card', 'data-id');
*/


/* ─────────────────────────────────────────────
   SAMPLE MODEL (PHP reference comment)
   Copy to your Model file as needed.
───────────────────────────────────────────── */
/*
  // Add new column to DB:
  ALTER TABLE users ADD COLUMN age INT DEFAULT NULL;
  ALTER TABLE companies ADD COLUMN registration_date DATE DEFAULT NULL;
  ALTER TABLE contracts ADD COLUMN time_period_months INT DEFAULT NULL;
  ALTER TABLE items ADD COLUMN nic VARCHAR(12) DEFAULT NULL;

  // PHP Model — save a new field:
  public function saveExtraField($userId, $value) {
      $stmt = $this->db->prepare(
          "UPDATE users SET age = ? WHERE id = ?"
      );
      return $stmt->execute([$value, $userId]);
  }

  // PHP Model — retrieve for table:
  public function getUsersWithAge() {
      $stmt = $this->db->query("SELECT id, name, age FROM users");
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
*/


/* ─────────────────────────────────────────────
   SAMPLE VIEW SNIPPETS (PHP/HTML reference comment)
───────────────────────────────────────────── */
/*
  <!-- Input field in a form -->
  <div class="form-group">
    <label for="age">Age</label>
    <input type="number" id="age" name="age" min="1" max="120" class="form-control">
    <span id="age-error" class="form-error" style="display:none;color:red;"></span>
  </div>

  <!-- Display in profile -->
  <p><strong>Age:</strong> <?= htmlspecialchars($user['age'] ?? 'N/A') ?></p>

  <!-- Table column -->
  <td data-value="<?= $row['amount'] ?>">
    <?= number_format($row['amount'], 2) ?>
  </td>

  <!-- District search input -->
  <input type="text" id="district-search" placeholder="Search by district...">

  <!-- Phone field with country code -->
  <select id="country-code">
    <option value="+94">+94 (LK)</option>
    <option value="+1">+1 (US)</option>
  </select>
  <input type="text" id="local-number" maxlength="9">
  <span id="tel-error" class="form-error"></span>
*/


/* ─────────────────────────────────────────────
   AUTO-INIT: wire up common fields if present
   (Remove / comment out what you don't need)
───────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
  // Phone
  attachPhoneValidation('mobile', 'mobile-error');
  attachPhoneValidation('phone',  'phone-error');

  // Age
  attachAgeValidation('age', 'age-error');

  // Dates — no future
  restrictFutureDates('registration-date', 'registration-date-error');
  restrictFutureDates('date-lost',  'date-lost-error');
  restrictFutureDates('date-found', 'date-found-error');

  // File uploads
  attachImageValidation('profile-photo');
  attachImageValidation('item-image');
  attachPdfValidation('document-upload');

  // Chassis
  attachChassisValidation('chassis-number', 'chassis-error');

  // NIC
  attachNICValidation('nic', 'nic-error');

  // Telephone with code
  attachTelephoneValidation('country-code', 'local-number', 'tel-error');

  // Discount
  attachDiscountValidation('discount-percentage', 'discount-error');

  // District search
  setupDistrictSearch('district-search', '#farmer-list', '.farmer-row', 'data-district');

  // Cascading dropdown
  setupCascadingDropdown('category-select', 'subcategory-select', CASCADE_DATA);

  // Conditional field (offered/rejected)
  setupConditionalField('interview-status', 'offer-details-wrapper', 'offered');

  // Dynamic textarea limit
  setupDynamicTextareaLimit('type-select', 'description', 'desc-counter', TEXTAREA_LIMITS);

  // Time period calculator
  setupTimePeriodCalculator('start-date', 'end-date', 'time-period-display');

  // Highest value summary card
  renderHighestValueCard('data-table', 2, 'highest-value-card', 'Highest Value');

  // Pin announcements — sort on load
  PinManager.sortWithPinnedFirst('#announcements-list', '.announcement-card', 'data-id');
});
