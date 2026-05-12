<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Add New Invoice</title>

  <!-- your header includes (bootstrap CSS, fonts, etc.) -->
  <?php include 'includes/header_links.php'; ?>

  <!-- Additional required CSS (safe to include even if header_links already has some) -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap"/>
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css"/>

  <!-- page-specific CSS -->
  <style>
    /* ===========================
       Basic page / typography tweaks
       (kept small and compatible with your crm-style)
       =========================== */
    body { font-family: "Inter", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial; }

    /* ensure table dropdown can overflow parent */
    .table-responsive { overflow: visible !important; }

    /* ------------------ Item table widths ------------------ */
    .item-table th:nth-child(1),
    .item-table td:nth-child(1) { width: 42% !important; }
    .item-table th:nth-child(2),
    .item-table td:nth-child(2) { width: 10% !important; }
    .item-table th:nth-child(3),
    .item-table td:nth-child(3) { width: 15% !important; }
    .item-table th:nth-child(4),
    .item-table td:nth-child(4) { width: 10% !important; }
    .item-table th:nth-child(5),
    .item-table td:nth-child(5) { width: 15% !important; }
    .item-table th:nth-child(6),
    .item-table td:nth-child(6) { width: 8% !important; }

    /* ------------------ Item search dropdown (quotation style) ------------------ */
    .item-search-container { position: relative; }
    .item-input-wrapper { position: relative; }
    .item-search {
      z-index: 3;
      position: relative;
      background: #fff;
    }

    .item-dropdown {
      position: absolute;
      left: 0;
      right: 0;
      top: calc(100% + 8px);
      background: #fff;
      border: 1px solid #e6e8ee;
      border-radius: 8px;
      box-shadow: 0 10px 30px rgba(20,25,70,0.06);
      max-height: 260px;
      overflow-y: auto;
      display: none;
      z-index: 99999; /* high so it floats above everything */
    }

    .item-option {
      padding: 10px 12px;
      cursor: pointer;
      font-size: 14px;
      color: #1e2446;
      border-bottom: 1px solid rgba(15,23,42,0.03);
    }
    .item-option:last-child { border-bottom: none; }
    .item-option:hover, .item-option.active {
      background: #f2f7ff;
      color: #0735d1;
    }

    /* caret icon inside input */
    .item-dropdown-icon {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 12px;
      color: #6c757d;
      cursor: pointer;
      z-index: 4;
    }

    /* small input styling consistent with crm */
    .input-editable { padding: 8px 10px; border-radius: 6px; border: 1px solid #d7d7df; background: #fff; }

    /* terms & payment side-by-side */
    .crm-split-row { display:flex; gap:20px; align-items:flex-start; margin-bottom:20px; }
    .terms-card { width:50%; }
    @media (max-width:768px) { .crm-split-row { flex-direction:column; } .terms-card { width:100%; } }

    /* Terms/payment inputs (make delete icon look like table delete icon) */
    .terms-point-row, .payment-point-row {
      display:flex; gap:8px; align-items:center; margin-bottom:10px;
    }
    .terms-point-row .form-control, .payment-point-row .form-control {
      flex:1;
      padding:8px 10px;
      border-radius:6px;
      border:1px solid #d7d7df;
    }
    .remove-term, .remove-payment { padding:6px 8px; }

    /* totals card */
    .totals-card { margin-top:10px; background:#fff; border-radius:8px; padding:12px; box-shadow:0 6px 18px rgba(30,34,95,0.04); border:1px solid rgba(30,34,95,0.03); }
    .totals-row, .totals-grand { display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; }
    .totals-grand { font-weight:600; font-size:1.02rem; }

    /* Adjust table inputs */
    .item-table input { height:36px; font-size:0.95rem; padding:6px 10px; }
    .readonly-cell { font-weight:600; }

    /* small button niceties */
    .btn-outline-secondary { border-radius:6px; }
    .btn { border-radius:6px; }

  </style>
</head>
<body>

  <?php include 'includes/sidebar.php'; ?>
  <?php include 'includes/header.php'; ?>

  <div class="content">
    <div class="container-crm">

      <div class="crm-header">
        <div class="crm-title"><i class="bi bi-receipt"></i> Create Invoice</div>
      </div>

      <form id="invoiceForm" autocomplete="off">

        <!-- ******** Customer & Invoice Info (UNCHANGED) ******** -->
        <div class="crm-card">
          <div class="crm-section-head"><i class="bi bi-person"></i> Customer & Invoice Info</div>
          <div class="row g-3">
            <div class="col-md-6 customer-select-group">
              <label class="form-label">Customer Name <span class="text-danger">*</span></label>
              <i class="bi bi-search"></i>
              <select class="form-select" id="customerName" style="width: 100%;">
                <option selected disabled value="">Select Customer</option>
                <option value="new">+ Add New Customer</option>
              </select>
            </div>

            <div class="col-md-3 input-icon">
              <label class="form-label">Invoice Date <span class="text-danger">*</span></label>
              <i class="bi bi-calendar3"></i>
              <input type="text" class="form-control datepicker" id="invoiceDate" required />
            </div>

            <div class="col-md-3 input-icon">
              <label class="form-label">Due Date <span class="text-danger">*</span></label>
              <i class="bi bi-calendar3"></i>
              <input type="text" class="form-control datepicker" id="dueDate" required />
            </div>

            <div class="col-md-4 input-invoice-group" style="position: relative;">
              <label class="form-label">Invoice Number</label>
              <i class="bi bi-hash"></i>
              <input type="text" class="form-control" id="invoiceNumber" readonly placeholder="Auto-generated" />
              <button class="gear-icon-btn" type="button" data-bs-toggle="modal" data-bs-target="#invoiceNumberPrefModal">
                <i class="bi bi-gear-fill"></i>
              </button>
            </div>
          </div>
        </div>

        <!-- ******** Items Table ******** -->
        <div class="crm-table-section">
          <div class="crm-section-head"><i class="bi bi-box"></i> Items</div>

          <div class="table-responsive">
            <table class="table item-table align-middle" id="itemsTable">
              <thead>
                <tr>
                  <th><i class="bi bi-tag"></i> Item Name</th>
                  <th><i class="bi bi-stack"></i> Qty</th>
                  <th><i class="bi bi-currency-rupee"></i> Rate (₹)</th>
                  <th><i class="bi bi-percent"></i> Tax (%)</th>
                  <th><i class="bi bi-cash-stack"></i> Amount (₹)</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="item-search-container">
                    <div class="item-input-wrapper" style="position:relative;">
                      <input type="text" class="form-control item-search input-editable" placeholder="Search item..." autocomplete="off" />
                      <i class="bi bi-caret-down-fill item-dropdown-icon" title="Open list"></i>
                    </div>
                    <div class="item-dropdown" aria-hidden="true"></div>
                  </td>

                  <td><input type="number" class="form-control qty" value="1" min="1" /></td>
                  <td><input type="number" class="form-control rate" value="0" step="0.01" /></td>
                  <td><input type="number" class="form-control tax" value="0" min="0" max="100" /></td>
                  <td class="amount readonly-cell">₹0.00</td>
                  <td>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-item" title="Remove row">
                      <i class="bi bi-trash3"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <button type="button" class="btn btn-outline-primary mt-2" id="addItem"><i class="bi bi-plus-circle me-1"></i> Add Item</button>
        </div>

        <div class="row">
          <div class="col-md-6"></div>
          <div class="col-md-6">
               <!-- ******** Totals ******** -->
        <div class="totals-card">
          <div id="summaryRows">
            <div class="totals-row"><span>Subtotal:</span><span class="subtotal">₹0.00</span></div>
            <div class="totals-row">
              <span>Discount:</span>
              <span><input type="number" id="discountAmount" class="form-control d-inline-block" style="width:90px;" value="0" min="0"/></span>
            </div>
          </div>

          <div class="totals-grand"><span>Total:</span><span class="grand-total">₹0.00</span></div>
        </div>
          </div>
        </div>

        <!-- ******** Terms & Payment (side-by-side) ******** -->
        <div class="crm-split-row mb-3">
          <!-- Terms -->
          <div class="terms-card">
            <div class="terms-section-head"><i class="bi bi-file-text"></i> Terms & Conditions</div>

            <div id="termsList">
              <div class="terms-point-row">
                <input class="form-control terms-text" name="terms[]" placeholder="Enter term or condition" />
                <button type="button" class="btn btn-outline-danger btn-sm remove-term" title="Remove term"><i class="bi bi-trash3"></i></button>
              </div>
            </div>

            <button type="button" class="btn btn-outline-secondary btn-sm mt-3" id="addTermBtn"><i class="bi bi-plus"></i> Add More</button>
          </div>

          <!-- Payment Terms -->
          <div class="terms-card">
            <div class="terms-section-head"><i class="bi bi-cash-stack"></i> Payment Terms</div>

            <div id="paymentList">
              <div class="payment-point-row">
                <input class="form-control payment-text" name="payments[]" placeholder="Enter payment term" />
                <button type="button" class="btn btn-outline-danger btn-sm remove-payment" title="Remove payment term"><i class="bi bi-trash3"></i></button>
              </div>
            </div>

            <button type="button" class="btn btn-outline-secondary btn-sm mt-3" id="addPaymentBtn"><i class="bi bi-plus"></i> Add More</button>
          </div>
        </div>

     

        <div class="crm-action-bar mt-3">
          <button type="button" class="btn btn-outline-secondary"><i class="bi bi-save me-1"></i> Save as Draft</button>
          <button type="submit" class="btn btn-primary"><i class="bi bi-send-fill me-1"></i> Create Invoice</button>
        </div>

      </form>
    </div>
  </div>

  <!-- ----------------------- Modals (expected by project) ----------------------- -->
  <!-- Invoice Number Pref Modal -->
  <div class="modal fade" id="invoiceNumberPrefModal" tabindex="-1" aria-labelledby="invoiceNumberPrefModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width:660px;">
      <div class="modal-content border-0 shadow-lg rounded-3">
        <div class="modal-header bg-navy text-white py-3 px-4 rounded-top-3">
          <h6 class="modal-title fw-bold d-flex align-items-center gap-2 mb-0"><i class="bi bi-gear-fill"></i> Configure Invoice Number</h6>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <p class="text-muted mb-3">Your invoice numbers are set on auto-generate mode to save your time.<br>Are you sure about changing this setting?</p>
          <div class="form-check mb-3">
            <input class="form-check-input" type="radio" name="invNumPref" id="autoGenInvNum" checked />
            <label class="form-check-label fw-medium text-navy" for="autoGenInvNum">Continue auto-generating invoice numbers</label>
          </div>
          <div class="row g-3 mb-3">
            <div class="col-6 input-icon">
              <i class="bi bi-currency-rupee"></i>
              <label class="form-label fw-light">Prefix</label>
              <input type="text" class="form-control" id="invPrefix" value="INV-" maxlength="12" />
            </div>
            <div class="col-6 input-icon">
              <i class="bi bi-hash"></i>
              <label class="form-label fw-light">Next Number</label>
              <input type="text" class="form-control" id="invNextNumber" value="000001" maxlength="12" />
            </div>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="invNumPref" id="manualInvNum" />
            <label class="form-check-label fw-medium text-navy" for="manualInvNum">Enter invoice numbers manually</label>
          </div>
        </div>
        <div class="modal-footer border-0 pt-3 pb-4 px-4">
          <button type="button" class="btn btn-outline-navy px-4" data-bs-dismiss="modal">Cancel</button>
          <button type="button" id="saveInvNumberPref" class="btn btn-navy px-5">Save</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Add New Customer Modal -->
  <div class="modal fade" id="newCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="max-width:800px;">
      <div class="modal-content border-0 shadow-lg rounded-3">
        <div class="modal-header bg-navy text-white py-3 px-4 rounded-top-3">
          <h5 class="modal-title fw-bold d-flex align-items-center gap-2 mb-0"><i class="bi bi-person-plus-fill"></i> Add New Customer</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <form id="newCustomerForm" class="row g-3">
            <div class="col-12">
              <label class="form-label fw-semibold text-navy mb-1">Customer Type <span class="text-danger">*</span></label>
              <div class="d-flex gap-4">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="customerType" id="business" value="business" checked>
                  <label class="form-check-label d-flex align-items-center gap-2 fw-medium" for="business"><i class="bi bi-building text-navy"></i> Business</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="customerType" id="individual" value="individual">
                  <label class="form-check-label d-flex align-items-center gap-2 fw-medium" for="individual"><i class="bi bi-person text-navy"></i> Individual</label>
                </div>
              </div>
            </div>

            <div class="col-md-3 input-icon"><i class="bi bi-person-badge"></i><label class="form-label text-navy">Salutation</label>
              <select class="form-select" id="salutation"><option value="">Select</option><option>Mr.</option><option>Ms.</option><option>Mrs.</option><option>Dr.</option></select>
            </div>

            <div class="col-md-4 input-icon"><i class="bi bi-person"></i><label class="form-label text-navy">First Name <span class="text-danger">*</span></label><input type="text" id="firstName" class="form-control" required></div>
            <div class="col-md-5 input-icon"><i class="bi bi-person"></i><label class="form-label text-navy">Last Name <span class="text-danger">*</span></label><input type="text" id="lastName" class="form-control" required></div>
            <div class="col-md-6 input-icon"><i class="bi bi-building"></i><label class="form-label text-navy">Company Name</label><input type="text" id="companyName" class="form-control"></div>
            <div class="col-md-6 input-icon"><i class="bi bi-currency-exchange"></i><label class="form-label text-navy">Currency <span class="text-danger">*</span></label><select class="form-select" id="currencyType" required><option value="">Select</option><option>INR</option><option>USD</option><option>EUR</option></select></div>
            <div class="col-md-6 input-icon"><i class="bi bi-phone"></i><label class="form-label text-navy">Mobile <span class="text-danger">*</span></label><input type="tel" id="mobile" class="form-control" required></div>
            <div class="col-md-6 input-icon"><i class="bi bi-envelope"></i><label class="form-label text-navy">Email <span class="text-danger">*</span></label><input type="email" id="emailCustomer" class="form-control" required></div>
            <div class="col-md-6 input-icon"><i class="bi bi-card-text"></i><label class="form-label text-navy">PAN</label><input type="text" id="pan" class="form-control" maxlength="10"></div>
            <div class="col-md-6 input-icon"><i class="bi bi-file-earmark-text"></i><label class="form-label text-navy">GSTIN</label><input type="text" id="gst" class="form-control" maxlength="15"></div>

            <div class="col-12 mt-4">
              <div class="d-flex align-items-center gap-2 mb-3"><i class="bi bi-geo-alt-fill text-navy"></i><h6 class="mb-0 fw-bold text-navy">Billing Address <span class="text-danger">*</span></h6></div>
              <div class="row g-3">
                <div class="col-12 input-icon"><i class="bi bi-house-door"></i><label class="form-label text-navy">Address Line 1 <span class="text-danger">*</span></label><input type="text" id="billing_addr1" class="form-control" required></div>
                <div class="col-12 input-icon"><i class="bi bi-house"></i><label class="form-label text-navy">Address Line 2</label><input type="text" id="billing_addr2" class="form-control"></div>
                <div class="col-md-4 input-icon"><i class="bi bi-building"></i><label class="form-label text-navy">City <span class="text-danger">*</span></label><input type="text" id="billing_city" class="form-control" required></div>
                <div class="col-md-4 input-icon"><i class="bi bi-map"></i><label class="form-label text-navy">State <span class="text-danger">*</span></label><select class="form-select" id="billing_state" required><option value="">Select</option><option>Maharashtra</option><option>Karnataka</option><option>Delhi</option></select></div>
                <div class="col-md-4 input-icon"><i class="bi bi-pin-map"></i><label class="form-label text-navy">PIN Code <span class="text-danger">*</span></label><input type="text" id="billing_pin" class="form-control" maxlength="6" required></div>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer border-0 pt-3 pb-4 px-4">
          <button type="button" class="btn btn-outline-navy px-4" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" form="newCustomerForm" class="btn btn-navy px-5">Save Customer</button>
        </div>
      </div>
    </div>
  </div>

  <?php include 'includes/footer_links.php'; ?>

  <!-- ===========================
       Libraries (ensure these are available; duplicates okay)
       =========================== -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <!-- ===========================
       Page JS: dropdown, terms/payment, totals
       =========================== -->
  <script>
    $(document).ready(function () {

      /* ------------------ Datepicker (Indian format dd-mm-yy) ------------------ */
      if ($.fn.datepicker) {
        $('.datepicker').datepicker({
          dateFormat: 'dd-mm-yy',
          changeMonth: true,
          changeYear: true
        });
      }

      // set default dates in dd-mm-yyyy
      const t = new Date();
      $('#invoiceDate').val(("0"+t.getDate()).slice(-2)+"-"+("0"+(t.getMonth()+1)).slice(-2)+"-"+t.getFullYear());
      const d = new Date();
      d.setDate(d.getDate() + 30);
      $('#dueDate').val(("0"+d.getDate()).slice(-2)+"-"+("0"+(d.getMonth()+1)).slice(-2)+"-"+d.getFullYear());

      /* ------------------ select2 for Customer ------------------ */
      if ($.fn.select2) {
        $('#customerName').select2({ width: '100%', placeholder: 'Select or Add New' })
          .on('select2:select', function (e) {
            if (e.params.data.id === 'new') {
              new bootstrap.Modal(document.getElementById('newCustomerModal')).show();
              $(this).val(null).trigger('change');
            }
          });
      }

      /* ------------------ Item data (static) ------------------ */
      const itemData = [
        {name:"Wooden Chair", rate:900, tax:18},
        {name:"Glass Table", rate:3500, tax:12},
        {name:"Office Desk", rate:4200, tax:18},
        {name:"Sofa 3-Seater", rate:11000, tax:18},
        {name:"Bookshelf", rate:3200, tax:12},
        {name:"King Size Bed", rate:22499, tax:12},
        {name:"Dining Table (6-Seater)", rate:15999, tax:18}
      ];

      /* ---------- Generate dropdown HTML (quotation style) ---------- */
      function buildDropdownHtml() {
        let html = '';
        itemData.forEach(item => {
          html += `<div class="item-option" tabindex="0"
                    data-name="${escapeHtml(item.name)}"
                    data-rate="${item.rate}"
                    data-tax="${item.tax}">${escapeHtml(item.name)}</div>`;
        });
        return html;
      }

      function escapeHtml(str) {
        return String(str).replace(/[&<>"'`=\/]/g, function(s) {
          return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;','/':'&#47;','`':'&#96;'})[s];
        });
      }

      /* ---------- Show dropdown for an input (populate if empty) ---------- */
      function showDropdownFor(input) {
        const $td = $(input).closest('td');
        const $dropdown = $td.find('.item-dropdown');
        if ($dropdown.children().length === 0) {
          $dropdown.html(buildDropdownHtml());
        }
        $dropdown.show();
      }

      /* ---------- Filter dropdown items based on input text ---------- */
      function filterDropdown(input) {
        const term = $(input).val().toLowerCase();
        const $dropdown = $(input).closest('td').find('.item-dropdown');
        $dropdown.find('.item-option').each(function () {
          const txt = $(this).text().toLowerCase();
          $(this).toggle(txt.indexOf(term) !== -1);
        });
      }

      /* ---------- Events: input focus / click to open ---------- */
      $(document).on('focus click', '.item-search', function (e) {
        e.stopPropagation();
        showDropdownFor(this);
        filterDropdown(this);
      });

      $(document).on('input', '.item-search', function () {
        filterDropdown(this);
      });

      /* ---------- Caret icon toggles dropdown ---------- */
      $(document).on('click', '.item-dropdown-icon', function (e) {
        e.stopPropagation();
        const $td = $(this).closest('td');
        const $dropdown = $td.find('.item-dropdown');
        if ($dropdown.is(':visible')) {
          $dropdown.hide();
        } else {
          showDropdownFor($td.find('.item-search'));
        }
      });

      /* ---------- Click option: populate row and hide ---------- */
      $(document).on('click', '.item-option', function (e) {
        e.stopPropagation();
        const $opt = $(this);
        const name = $opt.data('name');
        const rate = $opt.data('rate');
        const tax = $opt.data('tax');

        const $td = $opt.closest('td');
        const $row = $td.closest('tr');

        $row.find('.item-search').val(name);
        $row.find('.rate').val(rate);
        $row.find('.tax').val(tax);

        $td.find('.item-dropdown').hide();
        calculateTotals();
      });

      /* ---------- keyboard navigation in dropdown ---------- */
      $(document).on('keydown', '.item-search', function (e) {
        const $dropdown = $(this).closest('td').find('.item-dropdown');
        // open dropdown on ArrowDown
        if (e.key === 'ArrowDown') {
          e.preventDefault();
          showDropdownFor(this);
          $dropdown.find('.item-option:visible').first().focus();
        }
      });

      // allow focusing options and Enter to select
      $(document).on('keydown', '.item-option', function (e) {
        if (e.key === 'Enter') {
          e.preventDefault();
          $(this).trigger('click');
        } else if (e.key === 'ArrowDown') {
          e.preventDefault();
          $(this).nextAll('.item-option:visible').first().focus();
        } else if (e.key === 'ArrowUp') {
          e.preventDefault();
          $(this).prevAll('.item-option:visible').first().focus();
        }
      });

      /* ---------- Hide dropdown when clicking outside ---------- */
      $(document).on('click', function (e) {
        if (!$(e.target).closest('.item-search-container').length) {
          $('.item-dropdown').hide();
        }
      });

      /* ---------- Add new row (keeps dropdown behavior) ---------- */
      $('#addItem').on('click', function () {
        const row = `
          <tr>
            <td class="item-search-container">
              <div class="item-input-wrapper" style="position:relative;">
                <input type="text" class="form-control item-search input-editable" placeholder="Search item..." autocomplete="off" />
                <i class="bi bi-caret-down-fill item-dropdown-icon" title="Open list"></i>
              </div>
              <div class="item-dropdown" aria-hidden="true"></div>
            </td>
            <td><input type="number" class="form-control qty" value="1" min="1" /></td>
            <td><input type="number" class="form-control rate" value="0" step="0.01" /></td>
            <td><input type="number" class="form-control tax" value="0" min="0" max="100" /></td>
            <td class="amount readonly-cell">₹0.00</td>
            <td><button type="button" class="btn btn-sm btn-outline-danger remove-item" title="Remove row"><i class="bi bi-trash3"></i></button></td>
          </tr>
        `;
        $('#itemsTable tbody').append(row);
      });

      /* ---------- Remove row ---------- */
      $(document).on('click', '.remove-item', function () {
        $(this).closest('tr').remove();
        calculateTotals();
      });

      /* ---------- Terms & Payment add/remove (use same trash icon) ---------- */
      $('#addTermBtn').on('click', function () {
        $('#termsList').append(`
          <div class="terms-point-row">
            <input class="form-control terms-text" name="terms[]" placeholder="Enter term or condition" />
            <button type="button" class="btn btn-outline-danger btn-sm remove-term" title="Remove term"><i class="bi bi-trash3"></i></button>
          </div>`);
      });

      $(document).on('click', '.remove-term', function () {
        $(this).closest('.terms-point-row').remove();
      });

      $('#addPaymentBtn').on('click', function () {
        $('#paymentList').append(`
          <div class="payment-point-row">
            <input class="form-control payment-text" name="payments[]" placeholder="Enter payment term" />
            <button type="button" class="btn btn-outline-danger btn-sm remove-payment" title="Remove payment term"><i class="bi bi-trash3"></i></button>
          </div>`);
      });

      $(document).on('click', '.remove-payment', function () {
        $(this).closest('.payment-point-row').remove();
      });

      /* ---------- Totals calculation (qty/rate/tax/discount) ---------- */
      $(document).on('input', '.qty, .rate, .tax, #discountAmount', function () {
        calculateTotals();
      });

      function calculateTotals() {
        let subtotal = 0;
        let totalTax = 0;

        $('#itemsTable tbody tr').each(function () {
          const qty = parseFloat($(this).find('.qty').val()) || 0;
          const rate = parseFloat($(this).find('.rate').val()) || 0;
          const tax = parseFloat($(this).find('.tax').val()) || 0;

          const base = qty * rate;
          const taxAmt = base * (tax / 100);
          const total = base + taxAmt;

          $(this).find('.amount').text('₹' + total.toFixed(2));

          subtotal += base;
          totalTax += taxAmt;
        });

        $('.subtotal').text('₹' + subtotal.toFixed(2));

        const discount = parseFloat($('#discountAmount').val()) || 0;
        let grand = subtotal + totalTax - discount;
        if (grand < 0) grand = 0;
        $('.grand-total').text('₹' + grand.toFixed(2));
      }

      // initial calculation on load
      calculateTotals();

      /* ---------- Invoice number modal save ---------- */
      $('#saveInvNumberPref').on('click', function () {
        const isManual = $('#manualInvNum').is(':checked');
        const prefix = $('#invPrefix').val() || '';
        const next = $('#invNextNumber').val() || '';
        if (isManual) {
          $('#invoiceNumber').prop('readonly', false).val('');
        } else {
          $('#invoiceNumber').prop('readonly', true).val(prefix + next);
        }
        const modal = bootstrap.Modal.getInstance(document.getElementById('invoiceNumberPrefModal'));
        if (modal) modal.hide();
      });

      /* ---------- New Customer form (simple add to select2) ---------- */
      $('#newCustomerForm').on('submit', function (e) {
        e.preventDefault();
        const name = $('#firstName').val() + ( $('#lastName').val() ? ' ' + $('#lastName').val() : '' );
        const company = $('#companyName').val();
        const text = company ? (company + ' — ' + name) : name;
        const val = 'cust_' + Date.now();
        const newOpt = new Option(text, val, true, true);
        $('#customerName').append(newOpt).trigger('change');

        const modal = bootstrap.Modal.getInstance(document.getElementById('newCustomerModal'));
        if (modal) modal.hide();
        this.reset();
      });

    });
  </script>

</body>
</html>
