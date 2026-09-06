@extends('backend.layouts.app')

@section('content')

    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="mb-0 h6">{{ translate('Delivery Info') }}</h1>
                        <small class="text-muted">
                            {{ translate("Type any key and its value. Whatever you type is saved as-is. Use the + button to add a new row.") }}
                        </small>
                    </div>
                    <button type="button" class="btn btn-soft-primary btn-sm" id="addRowBtn">
                        <i class="las la-plus"></i> {{ translate('Add row') }}
                    </button>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('delivery_info.save') }}" method="POST" id="deliveryForm">
                        @csrf
                        <div id="kv-rows">
                            @php
                                $rows = $rows ?? collect();
                                if ($rows->count() === 0) {
                                    $rows = collect([
                                        (object)['id' => null, 'key' => 'shipping_inside_dhaka',  'value' => '70'],
                                        (object)['id' => null, 'key' => 'shipping_outside_dhaka', 'value' => '130'],
                                        (object)['id' => null, 'key' => 'return_policy_days',     'value' => '7'],
                                    ]);
                                }
                            @endphp
                            @foreach ($rows as $row)
                                <div class="form-group row kv-row align-items-center">
                                    <input type="hidden" name="ids[]" value="{{ $row->id }}">
                                    <div class="col-sm-4">
                                        <input type="text" name="keys[]" class="form-control kv-key" placeholder="key" value="{{ $row->key }}" autocomplete="off">
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" name="values[]" class="form-control kv-value" placeholder="value" value="{{ $row->value }}">
                                    </div>
                                    <div class="col-sm-1 text-right">
                                        <button type="button" class="btn btn-soft-danger btn-icon btn-sm kv-remove" title="{{ translate('Remove') }}">
                                            <i class="las la-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="form-group mb-0 text-right">
                            <button type="submit" class="btn btn-primary">{{ translate('Save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        (function () {
            const rowsContainer = document.getElementById('kv-rows');
            const addBtn = document.getElementById('addRowBtn');

            function attachRemove(btn) {
                btn.addEventListener('click', function () {
                    const row = btn.closest('.kv-row');
                    if (!row) return;
                    if (rowsContainer.querySelectorAll('.kv-row').length <= 1) {
                        row.querySelectorAll('input').forEach(i => i.value = '');
                        return;
                    }
                    row.remove();
                });
            }

            rowsContainer.querySelectorAll('.kv-remove').forEach(attachRemove);

            addBtn.addEventListener('click', function () {
                const template = rowsContainer.querySelector('.kv-row');
                const clone = template.cloneNode(true);
                clone.querySelectorAll('input').forEach(i => {
                    if (i.type === 'hidden' && i.name === 'ids[]') {
                        i.value = '';
                    } else {
                        i.value = '';
                    }
                });
                rowsContainer.appendChild(clone);
                attachRemove(clone.querySelector('.kv-remove'));
            });

            document.getElementById('deliveryForm').addEventListener('submit', function () {
                document.querySelectorAll('.kv-key').forEach(function (input) {
                    input.value = input.value.trim();
                });
            });
        })();
    </script>
@endsection
