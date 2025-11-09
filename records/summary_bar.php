<!-- ======= Document Summary Navbar ======= -->
<nav class="summary-nav d-flex align-items-center justify-content-evenly">
  
  <div class="summary-item" onclick="card_one()" style="--color:#007bff;">
    <i class="bx bx-file"></i>
    <div>
      <span id="load_new_received_count" class="summary-count">0</span>
      <small>Received</small>
    </div>
  </div>

  <div class="summary-item" onclick="card_two()" style="--color:#198754;">
    <i class="bx bx-archive-out"></i>
    <div>
      <span id="load_outgoing_count" class="summary-count">0</span>
      <small>Outgoing</small>
    </div>
  </div>

  <div class="summary-item" onclick="card_three()" style="--color:#dc3545;">
    <i class="bi bi-arrow-90deg-down"></i>
    <div>
      <span id="load_returned_count" class="summary-count">0</span>
      <small>Returned</small>
    </div>
  </div>

  <div class="summary-item" onclick="card_four()" style="--color:#ffc107;">
    <i class="bi bi-folder-check"></i>
    <div>
      <span id="load_acted_count" class="summary-count">0</span>
      <small>Acted</small>
    </div>
  </div>

  <div class="summary-item" onclick="card_five()" style="--color:#0dcaf0;">
    <i class="bi bi-person-walking"></i>
    <div>
      <span id="load_delivered_count" class="summary-count">0</span>
      <small>Delivered</small>
    </div>
  </div>

  <div class="summary-item" onclick="card_six()" style="--color:#20c997;">
    <i class="bi bi-list-columns"></i>
    <div>
      <span id="load_doc_count" class="summary-count">0</span>
      <small>All</small>
    </div>
  </div>

</nav>
<!-- ======= End Document Summary Navbar ======= -->
