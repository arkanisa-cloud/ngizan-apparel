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
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
        ]);

        Auth::user()->shippingAddresses()->create($validated);

        return redirect()
            ->route('customer.addresses.index')
            ->with('success', 'Alamat berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ShippingAddress $shippingAddress): View
    {
        // Cek ownership
        if ($shippingAddress->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return view('customer.shipping-addresses.edit', compact('shippingAddress'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ShippingAddress $shippingAddress): RedirectResponse
    {
        // Cek ownership
        if ($shippingAddress->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
        ]);

        $shippingAddress->update($validated);

        return redirect()
            ->route('customer.addresses.index')
            ->with('success', 'Alamat berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShippingAddress $shippingAddress): RedirectResponse
    {
        // Cek ownership
        if ($shippingAddress->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Cek apakah alamat dipakai di order aktif
        if ($shippingAddress->orders()->whereIn('status', ['pending', 'processed', 'shipped'])->count() > 0) {
            return back()->with('error', 'Tidak bisa menghapus alamat yang digunakan di order aktif.');
        }

        $shippingAddress->delete();

        return redirect()
            ->route('customer.addresses.index')
            ->with('success', 'Alamat berhasil dihapus.');
    }
}
