<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Package</title>

    @include('includes.header_links')

    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

    <style>
        .sub-test-card {
            background: #fff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            margin-top: 15px;
        }

        .sub-test-header {
            display: flex;
            align-items: center;
            padding: 10px 12px;
            border-bottom: 1px solid #eee;
        }

        .sub-test-body {
            padding: 10px 12px;
        }

        .preview-group-label {
            width: 100%;
            font-size: 13px;
            font-weight: 600;
            color: #2563eb;
            margin: 10px 0 5px;
        }

        .preview-row {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

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
                <div class="crm-title">Edit Package</div>
            </div>

            <form method="POST" action="{{ route('update-package', $package->id) }}" id="packageForm">
                @csrf

                <input type="hidden" name="parameters_order" id="parameters_order_input">

                <div class="row mb-4">
                    <div class="col-md-4">
                        <label>Package Name *</label>
                        <input type="text" name="package_name" class="form-control"
                            value="{{ old('package_name', $package->package_name) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label>Package Price *</label>
                        <input type="number" name="package_price" class="form-control"
                            value="{{ old('package_price', $package->package_price) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label>Status *</label>
                        <select name="status" class="form-control">
                            <option value="1" {{ $package->status == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ $package->status == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
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
                    <div id="display_box" class="border p-3 text-center">
                        <small>No parameters selected yet.</small>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Update Package</button>
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

            function addPill(name, index, target) {
                const pill = document.createElement('div');
                pill.className = 'order-pill';
                pill.innerHTML = `<span class="num">${index}</span>${name}`;
                target.appendChild(pill);
            }

            window.updateAll = function () {

                let structure = {
                    global: globalTom.getValue(),
                    groups: []
                };

                displayBox.innerHTML = '';
                let order = 1;

                if (structure.global.length) {
                    let row = document.createElement('div');
                    row.className = 'preview-row';
                    displayBox.appendChild(row);

                    structure.global.forEach(v => {
                        addPill(globalTom.options[v].text, order++, row);
                    });
                }

                document.querySelectorAll('.sub-test-card:not(#section_global)').forEach(card => {

                    const title = card.querySelector('.group-title').value;
                    const ts = card.querySelector('.sub-select').tomselect;
                    const vals = ts.getValue();

                    if (vals.length) {
                        structure.groups.push({ group_name: title, params: vals });

                        displayBox.insertAdjacentHTML(
                            'beforeend',
                            `<div class="preview-group-label">${title}</div>`
                        );

                        let row = document.createElement('div');
                        row.className = 'preview-row';
                        displayBox.appendChild(row);

                        vals.forEach(v => {
                            addPill(ts.options[v].text, order++, row);
                        });
                    }
                });

                hiddenInput.value = JSON.stringify(structure);
            };

            let globalTom = new TomSelect("#global_select", {
                plugins: ['remove_button'],
                options: availableOptions,
                onItemAdd: updateAll,
                onItemRemove: updateAll
            });

            document.getElementById('btn_add_subtest').addEventListener('click', function () {

                const name = prompt("Enter Sub-Group Name:");
                if (!name) return;

                const id = 'sub_' + Date.now();

                container.insertAdjacentHTML('beforeend', `
            <div class="sub-test-card" id="${id}">
                <div class="sub-test-header">
                    <input type="text" class="form-control border-0 fw-bold group-title"
                        value="${name}" oninput="updateAll()">
                    <button type="button" class="btn btn-sm text-danger ms-auto"
                        onclick="document.getElementById('${id}').remove(); updateAll();">✖</button>
                </div>
                <div class="sub-test-body">
                    <select class="sub-select" multiple></select>
                </div>
            </div>
        `);

                new TomSelect(document.querySelector(`#${id} .sub-select`), {
                    plugins: ['remove_button'],
                    options: availableOptions,
                    onItemAdd: updateAll,
                    onItemRemove: updateAll
                });
            });

            /* ---------- LOAD EXISTING DATA ---------- */
            const existing = @json($package->parameters);

            if (existing) {

                if (existing.global) {
                    globalTom.setValue(existing.global);
                }

                if (existing.groups && existing.groups.length > 0) {

                    existing.groups.forEach(g => {

                        const id = 'sub_' + Date.now() + Math.random().toString(36).substr(2, 5);

                        container.insertAdjacentHTML('beforeend', `
            <div class="sub-test-card" id="${id}">
                <div class="sub-test-header">
                    <input type="text"
                        class="form-control border-0 fw-bold group-title"
                        value="${g.group_name}"
                        oninput="updateAll()">
                    <button type="button"
                        class="btn btn-sm text-danger ms-auto"
                        onclick="document.getElementById('${id}').remove(); updateAll();">✖</button>
                </div>
                <div class="sub-test-body">
                    <select class="sub-select" multiple></select>
                </div>
            </div>
        `);

                        const selectEl = document.querySelector(`#${id} .sub-select`);

                        const ts = new TomSelect(selectEl, {
                            plugins: ['remove_button'],
                            options: availableOptions,
                            onItemAdd: updateAll,
                            onItemRemove: updateAll
                        });

                        ts.setValue(g.params);
                    });
                }
            }

            updateAll();
        });
    </script>

</body>

</html>