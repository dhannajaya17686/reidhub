<link rel="stylesheet" href="/css/app/user/lost-and-found/report-found-item.css">

<!-- Main Report Found Item Form -->
<main class="report-found-main" role="main" aria-label="Report Found Item Form">
  
  <!-- Page Header -->
  <div class="page-header">
    <div class="header-content">
      <h1 class="page-title">Report Found Item</h1>
      <p class="page-subtitle">Help someone recover their lost item by providing detailed information about what you found.</p>
    </div>
  </div>

  <!-- Report Found Item Form -->
  <form class="report-found-form" id="report-found-form" method="POST" action="/dashboard/lost-found/report-found" enctype="multipart/form-data">
    
    <!-- Basic Information Section -->
    <div class="form-section">
      <h2 class="section-title">Item Information</h2>
      
      <div class="form-grid">
        <!-- Item Name -->
        <div class="form-group">
          <label for="item-name" class="form-label">Item Name *</label>
          <input 
            type="text" 
            id="item-name" 
            name="item_name" 
            class="form-input" 
            placeholder="e.g., Backpack, Laptop, Phone" 
            required
            maxlength="100"
          >
          <div class="form-error" id="item-name-error"></div>
        </div>

        <!-- Category -->
        <div class="form-group">
          <label for="category" class="form-label">Category *</label>
          <select id="category" name="category" class="form-select" required>
            <option value="">Select Category</option>
            <option value="electronics">Electronics</option>
            <option value="bags">Bags & Backpacks</option>
            <option value="clothing">Clothing</option>
            <option value="accessories">Accessories</option>
            <option value="books">Books & Stationery</option>
            <option value="documents">Documents & IDs</option>
            <option value="keys">Keys</option>
            <option value="jewelry">Jewelry</option>
            <option value="sports">Sports Equipment</option>
            <option value="other">Other</option>
          </select>
          <div class="form-error" id="category-error"></div>
        </div>
      </div>

      <!-- Description -->
      <div class="form-group">
        <label for="description" class="form-label">Description (How you found the item) *</label>
        <textarea 
          id="description" 
          name="description" 
          class="form-textarea" 
          placeholder="Describe the item in detail and explain where/how you found it. Include color, brand, condition, distinctive features..."
          rows="4"
          required
          maxlength="500"
        ></textarea>
        <div class="char-counter">
          <span id="desc-count">0</span>/500 characters
        </div>
        <div class="form-error" id="description-error"></div>
      </div>
    </div>

    <!-- Location & Time Section -->
    <div class="form-section">
      <h2 class="section-title">Found Location & Time</h2>
      
      <div class="form-grid">
        <!-- Found Location -->
        <div class="form-group">
          <label for="location" class="form-label">Where did you find it? *</label>
          <input 
            type="text" 
            id="location" 
            name="location" 
            class="form-input" 
            placeholder="e.g., Library, Cafeteria, Computer Lab A" 
            required
            maxlength="100"
          >
          <div class="form-error" id="location-error"></div>
        </div>

        <!-- Specific Area -->
        <div class="form-group">
          <label for="specific-area" class="form-label">Specific Location</label>
          <input 
            type="text" 
            id="specific-area" 
            name="specific_area" 
            class="form-input" 
            placeholder="e.g., 2nd floor, under table, near entrance" 
            maxlength="100"
          >
        </div>
      </div>

      <div class="form-grid">
        <!-- Date Found -->
        <div class="form-group">
          <label for="date-found" class="form-label">Date Found *</label>
          <input 
            type="date" 
            id="date-found" 
            name="date_found" 
            class="form-input" 
            required
            max="<?php echo date('Y-m-d'); ?>"
          >
          <div class="form-error" id="date-found-error"></div>
        </div>

        <!-- Time Found -->
        <div class="form-group">
          <label for="time-found" class="form-label">Approximate Time Found</label>
          <input 
            type="time" 
            id="time-found" 
            name="time_found" 
            class="form-input"
          >
        </div>
      </div>
    </div>

    <!-- Item Condition Section -->
    <div class="form-section">
      <h2 class="section-title">Item Condition</h2>
      <p class="section-subtitle">Help the owner understand the current state of their item.</p>
      
      <div class="form-group">
        <div class="condition-group">
          <label class="condition-option excellent-condition">
            <input type="radio" name="condition" value="excellent" required>
            <span class="condition-custom"></span>
            <div class="condition-content">
              <span class="condition-title">Excellent</span>
              <span class="condition-description">Perfect condition, no visible damage</span>
            </div>
            <div class="condition-icon">✨</div>
          </label>
          
          <label class="condition-option good-condition">
            <input type="radio" name="condition" value="good" required>
            <span class="condition-custom"></span>
            <div class="condition-content">
              <span class="condition-title">Good</span>
              <span class="condition-description">Minor wear, fully functional</span>
            </div>
            <div class="condition-icon">👍</div>
          </label>
          
          <label class="condition-option fair-condition">
            <input type="radio" name="condition" value="fair" required>
            <span class="condition-custom"></span>
            <div class="condition-content">
              <span class="condition-title">Fair</span>
              <span class="condition-description">Noticeable wear, may need attention</span>
            </div>
            <div class="condition-icon">⚠️</div>
          </label>

          <label class="condition-option damaged-condition">
            <input type="radio" name="condition" value="damaged" required>
            <span class="condition-custom"></span>
            <div class="condition-content">
              <span class="condition-title">Damaged</span>
              <span class="condition-description">Significant damage or not working</span>
            </div>
            <div class="condition-icon">⚠️</div>
          </label>
        </div>
        <div class="form-error" id="condition-error"></div>
      </div>
    </div>

    <!-- Contact Information Section -->
    <div class="form-section">
      <h2 class="section-title">Your Contact Information</h2>
      <p class="section-subtitle">We'll share this with the owner so they can contact you to retrieve their item.</p>
      
      <div class="form-grid">
        <!-- Mobile Number -->
        <div class="form-group">
          <label for="mobile" class="form-label">Mobile Number *</label>
          <input 
            type="tel" 
            id="mobile" 
            name="mobile" 
            class="form-input" 
            placeholder="e.g., +94 71 234 5678" 
            required
            maxlength="15"
          >
          <div class="form-error" id="mobile-error"></div>
        </div>

        <!-- Email -->
        <div class="form-group">
          <label for="email" class="form-label">Email Address *</label>
          <input 
            type="email" 
            id="email" 
            name="email" 
            class="form-input" 
            placeholder="your.email@ucsc.cmb.ac.lk"
            required
            value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>"
          >
          <div class="form-error" id="email-error"></div>
        </div>
      </div>

      <!-- Alternative Contact -->
      <div class="form-group">
        <label for="alt-contact" class="form-label">Alternative Contact Method</label>
        <input 
          type="text" 
          id="alt-contact" 
          name="alt_contact" 
          class="form-input" 
          placeholder="e.g., WhatsApp, Telegram, Room number, Office location"
          maxlength="100"
        >
      </div>
    </div>

    <!-- Images Section -->
    <div class="form-section">
      <h2 class="section-title">Upload Photos *</h2>
      <p class="section-subtitle">Clear photos are essential for the owner to identify their item. Please upload at least one photo.</p>
      
      <div class="image-upload-container">
        <!-- Main Image -->
        <div class="image-slot main-image required-image" data-slot="0">
          <input type="file" id="image-0" name="images[]" accept="image/*" class="image-input" hidden required>
          <div class="image-preview" id="preview-0">
            <div class="upload-placeholder">
              <svg class="upload-icon" viewBox="0 0 24 24" fill="none">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" stroke="currentColor" stroke-width="2"/>
                <polyline points="17,8 12,3 7,8" stroke="currentColor" stroke-width="2"/>
                <line x1="12" y1="3" x2="12" y2="15" stroke="currentColor" stroke-width="2"/>
              </svg>
              <span class="upload-text">Main Photo *</span>
              <span class="upload-hint">Click to upload (Required)</span>
            </div>
          </div>
          <button type="button" class="remove-image" style="display: none;">×</button>
          <div class="main-badge">MAIN</div>
        </div>

        <!-- Additional Images -->
        <div class="image-slot" data-slot="1">
          <input type="file" id="image-1" name="images[]" accept="image/*" class="image-input" hidden>
          <div class="image-preview" id="preview-1">
            <div class="upload-placeholder">
              <svg class="upload-icon" viewBox="0 0 24 24" fill="none">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" stroke="currentColor" stroke-width="2"/>
                <polyline points="17,8 12,3 7,8" stroke="currentColor" stroke-width="2"/>
                <line x1="12" y1="3" x2="12" y2="15" stroke="currentColor" stroke-width="2"/>
              </svg>
              <span class="upload-text">Photo 2</span>
              <span class="upload-hint">Optional</span>
            </div>
          </div>
          <button type="button" class="remove-image" style="display: none;">×</button>
        </div>

        <div class="image-slot" data-slot="2">
          <input type="file" id="image-2" name="images[]" accept="image/*" class="image-input" hidden>
          <div class="image-preview" id="preview-2">
            <div class="upload-placeholder">
              <svg class="upload-icon" viewBox="0 0 24 24" fill="none">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" stroke="currentColor" stroke-width="2"/>
                <polyline points="17,8 12,3 7,8" stroke="currentColor" stroke-width="2"/>
                <line x1="12" y1="3" x2="12" y2="15" stroke="currentColor" stroke-width="2"/>
              </svg>
              <span class="upload-text">Photo 3</span>
              <span class="upload-hint">Optional</span>
            </div>
          </div>
          <button type="button" class="remove-image" style="display: none;">×</button>
        </div>
      </div>

      <div class="upload-tips">
        <h4>Photo Guidelines:</h4>
        <ul>
          <li>At least one clear photo is required</li>
          <li>Maximum file size: 5MB per image</li>
          <li>Supported formats: JPG, PNG, WebP</li>
          <li>Show distinctive features, labels, or damage</li>
          <li>Multiple angles help with identification</li>
        </ul>
      </div>
      <div class="form-error" id="images-error"></div>
    </div>

    <!-- Current Status Section -->
    <div class="form-section">
      <h2 class="section-title">Current Status</h2>
      
      <div class="form-group">
        <label for="current-location" class="form-label">Where is the item now? *</label>
        <select id="current-location" name="current_location" class="form-select" required>
          <option value="">Select Current Location</option>
          <option value="with-me">I still have it with me</option>
          <option value="security-office">Handed to Security Office</option>
          <option value="lost-found-office">Handed to Lost & Found Office</option>
          <option value="department-office">Left at Department Office</option>
          <option value="library-counter">Left at Library Counter</option>
          <option value="other">Other (please specify)</option>
        </select>
        <div class="form-error" id="current-location-error"></div>
      </div>

      <!-- Other Location Details (shown when "other" is selected) -->
      <div class="form-group other-location-details" id="other-location-details" style="display: none;">
        <label for="other-location" class="form-label">Please specify where the item is currently located *</label>
        <input 
          type="text" 
          id="other-location" 
          name="other_location" 
          class="form-input" 
          placeholder="e.g., Front desk at main building, with John at reception"
          maxlength="200"
        >
      </div>

      <!-- Special Instructions -->
      <div class="form-group">
        <label for="special-instructions" class="form-label">Special Instructions</label>
        <textarea 
          id="special-instructions" 
          name="special_instructions" 
          class="form-textarea" 
          placeholder="Any special instructions for pickup, preferred contact times, or additional notes..."
          rows="3"
          maxlength="300"
        ></textarea>
      </div>
    </div>

    <!-- Form Actions -->
    <div class="form-actions">
      <button type="button" class="btn btn-secondary" onclick="history.back()">
        Cancel
      </button>
      <button type="submit" class="btn btn-primary" id="submit-btn">
        <svg class="btn-icon" viewBox="0 0 24 24" fill="none">
          <path d="M5 12l5 5L20 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Report Found Item
      </button>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loading-overlay" style="display: none;">
      <div class="loading-spinner">
        <div class="spinner"></div>
        <p>Submitting report...</p>
      </div>
    </div>
  </form>

</main>

<!-- JavaScript -->
<script src="/js/app/lost-and-found/report-found-item.js"></script>