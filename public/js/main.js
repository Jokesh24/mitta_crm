//  <!-- Start JavaScript Section -->
/* Start Variable Initialization */
const toggleBtn = document.getElementById('toggleBtn');
const sidebar = document.querySelector('.sidebar');
const content = document.querySelector('.content');
const header = document.querySelector('.header');
const overlay = document.getElementById('overlay');
let isCollapsed = false;
/* End Variable Initialization */

/* Start Dropdown Toggle Functionality */
document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
  toggle.addEventListener('click', (e) => {
    e.preventDefault();
    e.stopPropagation(); // Prevent event bubbling to parent elements

    const submenuId = toggle.getAttribute('data-submenu');
    const submenu = document.getElementById(submenuId);
    const isOpen = submenu.classList.contains('show');

    // Get the parent container for sibling toggles (either sidebar-content or parent submenu)
    const parentContainer = toggle.closest('.submenu') || document.querySelector('.sidebar-content');

    // Close sibling submenus at the same level, but preserve parent submenus
    const siblingToggles = parentContainer.querySelectorAll(':scope > .dropdown-toggle, :scope > .submenu > .dropdown-toggle');
    siblingToggles.forEach(sibling => {
      if (sibling !== toggle) {
        const siblingSubmenuId = sibling.getAttribute('data-submenu');
        const siblingSubmenu = document.getElementById(siblingSubmenuId);
        if (siblingSubmenu) {
          siblingSubmenu.classList.remove('show');
          sibling.classList.remove('show');
        }
      }
    });

    // Toggle the current submenu
    submenu.classList.toggle('show');
    toggle.classList.toggle('show');

    // On mobile, ensure sidebar is visible when opening a submenu
    if (window.innerWidth <= 768) {
      sidebar.classList.remove('hidden');
      content.classList.remove('full-width');
      header.classList.remove('full-width');
      overlay.classList.add('active');
    }
  });
});
/* End Dropdown Toggle Functionality */

/* Start Sidebar Toggle Functionality */
toggleBtn.addEventListener('click', () => {
  if (window.innerWidth > 768) {
    // Desktop: Toggle between expanded and collapsed
    isCollapsed = !isCollapsed;
    sidebar.classList.toggle('collapsed', isCollapsed);
    content.classList.toggle('collapsed', isCollapsed);
    header.classList.toggle('collapsed', isCollapsed);
  } else {
    // Mobile: Toggle between hidden and expanded
    sidebar.classList.toggle('hidden');
    content.classList.toggle('full-width');
    header.classList.toggle('full-width');
    overlay.classList.toggle('active');
  }
});
/* End Sidebar Toggle Functionality */

/* Start Overlay Click Handler */
overlay.addEventListener('click', () => {
  sidebar.classList.add('hidden');
  content.classList.add('full-width');
  header.classList.add('full-width');
  overlay.classList.remove('active');
  // Close all submenus when closing sidebar
  document.querySelectorAll('.submenu').forEach(submenu => {
    submenu.classList.remove('show');
  });
  document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
    toggle.classList.remove('show');
  });
});
/* End Overlay Click Handler */

/* Start Window Resize Handler */
window.addEventListener('resize', () => {
  if (window.innerWidth <= 768) {
    sidebar.classList.add('hidden');
    content.classList.add('full-width');
    header.classList.add('full-width');
    overlay.classList.remove('active');
    isCollapsed = false;
    sidebar.classList.remove('collapsed');
    content.classList.remove('collapsed');
    header.classList.remove('collapsed');
  } else {
    sidebar.classList.remove('hidden');
    content.classList.remove('full-width');
    header.classList.remove('full-width');
    overlay.classList.remove('active');
    if (isCollapsed) {
      sidebar.classList.add('collapsed');
      content.classList.add('collapsed');
      header.classList.add('collapsed');
    } else {
      header.classList.remove('collapsed');
    }
  }
});
/* End Window Resize Handler */

/* Start Initial State Setup */
if (window.innerWidth <= 768) {
  sidebar.classList.add('hidden');
  content.classList.add('full-width');
  header.classList.add('full-width');
}
/* End Initial State Setup */

/* Start Icon Loading Debug */
document.querySelectorAll('.bi').forEach(icon => {
  if (!window.getComputedStyle(icon, ':before').content) {
    console.warn(`Icon ${icon.className} failed to load.`);
  }
});
/* End Icon Loading Debug */
// <!-- End JavaScript Section -->

//=======================
//  AGENT FILE UPLOAD & MODEL JS
// =======================
document.addEventListener('DOMContentLoaded', function () {
  // Initialize Flatpickr for date inputs if they exist
  if (document.querySelector('#dob')) {
    flatpickr("#dob", {
      dateFormat: "d-m-Y",
      altInput: true,
      altFormat: "d-m-Y",
      allowInput: true
    });
  }
  if (document.querySelector('#join_date')) {
    flatpickr("#join_date", {
      dateFormat: "d-m-Y",
      altInput: true,
      altFormat: "d-m-Y",
      allowInput: true
    });
  }

  // File preview function
  function setupFilePreview(inputId, previewId, isImageOnly = false) {
    const input = document.getElementById(inputId);
    if (!input) return;
    input.addEventListener('change', function (e) {
      const preview = document.getElementById(previewId);
      preview.innerHTML = '';
      Array.from(e.target.files).forEach(file => {
        const wrapper = document.createElement('div');
        wrapper.className = 'preview-item';

        let element;
        if (file.type.includes('image')) {
          element = document.createElement('img');
          element.src = URL.createObjectURL(file);
          element.addEventListener('click', () => {
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            modalImage.src = element.src;
            modal.style.display = 'flex';
          });
        } else if (!isImageOnly && file.type.includes('pdf')) {
          element = document.createElement('iframe');
          element.src = URL.createObjectURL(file);
          element.style.width = '100px';
          element.style.height = '70px';
        } else {
          return; // Skip unsupported file types
        }

        const removeBtn = document.createElement('button');
        removeBtn.innerHTML = '&times;';
        removeBtn.className = 'remove-btn';
        removeBtn.onclick = () => wrapper.remove();

        wrapper.appendChild(element);
        wrapper.appendChild(removeBtn);
        preview.appendChild(wrapper);
      });
    });
  }

  // Initialize previews for each file input
  setupFilePreview('agent_photo', 'agent_photo_preview', true);
  setupFilePreview('agent_applicationcopy', 'agent_applicationcopy_preview');
  setupFilePreview('aadhar_copy', 'aadhar_copy_preview');
  setupFilePreview('pan_copy', 'pan_copy_preview');
  setupFilePreview('bankpassbook_copy', 'bankpassbook_copy_preview');

  // Modal close functionality
  const modalClose = document.querySelector('.modal-close');
  if (modalClose) {
    modalClose.addEventListener('click', () => {
      document.getElementById('imageModal').style.display = 'none';
    });
  }

  // Close modal when clicking outside the image
  const imageModal = document.getElementById('imageModal');
  if (imageModal) {
    imageModal.addEventListener('click', (e) => {
      if (e.target === e.currentTarget) {
        e.currentTarget.style.display = 'none';
      }
    });
  }

  
  // Agent ID form validation (only for agent_update.php)
  const agentIdForm = document.getElementById('agentIdForm');
  if (agentIdForm) {
    agentIdForm.addEventListener('submit', function (e) {
      e.preventDefault();
      let isValid = true;

      const agentIdInput = document.getElementById('agent_id');
      const errorElement = document.getElementById('agent-id-error');
      agentIdInput.classList.remove('is-invalid');
      errorElement.style.display = 'none';

      if (!agentIdInput.value.trim()) {
        isValid = false;
        agentIdInput.classList.add('is-invalid');
        errorElement.style.display = 'block';
      }

      if (isValid) {
        document.getElementById('agent_id_hidden').value = agentIdInput.value.trim();
        this.submit();
      }
    });
  }

  // Main form validation (for both add_agent.php and agent_update.php)
  const mainForm = document.getElementById('agent-form') || document.getElementById('agentForm');
  if (mainForm) {
    mainForm.addEventListener('submit', function (e) {
      e.preventDefault();
      let isValid = true;

      // Reset previous error states
      document.querySelectorAll('.form-control, .form-select, .file-upload').forEach(input => {
        input.classList.remove('is-invalid');
        const errorElement = document.getElementById(`${input.name}-error`);
        if (errorElement) errorElement.style.display = 'none';
      });

      const requiredFields = [
        { name: 'agent_code', errorId: 'agent-code-error' },
        { name: 'name', errorId: 'name-error' },
        { name: 'father_wife_name', errorId: 'father-wife-name-error' },
        { name: 'branch_id', errorId: 'branch-id-error' },
        { name: 'agent_gender', errorId: 'agent-gender-error' },
        { name: 'dob', errorId: 'dob-error', validate: (value) => /^\d{2}-\d{2}-\d{4}$/.test(value) },
        { name: 'referred_by', errorId: 'referred-by-error' },
        { name: 'introduced_cadre', errorId: 'introduced-cadre-error' },
        { name: 'join_date', errorId: 'join-date-error', validate: (value) => /^\d{2}-\d{2}-\d{4}$/.test(value) },
        { name: 'cadre_id', errorId: 'cadre-id-error' },
        { name: 'mobile_number', errorId: 'mobile-number-error', validate: (value) => /^\+?\d{10,15}$/.test(value) },
        { name: 'pan_no', errorId: 'pan-no-error', validate: (value) => /^[A-Z]{5}\d{4}[A-Z]{1}$/.test(value) },
        { name: 'account_no', errorId: 'account-no-error', validate: (value) => /^\d{9,18}$/.test(value) },
        { name: 'bank_name', errorId: 'bank-name-error' },
        { name: 'ifsc_code', errorId: 'ifsc-code-error', validate: (value) => /^[A-Z]{4}0[A-Z0-9]{6}$/.test(value) },
        { name: 'nominee_name', errorId: 'nominee-name-error' },
        { name: 'nominee_relation', errorId: 'nominee-relation-error' },
        { name: 'nominee_gender', errorId: 'nominee-gender-error' },
        { name: 'status', errorId: 'status-error' }
      ];

      requiredFields.forEach(field => {
        const input = document.querySelector(`[name="${field.name}"]`);
        if (!input) return;
        const errorElement = document.getElementById(field.errorId);
        const value = input.type === 'file' ? input.files.length : input.value.trim();

        if (!value || (field.validate && !field.validate(input.value.trim()))) {
          isValid = false;
          input.classList.add('is-invalid');
          errorElement.style.display = 'block';
        }
      });

      // Validate optional fields if provided
      const optionalFields = [
        { name: 'password', errorId: 'password-error', validate: (value) => value.length >= 8 }
      ];

      optionalFields.forEach(field => {
        const input = document.querySelector(`[name="${field.name}"]`);
        if (!input) return;
        const errorElement = document.getElementById(field.errorId);
        const value = input.value.trim();

        if (value && field.validate && !field.validate(value)) {
          isValid = false;
          input.classList.add('is-invalid');
          errorElement.style.display = 'block';
        }
      });

      if (isValid) {
        const confirmMessage = mainForm.id === 'agent-form' ?
          'Are you sure you want to submit the agent details?' :
          'Are you sure you want to update the agent details?';
        if (confirm(confirmMessage)) {
          this.submit();
        }
      }
    });
  }

  // Reset form and previews
  window.resetForm = function () {
    const form = document.getElementById('agent-form') || document.getElementById('agentForm');
    if (form) {
      form.reset();
      document.querySelectorAll('.form-control, .form-select, .file-upload').forEach(input => {
        input.classList.remove('is-invalid');
        const errorElement = document.getElementById(`${input.name}-error`);
        if (errorElement) errorElement.style.display = 'none';
      });
      document.querySelectorAll('#agent_photo_preview, #agent_applicationcopy_preview, #aadhar_copy_preview, #pan_copy_preview, #bankpassbook_copy_preview').forEach(preview => {
        preview.innerHTML = '';
      });
      document.getElementById('imageModal').style.display = 'none';
    }
  };
});



// Function to initialize file input handler
function initFileInputHandler() {
  console.log('Attempting to initialize file input handler');
  const fileInput = document.getElementById('file');
  const fileNameDisplay = document.querySelector('.file-name');

  if (!fileInput) {
    console.error('File input element with ID "file" not found');
    return false;
  }
  if (!fileNameDisplay) {
    console.error('File name display element with class "file-name" not found');
    return false;
  }

  console.log('File input and file-name elements found');
  fileInput.addEventListener('change', function () {
    console.log('File input changed');
    if (fileInput.files.length > 0) {
      fileNameDisplay.textContent = fileInput.files[0].name;
      console.log('Selected file:', fileInput.files[0].name);
    } else {
      fileNameDisplay.textContent = 'No file chosen';
      console.log('No file selected');
    }
  });
  return true;
}

// Run on DOMContentLoaded
document.addEventListener('DOMContentLoaded', function () {
  console.log('DOM loaded, initializing file input handler');
  if (!initFileInputHandler()) {
    // Retry after a delay if elements are not found (for dynamic content)
    setTimeout(function () {
      console.log('Retrying initialization after 1 second');
      initFileInputHandler();
    }, 1000);
  }
});
