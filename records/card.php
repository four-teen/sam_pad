<style>
  /* keep equal heights (from earlier) */
  .row.g-2 > [class*='col-'] { display:flex; align-items:stretch; }
  .info-card { flex:1; }
  .info-card .card-body { display:flex; flex-direction:column; justify-content:space-between; height:100%; }

  /* 1) Reserve the same space for titles (up to two lines) so the bottom row starts at the same height */
  .info-card .card-title {
    min-height: 2.6em;        /* ~2 lines reserve; adjust if your font-size differs */
    display: -webkit-box;
    -webkit-line-clamp: 2;     /* allow up to two lines */
    -webkit-box-orient: vertical;
    overflow: hidden;
    margin-bottom: .5rem;
  }

  /* 2) Lock the metrics row into two columns: fixed icon box + right-aligned numbers */
  .info-card .d-flex.align-items-center {
    display: grid !important;
    grid-template-columns: 48px 1fr;  /* fixed icon column */
    align-items: center;
    column-gap: 10px;
    width: 100%;
  }

  /* 3) Normalize icon box so all icons occupy identical space */
  .info-card .card-icon {
    width: 48px;
    height: 48px;
    display: grid;
    place-items: center;
  }
  .info-card .card-icon i {
    font-size: 1.8rem; /* unify bx/bi sizes */
    line-height: 1;
  }

  /* 4) Right-align the count block for consistent alignment */
  .info-card .d-flex.align-items-center > div:last-child {
    text-align: right;
  }
  .info-card h3.mb-0 { line-height: 1; margin: 0; }

  /* Optional: tighter layout on small screens but still aligned */
  @media (max-width: 576px) {
    .info-card .card-icon { width: 42px; height: 42px; }
    .info-card .d-flex.align-items-center { grid-template-columns: 42px 1fr; }
  }
</style>

<div class="row g-2">

  <!-- Manage Request -->
  <div class="col-lg-2 col-md-6">
    <div class="card info-card border-0 shadow-sm" style="--start-color:#007bff;--end-color:#17a2b8;"  onclick="card_one()">
      <div class="card-body">
        <h5 class="card-title">Received <span class="text-muted">| records</span></h5>
        <div class="d-flex align-items-center">
          <div class="card-icon">
            <i class="bx bx-file"></i>
          </div>
          <div>
            <h3 id="load_new_received_count" class="mb-0">0</h3>
            <small class="text-muted">document</small>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- OUTGOING -->
  <div class="col-lg-2 col-md-6">
    <div class="card info-card border-0 shadow-sm" style="--start-color:#198754;--end-color:#20c997;" onclick="card_two()">
      <div class="card-body">
        <h5 class="card-title">Outgoing <span class="text-muted">| Documents</span></h5>
        <div class="d-flex align-items-center">
          <div class="card-icon">
            <i class='bx bx-archive-out'></i>
          </div>
          <div>
            <h3 id="load_outgoing_count" class="mb-0">0</h3>
            <small class="text-muted">Need Actions</small>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Returned -->
<div class="col-lg-2 col-md-6">
  <div class="card info-card border-0 shadow-sm" style="--start-color:#dc3545;--end-color:#ff4136;" onclick="card_three()">
    <div class="card-body">
      <h5 class="card-title">Returned <span class="text-muted">| Documents</span></h5>
      <div class="d-flex align-items-center">
        <div class="card-icon">
          <i class="bi bi-arrow-90deg-down"></i>
        </div>
        <div>
          <h3 id="load_statistics" class="mb-0">0</h3>
          <small class="text-muted">records</small>
        </div>
      </div>
    </div>
  </div>
</div>

  <!-- ACTED -->
  <div class="col-lg-2 col-md-6">
    <div class="card info-card border-0 shadow-sm" style="--start-color:#ffc107;--end-color:#ffb347;" onclick="card_four()">
      <div class="card-body">
        <h5 class="card-title">Acted <span class="text-muted">| Records</span></h5>
        <div class="d-flex align-items-center">
          <div class="card-icon">
            <i class="bi bi-folder-check"></i>
          </div>
          <div>
            <h3 id="load_acted_count" class="mb-0">0</h3>
            <small class="text-muted">documents</small>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-2 col-md-6">
    <div class="card info-card border-0 shadow-sm" style="--start-color:#007bff;--end-color:#17a2b8;"  onclick="card_five()">
      <div class="card-body">
        <h5 class="card-title">Delivered <span class="text-muted">| records</span></h5>
        <div class="d-flex align-items-center">
          <div class="card-icon">
            <i class="bi bi-person-walking"></i>
          </div>
          <div>
            <h3 id="load_delivered_count" class="mb-0">0</h3>
            <small class="text-muted">document</small>
          </div>
        </div>
      </div>
    </div>
  </div> 


  <!-- ALL RECORDS -->
  <div class="col-lg-2 col-md-6">
    <div class="card info-card border-0 shadow-sm" style="--start-color:#198754;--end-color:#20c997;" onclick="card_six()">
      <div class="card-body">
        <h5 class="card-title">All <span class="text-muted">| Timeline</span></h5>
        <div class="d-flex align-items-center">
          <div class="card-icon">
            <i class="bi bi-list-columns"></i>
          </div>
          <div>
            <h3 id="load_doc_count" class="mb-0">0</h3>
            <small class="text-muted">Documents</small>
          </div>
        </div>
      </div>
    </div>
  </div>   

</div>