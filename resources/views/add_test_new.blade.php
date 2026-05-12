<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Add New Test</title>

    @include('includes.header_links')

    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

    <style>
        .input-icon {
            position: relative;
        }

        .input-icon .icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        .input-icon input {
            padding-left: 40px !important;
        }

        /* Sortable Display Area */
        .selected-items-box {
            background: #f8f9fa;
            border: 1px dashed #ced4da;
            border-radius: 8px;
            padding: 15px;
            min-height: 60px;
            margin-top: 10px;
        }

        .order-pill {
            display: inline-flex;
            align-items: center;
            background: #e7f1ff;
            color: #0056b3;
            border: 1px solid #b6d4fe;
            padding: 5px 12px;
            border-radius: 20px;
            margin: 4px;
            font-size: 13px;
            font-weight: 500;
            animation: popIn 0.2s ease;
        }

        .order-pill .num {
            background: #0056b3;
            color: #fff;
            width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 10px;
            margin-right: 8px;
        }

        /* Sub-Test Styling */
        .sub-test-card {
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            background: #fff;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .sub-test-header {
            background: #fdfdfd;
            padding: 10px 15px;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 10px;
            border-radius: 10px 10px 0 0;
        }

        .sub-test-body {
            padding: 15px;
        }

        @keyframes popIn {
            0% {
                transform: scale(0.8);
                opacity: 0;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }
    </style>
</head>

<body>
    @include('includes.sidebar')
    @include('includes.header')

    <div class="content">
        <div class="container-crm">
            <div class="crm-header">
                <div class="crm-title">Add New Test</div>
                <div class="d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-back" onclick="history.back()">
                        <i class="bi bi-arrow-left"></i> Back
                    </button>
                    <a href="{{ route('manage-test-new') }}" class="create-invoice-btn">Manage Tests</a>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mt-3">
                    <strong><i class="bi bi-check-circle-fill"></i></strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show mt-3">
                    <strong><i class="bi bi-exclamation-triangle-fill"></i></strong> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="crm-card">
                <form id="addTestForm" method="POST" action="{{ route('store-test-new') }}" autocomplete="off">
                    @csrf

                    <input type="hidden" name="parameters_order" id="parameters_order_input"
                        value="{{ old('parameters_order') }}">

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Test Name <span class="text-danger">*</span></label>
                            <div class="input-icon">
                                <div class="icon"><i class="bi bi-flask"></i></div>
                                <input type="text" class="form-control" name="test_name" value="{{ old('test_name') }}"
                                    placeholder="Enter Test Name">
                            </div>
                            @error('test_name') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Price <span class="text-danger">*</span></label>
                            <div class="input-icon">
                                <div class="icon"><i class="bi bi-currency-rupee"></i></div>
                                <input type="number" class="form-control" name="price" value="{{ old('price') }}"
                                    placeholder="0.00">
                            </div>
                            @error('price') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0 text-primary fw-bold">Structure & Parameters</h6>
                        <button type="button" class="btn btn-sm btn-outline-dark" id="btn_add_subtest">
                            <i class="bi bi-plus-circle"></i> Add Sub-Test Group
                        </button>
                    </div>

                    <div id="test_structure_area">
                        <div class="sub-test-card" id="section_global">
                            <div class="sub-test-header">
                                <i class="bi bi-list-stars text-secondary"></i>
                                <span class="fw-bold small text-uppercase">General Parameters (Ungrouped)</span>
                            </div>
                            <div class="sub-test-body">
                                <select id="global_select" class="param-selector" multiple
                                    placeholder="Select parameters..."></select>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mt-4">
                        <label class="form-label text-muted small">Live Preview (Final Order):</label>
                        <div id="display_box" class="selected-items-box text-muted text-center pt-3">
                            <small>No parameters selected yet.</small>
                        </div>
                    </div>

                    <div class="crm-action-bar mt-4">
                        <button type="button" class="btn btn-outline-secondary" onclick="history.back()">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Test</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('includes.footer_links')

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const container = document.getElementById('test_structure_area');
            const hiddenInput = document.getElementById('parameters_order_input');
            const displayBox = document.getElementById('display_box');

            // Available parameters from the Controller
            const availableOptions = @json($parameters).map(p => ({ value: p.id, text: p.test_name }));

            // 1. Initialize Global Select
            let globalTom = new TomSelect("#global_select", {
                plugins: ['remove_button'],
                options: availableOptions,
                onItemAdd: updateAll,
                onItemRemove: updateAll
            });

            // 2. Add Sub-Test Logic
            document.getElementById('btn_add_subtest').addEventListener('click', function () {
                const subTitle = prompt("Enter Sub-Test Name (e.g. Liver Function):");
                if (!subTitle) return;

                const id = 'sub_' + Date.now();
                const html = `
                    <div class="sub-test-card" id="${id}">
                        <div class="sub-test-header">
                            <i class="bi bi-folder2-open text-primary"></i>
                            <input type="text" class="form-control form-control-sm border-0 bg-transparent fw-bold group-title" value="${subTitle}" oninput="updateAll()">
                            <button type="button" class="btn btn-sm text-danger ms-auto" onclick="removeSubTest('${id}')"><i class="bi bi-trash"></i></button>
                        </div>
                        <div class="sub-test-body">
                            <select class="param-selector sub-select" multiple placeholder="Select sub-test parameters..."></select>
                        </div>
                    </div>`;

                container.insertAdjacentHTML('beforeend', html);

                new TomSelect(document.getElementById(id).querySelector('.sub-select'), {
                    plugins: ['remove_button'],
                    options: availableOptions,
                    onItemAdd: updateAll,
                    onItemRemove: updateAll
                });
                updateAll();
            });

            window.removeSubTest = function (id) {
                document.getElementById(id).remove();
                updateAll();
            };

            // 3. Main function to update Hidden Input and the Pill Display
            function updateAll() {
                let structure = {
                    global: globalTom.getValue(),
                    groups: []
                };

                displayBox.innerHTML = '';
                let pillIndex = 1;

                // Process Ungrouped
                structure.global.forEach(val => {
                    const opt = globalTom.options[val];
                    addPill(opt ? opt.text : 'Unknown', pillIndex++);
                });

                // Process Groups
                document.querySelectorAll('.sub-test-card:not(#section_global)').forEach(card => {
                    const title = card.querySelector('.group-title').value;
                    const ts = card.querySelector('.sub-select').tomselect;
                    const vals = ts.getValue();

                    if (vals.length > 0) {
                        structure.groups.push({ group_name: title, params: vals });

                        // Add group label to preview
                        const label = document.createElement('div');
                        label.className = 'w-100 mt-2 mb-1 small fw-bold text-primary';
                        label.innerHTML = `<i class="bi bi-chevron-right"></i> ${title}`;
                        displayBox.appendChild(label);

                        vals.forEach(v => {
                            const opt = ts.options[v];
                            addPill(opt ? opt.text : 'Unknown', pillIndex++);
                        });
                    }
                });

                if (pillIndex === 1) {
                    displayBox.innerHTML = '<small>No parameters selected yet.</small>';
                    displayBox.classList.add('text-center', 'pt-3');
                } else {
                    displayBox.classList.remove('text-center', 'pt-3');
                }

                hiddenInput.value = JSON.stringify(structure);
            }

            function addPill(name, index) {
                const pill = document.createElement('div');
                pill.className = 'order-pill';
                pill.innerHTML = `<span class="num">${index}</span> ${name}`;
                displayBox.appendChild(pill);
            }
        });
    </script>
</body>

</html>