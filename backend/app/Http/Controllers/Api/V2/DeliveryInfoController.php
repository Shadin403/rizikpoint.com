<?php

namespace App\Http\Controllers\Api\V2;

use App\Models\DeliveryInfo;
use Illuminate\Http\Request;

class DeliveryInfoController extends Controller
{
    /**
     * Admin page: list all delivery info rows + form to add/edit.
     */
    public function adminIndex(Request $request)
    {
        $rows = DeliveryInfo::orderBy('id')->get();
        return view('backend.setup_configurations.delivery_info', compact('rows'));
    }

    /**
     * Save the entire form (key/value rows). Creates new rows for new keys,
     * updates existing rows by id, and removes rows the admin deleted.
     */
    public function save(Request $request)
    {
        $keptIds = [];
        $keys    = (array) $request->input('keys', []);
        $values  = (array) $request->input('values', []);
        $ids     = (array) $request->input('ids', []);

        foreach ($keys as $i => $rawKey) {
            $key = trim((string) $rawKey);
            if ($key === '') {
                continue;
            }
            $val = $values[$i] ?? '';
            $id  = $ids[$i] ?? null;

            if ($id) {
                $row = DeliveryInfo::find($id);
                if ($row) {
                    // If the admin changed the key, free up the old key first.
                    if ($row->key !== $key) {
                        DeliveryInfo::where('key', $key)->where('id', '!=', $row->id)->delete();
                    }
                    $row->key   = $key;
                    $row->value = $val;
                    $row->save();
                    $keptIds[] = $row->id;
                } else {
                    $row = DeliveryInfo::create(['key' => $key, 'value' => $val]);
                    $keptIds[] = $row->id;
                }
            } else {
                DeliveryInfo::where('key', $key)->delete();
                $row = DeliveryInfo::create(['key' => $key, 'value' => $val]);
                $keptIds[] = $row->id;
            }
        }

        // Remove rows the admin deleted in the UI.
        DeliveryInfo::whereNotIn('id', $keptIds)->delete();

        \Artisan::call('cache:clear');
        flash(translate('Delivery info updated successfully'))->success();
        return back();
    }

    /**
     * Public API: returns all delivery info rows as a plain JSON array
     * shaped like [{key, value}, ...] for the storefront.
     */
    public function index()
    {
        $rows = DeliveryInfo::orderBy('id')->get(['key', 'value'])->map(function ($r) {
            return [
                'type'  => $r->key,
                'value' => $r->value,
            ];
        });
        return response()->json([
            'data'    => $rows,
            'success' => true,
            'status'  => 200,
        ]);
    }
}
