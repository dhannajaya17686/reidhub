<link rel="stylesheet" href="/css/app/user/lost-and-found/report-lost-item.css">

<!-- Main Report Lost Item Form -->
<main class="report-lost-main" role="main" aria-label="Report Lost Item Form">
  
  <!-- Page Header -->
  <div class="page-header">
    <div class="header-content">
      <h1 class="page-title">Report Lost Item</h1>
      <p class="page-subtitle">Help us help you find your lost item by providing detailed information below.</p>
    </div>
  </div>

  <!-- Report Lost Item Form -->
  <form class="report-lost-form" id="report-lost-form" method="POST" action="/dashboard/lost-found/report" enctype="multipart/form-data">
    
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
        <label for="description" class="form-label">Description (How you lost the item) *</label>
        <textarea 
          id="description" 
          name="description" 
          class="form-textarea" 
          placeholder="Describe your item in detail and explain how/where you lost it. Include color, brand, distinctive features..."
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
      <h2 class="section-title">Location & Time Details</h2>
      
      <div class="form-grid">
        <!-- Last Known Location -->
        <div class="form-group">
          <label for="location" class="form-label">Last Known Location *</label>
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
          <label for="specific-area" class="form-label">Specific Area</label>
          <input 
            type="text" 
            id="specific-area" 
            name="specific_area" 
            class="form-input" 
            placeholder="e.g., 2nd floor, near entrance, table 5" 
            maxlength="100"
          >
        </div>
      </div>

      <div class="form-grid">
        <!-- Date Lost -->
        <div class="form-group">
          <label for="date-lost" class="form-label">Date Lost *</label>
          <input 
            type="date" 
            id="date-lost" 
            name="date_lost" 
            class="form-input" 
            required
            max="<?php echo date('Y-m-d'); ?>"
          >
          <div class="form-error" id="date-lost-error"></div>
        </div>

        <!-- Time Lost -->
        <div class="form-group">
          <label for="time-lost" class="form-label">Approximate Time Lost</label>
          <input 
            type="time" 
            id="time-lost" 
            name="time_lost" 
            class="form-input"
          >
        </div>
      </div>
    </div>

    <!-- Contact Information Section -->
    <div class="form-section">
      <h2 class="section-title">Contact Information</h2>
      <p class="section-subtitle">We'll use this information to contact you if someone finds your item.</p>
      
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
          placeholder="e.g., WhatsApp, Telegram, Facebook profile"
          maxlength="100"
        >
      </div>
    </div>

    <!-- Priority Level Section -->
    <div class="form-section">
      <h2 class="section-title">Priority Level</h2>
      <p class="section-subtitle">Help us understand how urgent this is for you.</p>
      
      <div class="form-group">
        <div class="priority-group">
          <label class="priority-option low-priority">
            <input type="radio" name="priority" value="low" required>
            <span class="priority-custom"></span>
            <div class="priority-content">
              <span class="priority-title">Low Priority</span>
              <span class="priority-description">Not urgent, replaceable item</span>
            </div>
            <div class="priority-icon">📋</div>
          </label>
          
          <label class="priority-option medium-priority">
            <input type="radio" name="priority" value="medium" required>
            <span class="priority-custom"></span>
            <div class="priority-content">
              <span class="priority-title">Medium Priority</span>
              <span class="priority-description">Important but not critical</span>
            </div>
            <div class="priority-icon">⚠️</div>
          </label>
          
          <label class="priority-option high-priority">
            <input type="radio" name="priority" value="high" required>
            <span class="priority-custom"></span>
            <div class="priority-content">
              <span class="priority-title">High Priority</span>
              <span class="priority-description">Very important, irreplaceable or urgent</span>
            </div>
            <div class="priority-icon">🚨</div>
          </label>
        </div>
        <div class="form-error" id="priority-error"></div>
      </div>
    </div>

    <!-- Images Section -->
    <div class="form-section">
      <h2 class="section-title">Upload Photos</h2>
      <p class="section-subtitle">Upload photos of your item (if you have any). This will help others identify it.</p>
      
      <div class="image-upload-container">
        <!-- Main Image -->
        <div class="image-slot main-image" data-slot="0">
          <input type="file" id="image-0" name="images[]" accept="image/*" class="image-input" hidden>
          <div class="image-preview" id="preview-0">
            <div class="upload-placeholder">
              <svg class="upload-icon" viewBox="0 0 24 24" fill="none">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" stroke="currentColor" stroke-width="2"/>
                <polyline points="17,8 12,3 7,8" stroke="currentColor" stroke-width="2"/>
                <line x1="12" y1="3" x2="12" y2="15" stroke="currentColor" stroke-width="2"/>
              </svg>
              <span class="upload-text">Main Photo</span>
              <span class="upload-hint">Click to upload (Optional)</span>
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
          <li>Maximum file size: 5MB per image</li>
          <li>Supported formats: JPG, PNG, WebP</li>
          <li>Use clear photos that show distinctive features</li>
          <li>Multiple angles help with identification</li>
        </ul>
      </div>
    </div>

    <!-- Additional Information Section -->
    <div class="form-section">
      <h2 class="section-title">Additional Information</h2>
      
      <!-- Reward Offered -->
      <div class="form-group">
        <label class="checkbox-wrapper">
          <input type="checkbox" id="reward-offered" name="reward_offered" value="1">
          <span class="checkbox-custom"></span>
          <span class="checkbox-label">I'm offering a reward for finding this item</span>
        </label>
      </div>

      <!-- Reward Details (shown when checkbox is checked) -->
      <div class="reward-details" id="reward-details" style="display: none;">
        <div class="form-group">
          <label for="reward-amount" class="form-label">Reward Amount (Rs.)</label>
          <div class="price-input">
            <span class="price-prefix">Rs.</span>
            <input 
              type="number" 
              id="reward-amount" 
              name="reward_amount" 
              class="form-input price-field" 
              placeholder="1000" 
              min="0"
              max="100000"
              step="100"
            >
          </div>
        </div>
        
        <div class="form-group">
          <label for="reward-details-text" class="form-label">Reward Details</label>
          <textarea 
            id="reward-details-text" 
            name="reward_details" 
            class="form-textarea" 
            placeholder="Describe the reward terms, conditions, or additional incentives..."
            rows="2"
            maxlength="200"
          ></textarea>
        </div>
      </div>

      <!-- Special Instructions -->
      <div class="form-group">
        <label for="special-instructions" class="form-label">Special Instructions</label>
        <textarea 
          id="special-instructions" 
          name="special_instructions" 
          class="form-textarea" 
          placeholder="Any special handling instructions, preferred contact times, or additional notes..."
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
        Report Lost Item
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
<script src="/js/app/lost-and-found/report-lost-item.js"></script>