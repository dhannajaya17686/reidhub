<link rel="stylesheet" href="/css/app/user/user-dashboard.css">

<!-- Main Dashboard Content -->
<main class="dashboard-main" role="main" aria-label="User Dashboard">

  <!-- Quick Actions Section -->
  <section class="quick-actions">
    <div class="action-card ask-card">
      <h3 class="card-title">Ask Anything</h3>
      <div class="user-info">
        <div class="user-avatar small">
          <span><?php echo strtoupper(substr($user['first_name'] ?? 'U', 0, 1)); ?></span>
        </div>
        <span class="user-name"><?php echo htmlspecialchars($user['first_name'] ?? 'User'); ?> <?php echo htmlspecialchars($user['last_name'] ?? ''); ?></span>
      </div>
      <p class="card-description">Ask others what you need to know...</p>
      <button class="action-btn primary-btn" onclick="navigateTo('/community/ask')">Publish</button>
    </div>

    <div class="action-card answer-card">
      <h3 class="card-title">Answer Question</h3>
      <div class="user-info">
        <div class="user-avatar small">
          <span>D</span>
        </div>
        <span class="user-name">Dhananjaya Mudalige</span>
      </div>
      <p class="card-description">Send message from a tonic stream</p>
      <div class="question-text">
        Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text...
      </div>
      <div class="voting-section">
        <span class="vote-count">01 Votes</span>
        <span class="answer-count">02 Answers</span>
      </div>
      <button class="action-btn secondary-btn" onclick="navigateTo('/community/questions/123')">Answer</button>
    </div>

    <div class="action-card blog-card">
      <h3 class="card-title">Featured Blogs</h3>
      <div class="user-info">
        <div class="user-avatar small">
          <span>D</span>
        </div>
        <span class="user-name">Dhananjaya Mudalige</span>
      </div>
      
      <!-- Blog Image -->
      <div class="blog-image">
        <img src="https://via.placeholder.com/300x150/10B981/ffffff?text=Sustainable+Campus" alt="Sustainable Campus Living" loading="lazy">
      </div>
      
      <h4 class="blog-title">The Future of Sustainable Campus Living</h4>
      <p class="blog-excerpt">
        At UCSC, we're committed to fostering a sustainable campus environment. This blog post explores our latest initiatives, including solar energy projects, waste reduction programs, and green building designs. Learn how we're integrating sustainability into every aspect of campus life and our students' journey.
      </p>
      <button class="see-more-btn" onclick="navigateTo('/community/blogs/sustainable-campus')">See More...</button>
    </div>
  </section>

  <!-- Two Column Layout -->
  <div class="dashboard-grid">
    
    <!-- Left Column -->
    <div class="left-column">
      
      <!-- Featured Posts -->
      <section class="dashboard-section featured-posts">
        <h2 class="section-title">Featured Posts</h2>
        
        <div class="post-card">
          <div class="post-header">
            <div class="chapter-badge">ACM Student Chapter</div>
            <div class="post-date">2 days ago</div>
          </div>
          <h3 class="post-title">The Time is Running Out!</h3>
          
          <!-- Event Poster Image -->
          <div class="event-poster-image">
            <img src="https://via.placeholder.com/400x250/8B5CF6/ffffff?text=Qiskit+Fall+Fest+2025" alt="Qiskit Fall Fest 2025" loading="lazy">
            <div class="poster-overlay">
              <div class="event-year">2025</div>
              <h4 class="event-name">Qiskit Fall Fest 2025</h4>
              <div class="event-logo">🎯</div>
            </div>
          </div>
          
          <p class="post-description">
            We're excited to announce that in honor of the International Year of Quantum, the 
            <strong>UCSC ACM Student Chapter</strong> will be hosting a Qiskit Fall Fest 2025 event this fall, 
            supported by <strong>IBM Quantum</strong>. Qiskit Fall Fest is a student-led, worldwide series 
            of events that brings people together to explore and learn about quantum computing.
          </p>
          
          <div class="post-actions">
            <button class="see-more-btn" onclick="navigateTo('/community/posts/qiskit-fall-fest-2025')">See More...</button>
            <div class="post-stats">
              <span class="stat-item">👍 24</span>
              <span class="stat-item">💬 8</span>
              <span class="stat-item">📤 5</span>
            </div>
          </div>
        </div>

        <!-- Additional Featured Post -->
        <div class="post-card">
          <div class="post-header">
            <div class="chapter-badge">UCSC Robotics Club</div>
            <div class="post-date">5 days ago</div>
          </div>
          <h3 class="post-title">Robotics Workshop Series Begins!</h3>
          
          <div class="event-poster-image">
            <img src="https://via.placeholder.com/400x250/F59E0B/ffffff?text=Robotics+Workshop" alt="Robotics Workshop" loading="lazy">
            <div class="poster-overlay">
              <h4 class="event-name">Arduino & Sensors</h4>
              <div class="event-logo">🤖</div>
            </div>
          </div>
          
          <p class="post-description">
            Join us for an intensive robotics workshop series covering Arduino programming, sensor integration, 
            and autonomous robot development. Perfect for beginners and advanced students alike.
          </p>
          
          <div class="post-actions">
            <button class="see-more-btn" onclick="navigateTo('/community/posts/robotics-workshop')">See More...</button>
            <div class="post-stats">
              <span class="stat-item">👍 18</span>
              <span class="stat-item">💬 12</span>
              <span class="stat-item">📤 3</span>
            </div>
          </div>
        </div>
      </section>

      <!-- Recent Blogs Section -->
      <section class="dashboard-section recent-blogs">
        <h2 class="section-title">Recent Blogs</h2>
        
        <div class="blog-grid">
          <div class="blog-card-small">
            <div class="blog-image-small">
              <img src="https://via.placeholder.com/150x100/3B82F6/ffffff?text=AI+Ethics" alt="AI Ethics" loading="lazy">
            </div>
            <div class="blog-content-small">
              <h4 class="blog-title-small">Ethics in AI Development</h4>
              <p class="blog-author">by Sarah Johnson</p>
              <p class="blog-date">Oct 20, 2024</p>
            </div>
          </div>

          <div class="blog-card-small">
            <div class="blog-image-small">
              <img src="https://via.placeholder.com/150x100/EF4444/ffffff?text=Cyber+Security" alt="Cybersecurity" loading="lazy">
            </div>
            <div class="blog-content-small">
              <h4 class="blog-title-small">Cybersecurity Best Practices</h4>
              <p class="blog-author">by Mike Chen</p>
              <p class="blog-date">Oct 18, 2024</p>
            </div>
          </div>

          <div class="blog-card-small">
            <div class="blog-image-small">
              <img src="https://via.placeholder.com/150x100/10B981/ffffff?text=Web+Dev" alt="Web Development" loading="lazy">
            </div>
            <div class="blog-content-small">
              <h4 class="blog-title-small">Modern Web Development</h4>
              <p class="blog-author">by Alex Kumar</p>
              <p class="blog-date">Oct 15, 2024</p>
            </div>
          </div>
        </div>
      </section>

      <!-- Lost Items -->
      <section class="dashboard-section lost-items">
        <h2 class="section-title">Recent Lost Items</h2>
        
        <div class="lost-item-card">
          <div class="item-image">
            <img src="https://via.placeholder.com/80x80/8B5A3C/ffffff?text=BAG" alt="Lost Backpack" loading="lazy">
          </div>
          <div class="item-info">
            <h4 class="item-title">Brown Leather Backpack</h4>
            <p class="item-description">
              I lost my brown color backpack near the left corner in Bawana. It was in the corner table chair. 
              If you found this bag please let me know.
            </p>
            <div class="item-details">
              <span class="item-location">📍 Last seen at: Bawana</span>
              <span class="item-date">🕒 Lost: 22/10/23 08:00 AM</span>
            </div>
          </div>
          <button class="contact-btn" onclick="navigateTo('/lost-found/item/123')">Contact</button>
        </div>
        
        <button class="see-more-btn" onclick="navigateTo('/lost-found')">See More Lost Items...</button>
      </section>
    </div>

    <!-- Right Column -->
    <div class="right-column">
      
      <!-- Upcoming Events -->
      <section class="dashboard-section upcoming-events">
        <h2 class="section-title">Upcoming Events</h2>
        
        <div class="events-list">
          <div class="event-item" onclick="navigateTo('/events/programming-10')">
            <div class="event-date">
              <span class="date-number">07</span>
              <span class="date-text">Nov Mon</span>
            </div>
            <div class="event-details">
              <span class="event-time">02:30-04:30 PM</span>
              <span class="event-name">Programming 1.0</span>
              <span class="event-location">Computer Lab A</span>
            </div>
            <div class="event-status attending">Attending</div>
          </div>
          
          <div class="event-item" onclick="navigateTo('/events/firmware-intro')">
            <div class="event-date">
              <span class="date-number">08</span>
              <span class="date-text">Nov Tue</span>
            </div>
            <div class="event-details">
              <span class="event-time">01:30-03:00 PM</span>
              <span class="event-name">Firmware Intro Session</span>
              <span class="event-location">Lab B</span>
            </div>
            <div class="event-status interested">Interested</div>
          </div>
          
          <div class="event-item" onclick="navigateTo('/events/acm-intro')">
            <div class="event-date">
              <span class="date-number">09</span>
              <span class="date-text">Nov Wed</span>
            </div>
            <div class="event-details">
              <span class="event-time">05:30-07:00 PM</span>
              <span class="event-name">ACM Introductory Session</span>
              <span class="event-location">Auditorium</span>
            </div>
            <div class="event-status">Available</div>
          </div>
          
          <div class="event-item all-day">
            <div class="event-date">
              <span class="date-number">10</span>
              <span class="date-text">Nov Thu</span>
            </div>
            <div class="event-details">
              <span class="event-time">All Day</span>
              <span class="event-name">Registration Deadline</span>
              <span class="event-location">Online</span>
            </div>
            <div class="event-status deadline">Deadline</div>
          </div>
        </div>
        
        <button class="see-more-btn" onclick="navigateTo('/events')">View All Events...</button>
      </section>

      <!-- Order Summary -->
      <section class="dashboard-section order-summary">
        <h2 class="section-title">Recent Orders</h2>
        
        <div class="order-card" onclick="navigateTo('/marketplace/orders/27648373830')">
          <div class="order-status shipping">Yet To Ship</div>
          <div class="order-content">
            <div class="product-image">
              <img src="https://via.placeholder.com/60x60/1D4ED8/ffffff?text=UCSC" alt="UCSC T-Shirt" loading="lazy">
            </div>
            <div class="order-info">
              <h4 class="product-name">UCSC T-Shirt</h4>
              <div class="product-price">Rs. 4,000</div>
              <div class="order-details">
                <span class="order-condition">Condition: Yet to ship</span>
                <span class="order-id">Order ID: #27648373830</span>
              </div>
            </div>
          </div>
          <div class="order-actions">
            <button class="track-btn">Track Order</button>
          </div>
        </div>

        <div class="order-card" onclick="navigateTo('/marketplace/orders/27648373829')">
          <div class="order-status delivered">Delivered</div>
          <div class="order-content">
            <div class="product-image">
              <img src="https://via.placeholder.com/60x60/10B981/ffffff?text=Book" alt="CS Textbook" loading="lazy">
            </div>
            <div class="order-info">
              <h4 class="product-name">Data Structures Textbook</h4>
              <div class="product-price">Rs. 2,500</div>
              <div class="order-details">
                <span class="order-condition">Delivered on Oct 20</span>
                <span class="order-id">Order ID: #27648373829</span>
              </div>
            </div>
          </div>
          <div class="order-actions">
            <button class="review-btn">Leave Review</button>
          </div>
        </div>
        
        <button class="see-more-btn" onclick="navigateTo('/marketplace/my-orders')">View All Orders...</button>
      </section>

      <!-- Quick Stats -->
      <section class="dashboard-section quick-stats">
        <h2 class="section-title">Your Activity</h2>
        
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-icon">📝</div>
            <div class="stat-value">12</div>
            <div class="stat-label">Questions Asked</div>
          </div>
          
          <div class="stat-card">
            <div class="stat-icon">💡</div>
            <div class="stat-value">8</div>
            <div class="stat-label">Answers Given</div>
          </div>
          
          <div class="stat-card">
            <div class="stat-icon">🛒</div>
            <div class="stat-value">5</div>
            <div class="stat-label">Orders Placed</div>
          </div>
          
          <div class="stat-card">
            <div class="stat-icon">📚</div>
            <div class="stat-value">3</div>
            <div class="stat-label">Blogs Written</div>
          </div>
        </div>
      </section>
    </div>
  </div>
</main>

<!-- Minimal JavaScript -->
<script src="/js/app/user-dashboard.js"></script>