<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Reorder Tests - Rich Labz CRM</title>

    {{-- Global Header Links --}}
    @include('includes.header_links')

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>

    <style>
        body {
            background: #f4f7fb;
        }

        .sort-box {
            max-width: 900px;
            margin: 40px auto;
            padding: 30px;
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid #dce3ee;
            box-shadow: 0px 6px 20px rgba(0, 0, 0, 0.08);
        }

        .sort-box h3 {
            font-size: 22px;
            font-weight: 700;
            color: #0c4890;
            margin-bottom: 20px;
            letter-spacing: 0.4px;
        }

        .controls {
            display: flex;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 18px;
        }

        .controls input,
        .controls select {
            padding: 12px 14px;
            border: 1px solid #c8d4e2;
            border-radius: 8px;
            width: 100%;
            background: #f9fbfd;
            font-size: 15px;
        }

        .controls input:focus,
        .controls select:focus {
            border-color: #0c4890;
            outline: none;
            box-shadow: 0px 0px 5px rgba(12, 72, 144, 0.3);
        }

        .sortable-container {
            max-height: 520px;
            overflow-y: auto;
            padding-right: 8px;
            border-radius: 10px;
        }

        /* Smooth luxury scrollbar */
        .sortable-container::-webkit-scrollbar {
            width: 8px;
        }

        .sortable-container::-webkit-scrollbar-track {
            background: #eef2f7;
            border-radius: 10px;
        }

        .sortable-container::-webkit-scrollbar-thumb {
            background: #a0b4cc;
            border-radius: 10px;
        }

        ul.sortable {
            list-style: none;
            padding: 0;
        }

        ul.sortable li {
            background: #ffffff;
            padding: 15px 18px;
            margin-bottom: 10px;
            border: 1px solid #d8e2ee;
            border-radius: 10px;
            font-size: 15.5px;
            cursor: move;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: 0.2s ease;
            box-shadow: 0px 3px 10px rgba(0, 0, 0, 0.05);
        }

        ul.sortable li:hover {
            background: #f7faff;
            border-color: #b8cbe0;
        }

        .left-section {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .handle {
            font-weight: bold;
            cursor: grab;
            color: #0c4890;
            font-size: 18px;
        }

        .handle:active {
            cursor: grabbing;
        }

        .order-input {
            width: 70px;
            padding: 8px 10px;
            border: 1px solid #b5c6d8;
            border-radius: 8px;
            text-align: center;
            font-size: 15px;
        }

        .order-input:focus {
            border-color: #0c4890;
            outline: none;
            box-shadow: 0px 0px 6px rgba(12, 72, 144, 0.25);
        }

        .save-btn {
            margin-top: 25px;
            padding: 14px 18px;
            background: #0c4890;
            color: #fff;
            border: none;
            border-radius: 8px;
            width: 100%;
            font-size: 17px;
            font-weight: 600;
            letter-spacing: 0.4px;
            transition: 0.2s;
        }

        .save-btn:hover {
            background: #0a3a73;
        }
    </style>
</head>

<body>

    {{-- Sidebar --}}
    @include('includes.sidebar')

    {{-- Header --}}
    @include('includes.header')

    <div class="content">

        <div class="sort-box">

            <h3>Reorder Tests</h3>

            <!-- Controls -->
            <div class="controls">
                <input type="text" id="searchBox" placeholder="🔍 Search tests...">

                <select id="jumpTo">
                    <option value="">Jump to test...</option>
                    @foreach ($tests as $t)
                        <option value="{{ $t->id }}">{{ $t->test_name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Scrollable Container -->
            <div class="sortable-container">
                <ul class="sortable">
                    @foreach($tests as $t)
                        <li data-id="{{ $t->id }}">
                            <div class="left-section">
                                <span class="handle">☰</span>
                                {{ $t->test_name }}
                            </div>
                            <input type="number" class="order-input" value="{{ $t->display_order }}">
                        </li>
                    @endforeach
                </ul>
            </div>

            <button class="save-btn" id="saveOrder">Save Order</button>

        </div>

    </div>

    {{-- Footer --}}
    @include('includes.footer_links')

    <script>
        $(".sortable").sortable({
            handle: '.handle',
            animation: 150
        });

        $("#searchBox").on("keyup", function () {
            let term = $(this).val().toLowerCase();

            $(".sortable li").each(function () {
                let text = $(this).text().toLowerCase();
                $(this).toggle(text.indexOf(term) > -1);
            });
        });

        $("#jumpTo").change(function () {
            let id = $(this).val();
            if (!id) return;

            let target = $('.sortable li[data-id="' + id + '"]');

            $('.sortable-container').animate({
                scrollTop: target.position().top - 50
            }, 400);
        });

        $("#saveOrder").click(function () {
            let order = [];

            $(".sortable li").each(function (index) {
                let id = $(this).data("id");
                let manualOrder = $(this).find(".order-input").val();
                order.push({ id: id, order: manualOrder ? manualOrder : index + 1 });
            });

            $.post("{{ route('tests.order.save') }}", {
                _token: "{{ csrf_token() }}",
                order: order
            }, function (res) {
                alert("Order Saved Successfully!");
            });
        });
    </script>

</body>

</html>