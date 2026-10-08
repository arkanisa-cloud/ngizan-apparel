<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ShippingAddress;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * ShippingAddressController
 * Controller untuk mengelola alamat pengiriman customer
 */
class ShippingAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     * List semua alamat customer
     */
    public function index(): View
    {
        $addresses = Auth::user()->shippingAddresses;
        return view('customer.shipping-addresses.index', compact('addresses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('customer.shipping-addresses.create');
    }

    /**
     * Store a newly created resource in storage.
     * Simpan alamat baru
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'label'            => 'nullable|string|max:100',
            'recipient_name'   => 'required|string|max:255',
            'phone'            => 'required|string|max:50',
            'address'          => 'required|string',
            'district'         => 'nullable|string|max:100',
            'city'             => 'required|string|max:100',
            'province'         => 'required|string|max:100',
            'postal_code'      => 'required|string|max:10',
            'benchmark_notes'  => 'nullable|string|max:255',
            'is_primary'       => 'nullable|boolean',
            'biteship_area_id' => 'nullable|string|max:100',
            'latitude'         => 'nullable|numeric',
            'longitude'        => 'nullable|numeric',
        ]);

        $hasAddresses = Auth::user()->shippingAddresses()->exists();
        $isPrimary = $request->boolean('is_primary') || !$hasAddresses;

        if ($isPrimary) {
            Auth::user()->shippingAddresses()->update(['is_primary' => false]);
        }

        Auth::user()->shippingAddresses()->create([
            'label'            => $validated['label'] ?: ($hasAddresses ? 'Alamat Saya' : 'Rumah'),
            'recipient_name'   => $validated['recipient_name'],
            'phone_number'     => $validated['phone'],
            'full_address'     => $validated['address'],
            'city_name'        => $validated['city'],
            'province_name'    => $validated['province'],
            'district_name'    => $validated['district'] ?? '-',
            'postal_code'      => $validated['postal_code'],
            'benchmark_notes'  => $validated['benchmark_notes'] ?? null,
            'biteship_area_id' => $validated['biteship_area_id'] ?? null,
            'latitude'         => $validated['latitude'] ?? null,
            'longitude'        => $validated['longitude'] ?? null,
            'is_primary'       => $isPrimary,
        ]);

        return redirect()
            ->route('customer.addresses.index')
            ->with('success', 'Alamat berhasil ditambahkan.');
    }

    /**
     * Set alamat sebagai alamat utama
     */
    public function setPrimary(ShippingAddress $address): RedirectResponse
    {
        if ($address->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        Auth::user()->shippingAddresses()->update(['is_primary' => false]);
        $address->update(['is_primary' => true]);

        return back()->with('success', 'Alamat "' . ($address->label ?? 'Alamat') . '" berhasil dijadikan sebagai alamat utama.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ShippingAddress $address): View
    {
        $shippingAddress = $address;

        // Cek ownership
        if ($shippingAddress->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return view('customer.shipping-addresses.edit', compact('shippingAddress', 'address'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ShippingAddress $address): RedirectResponse
    {
        $shippingAddress = $address;

        // Cek ownership
        if ($shippingAddress->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'label'            => 'nullable|string|max:100',
            'recipient_name'   => 'required|string|max:255',
            'phone'            => 'required|string|max:50',
            'address'          => 'required|string',
            'district'         => 'nullable|string|max:100',
            'city'             => 'required|string|max:100',
            'province'         => 'required|string|max:100',
            'postal_code'      => 'required|string|max:10',
            'benchmark_notes'  => 'nullable|string|max:255',
            'is_primary'       => 'nullable|boolean',
            'biteship_area_id' => 'nullable|string|max:100',
            'latitude'         => 'nullable|numeric',
            'longitude'        => 'nullable|numeric',
        ]);

        $isPrimary = $request->has('is_primary') ? $request->boolean('is_primary') : $shippingAddress->is_primary;

        if ($isPrimary) {
            Auth::user()->shippingAddresses()->where('id', '!=', $shippingAddress->id)->update(['is_primary' => false]);
        }

        $shippingAddress->update([
            'label'            => $validated['label'] ?: $shippingAddress->label,
            'recipient_name'   => $validated['recipient_name'],
            'phone_number'     => $validated['phone'],
            'full_address'     => $validated['address'],
            'city_name'        => $validated['city'],
            'province_name'    => $validated['province'],
            'district_name'    => $validated['district'] ?? ($shippingAddress->district_name ?? '-'),
            'postal_code'      => $validated['postal_code'],
            'benchmark_notes'  => $validated['benchmark_notes'] ?? $shippingAddress->benchmark_notes,
            'biteship_area_id' => $validated['biteship_area_id'] ?? $shippingAddress->biteship_area_id,
            'latitude'         => $validated['latitude'] ?? $shippingAddress->latitude,
            'longitude'        => $validated['longitude'] ?? $shippingAddress->longitude,
            'is_primary'       => $isPrimary,
        ]);

        return redirect()
            ->route('customer.addresses.index')
            ->with('success', 'Alamat berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShippingAddress $address): RedirectResponse
    {
        $shippingAddress = $address;

        // Cek ownership
        if ($shippingAddress->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Cek apakah alamat dipakai di order aktif
        if ($shippingAddress->hasActiveOrders()) {
            return back()->with('error', 'Tidak bisa menghapus alamat yang sedang digunakan dalam pesanan aktif.');
        }

        $shippingAddress->delete();

        return redirect()
            ->route('customer.addresses.index')
            ->with('success', 'Alamat berhasil dihapus.');
    }
}
