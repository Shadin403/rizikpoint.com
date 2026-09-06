@extends('backend.layouts.app')

@section('content')

    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="mb-0 h6">{{ translate('Storefront Info') }}</h1>
                        <small class="text-muted">
                            {{ translate('Add any key:value pairs you want shown on the storefront. Use the + button to add a new row.') }}
                        </small>
                    </div>
                    <button type="button" class="btn btn-soft-primary btn-sm" id="addRowBtn">
                        <i class="las la-plus"></i> {{ translate('Add row') }}
                    </button>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('business_settings.update') }}" method="POST" id="storefrontForm">
                        @csrf
                        <input type="hidden" name="source" value="storefront_info">
                        <div id="kv-rows">
                            @php
                                $existing = \App\Models\BusinessSetting::orderBy('id')->get();
                                $rows = $existing->count() > 0
                                    ? $existing
                                    : collect([
                                        (object)['type' => '', 'value' => ''],
                                    ]);
                            @endphp
                            @foreach ($rows as $i => $row)
                                <div class="form-group row kv-row align-items-center">
                                    <div class="col-sm-4">
                                        <input type="text" name="keys[]" class="form-control kv-key" placeholder="{{ translate('key (e.g. shipping_inside_dhaka)') }}" value="{{ $row->type }}">
                                    </div>
                                    <div class="col-sm-7">
                                        <input type="text" name="values[]" class="form-control kv-value" placeholder="{{ translate('value') }}" value="{{ $row->value }}">
                                    </div>
                                    <div class="col-sm-1 text-right">
                                        <button type="button" class="btn btn-soft-danger btn-icon btn-sm kv-remove" title="{{ translate('Remove') }}">
                                            <i class="las la-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="form-group mb-0 text-right mt-3">
                            <button type="submit" class="btn btn-primary">{{ translate('Save all') }}</button>
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
                    // Always keep at least one row.
                    if (rowsContainer.querySelectorAll('.kv-row').length <= 1) {
                        row.querySelectorAll('input').forEach(i => i.value = '');
                        return;
                    }
                    row.remove();
                });
            }

            // Attach remove handlers to existing rows.
            rowsContainer.querySelectorAll('.kv-remove').forEach(attachRemove);

            // Add new row on + click.
            addBtn.addEventListener('click', function () {
                const template = rowsContainer.querySelector('.kv-row');
                const clone = template.cloneNode(true);
                clone.querySelectorAll('input').forEach(i => i.value = '');
                rowsContainer.appendChild(clone);
                attachRemove(clone.querySelector('.kv-remove'));
            });

            // Sanitize keys: lowercase + underscores.
            document.getElementById('storefrontForm').addEventListener('submit', function () {
                document.querySelectorAll('.kv-key').forEach(function (input) {
                    input.value = input.value.trim();
                });
            });
        })();
    </script>
@endsection
