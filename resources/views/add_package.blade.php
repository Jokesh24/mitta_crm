<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Package</title>

    @include('includes.header_links')

    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>


    <style>
        /* ---------- PAGE POLISH ---------- */
        .crm-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .crm-title {
            font-size: 20px;
            font-weight: 600;
            color: #1f2937;
        }

        /* ---------- STRUCTURE AREA ---------- */
        .structure-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 20px 0 10px;
        }

        .structure-toolbar h6 {
            margin: 0;
            font-weight: 600;
            color: #2563eb;
        }

        /* ---------- SUB GROUP CARD ---------- */
        .sub-test-card {
            background: #ffffff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .sub-test-header input {
            font-size: 14px;
        }

        /* ---------- LIVE PREVIEW ---------- */
        .selected-items-box {
            background: #f9fafb;
            border: 1px dashed #d1d5db;
        }

        /* Group label */
        .preview-group-label {
            width: 100%;
            font-size: 13px;
            font-weight: 600;
            color: #2563eb;
            margin: 10px 0 5px;
        }

        /* Pills container */
        .preview-row {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        /* Pill */
        .order-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #e7f1ff;
            border: 1px solid #b6d4fe;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 500;
            color: #1e40af;
        }

        /* Number circle */
        .order-pill .num {
            min-width: 20px;
            height: 20px;
            background: #2563eb;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 11px;
            font-weight: 600;
        }
    </style>

</head>

<body>
    @include('includes.sidebar')
    @include('includes.header')

    <div class="content">
        <div class="container-crm">

            <div class="crm-header">
                <div class="crm-title">Add Package</div>
            </div>

            <!-- <form method="POST" action="{{ route('store-package') }}"> -->
            <form method="POST" action="{{ route('store-package') }}" id="packageForm">
                @csrf

                <input type="hidden" name="parameters_order" id="parameters_order_input">

                <div class="row mb-4">
                    <div class="col-md-4">
                        <label>Package Name *</label>
                        <input type="text" name="package_name" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label>Package Price *</label>
                        <input type="number" name="package_price" class="form-control" required>
                    </div>
                </div>

                <button type="button" class="btn btn-sm btn-outline-dark" id="btn_add_subtest">
                    + Add Sub-Group
                </button>

                <div id="test_structure_area">
                    <div class="sub-test-card" id="section_global">
                        <div class="sub-test-header">
                            <strong>General Parameters</strong>
                        </div>
                        <div class="sub-test-body">
                            <select id="global_select" multiple></select>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <label>Live Preview</label>
                    <div id="display_box" class="selected-items-box text-center">
                        <small>No parameters selected yet.</small>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Save Package</button>
                </div>

            </form>
        </div>
    </div>

    @include('includes.footer_links')
    <script>
        document.addEventListener("DOMContentLoaded", function () {

            const container = document.getElementById('test_structure_area');
            const hiddenInput = document.getElementById('parameters_order_input');
            const displayBox = document.getElementById('display_box');

            const availableOptions = @json($parameters).map(p => ({
                value: p.id,
                text: p.test_name
            }));

            /* ---------- MAIN UPDATE FUNCTION (DEFINED FIRST) ---------- */
            // function updateAll() {

            window.updateAll = function () {

                let structure = {
                    global: globalTom.getValue(),
                    groups: []
                };

                displayBox.innerHTML = '';
                let pillIndex = 1;

                // Global parameters
                if (structure.global.length > 0) {
                    const row = document.createElement('div');
                    row.className = 'preview-row';
                    displayBox.appendChild(row);

                    structure.global.forEach(val => {
                        const opt = globalTom.options[val];
                        addPill(opt ? opt.text : 'Unknown', pillIndex++, row);
                    });
                }

                // Group parameters
                document.querySelectorAll('.sub-test-card:not(#section_global)').forEach(card => {

                    const title = card.querySelector('.group-title').value;
                    const ts = card.querySelector('.sub-select').tomselect;
                    const vals = ts.getValue();

                    if (vals.length > 0) {

                        structure.groups.push({
                            group_name: title,
                            params: vals
                        });

                        const label = document.createElement('div');
                        label.className = 'preview-group-label';
                        label.innerText = title;
                        displayBox.appendChild(label);

                        const row = document.createElement('div');
                        row.className = 'preview-row';
                        displayBox.appendChild(row);

                        vals.forEach(v => {
                            const opt = ts.options[v];
                            addPill(opt ? opt.text : 'Unknown', pillIndex++, row);
                        });
                    }
                });

                if (pillIndex === 1) {
                    displayBox.innerHTML = '<small>No parameters selected yet.</small>';
                    displayBox.classList.add('text-center');
                } else {
                    displayBox.classList.remove('text-center');
                }

                hiddenInput.value = JSON.stringify(structure);
            }

            function addPill(name, index, target = displayBox) {
                const pill = document.createElement('div');
                pill.className = 'order-pill';
                pill.innerHTML = `<span class="num">${index}</span>${name}`;
                target.appendChild(pill);
            }

            /* ---------- GLOBAL SELECT ---------- */
            let globalTom = new TomSelect("#global_select", {
                plugins: ['remove_button'],
                options: availableOptions,
                onItemAdd: updateAll,
                onItemRemove: updateAll
            });

            /* ---------- ADD SUB GROUP ---------- */
            document.getElementById('btn_add_subtest').addEventListener('click', function () {

                const subTitle = prompt("Enter Sub-Group Name:");
                if (!subTitle) return;

                const id = 'sub_' + Date.now();

                const html = `
            <div class="sub-test-card" id="${id}">
                <div class="sub-test-header">
                    <input type="text"
                        class="form-control form-control-sm border-0 bg-transparent fw-bold group-title"
                        value="${subTitle}"
                        oninput="updateAll()">
                    <button type="button"
                        class="btn btn-sm text-danger ms-auto"
                        onclick="removeSubTest('${id}')">✖</button>
                </div>
                <div class="sub-test-body">
                    <select class="sub-select" multiple></select>
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

            /* ---------- REMOVE SUB GROUP ---------- */
            window.removeSubTest = function (id) {
                document.getElementById(id).remove();
                updateAll();
            };

            document.getElementById('packageForm').addEventListener('submit', function (e) {

                // Always rebuild structure before submit
                updateAll();

                const data = document.getElementById('parameters_order_input').value;

                try {
                    const parsed = JSON.parse(data);

                    const hasGlobal = parsed.global && parsed.global.length > 0;
                    const hasGroups = parsed.groups && parsed.groups.some(g => g.params.length > 0);

                    if (!hasGlobal && !hasGroups) {
                        e.preventDefault();
                        alert('Please select at least one parameter before saving the package.');
                        return false;
                    }

                } catch (err) {
                    e.preventDefault();
                    alert('Invalid package structure.');
                    return false;
                }
            });

        });


    </script>

</body>

</html>