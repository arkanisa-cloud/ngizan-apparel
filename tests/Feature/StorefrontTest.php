<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Homepage Storefront renders with Hero and Custom Studio
     */
    public function test_homepage_renders_successfully(): void
    {
        $this->seed();

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('NGIZAN');
        $response->assertSee('EDISI MUSIM 2024/2025');
        $response->assertSee('Studio Kustomisasi');
        $response->assertSee('Katalog Paling Dicari');
    }

    /**
     * Test Shop Catalog renders with filters and products
     */
    public function test_shop_catalog_renders_with_filters(): void
    {
        $this->seed();

        $response = $this->get(route('shop.index'));

        $response->assertStatus(200);
        $response->assertSee('Katalog Jersey');
        $response->assertSee('Klub Eropa');
        $response->assertSee('Tim Nasional');
    }

    /**
     * Test Product Detail renders with Dual POV and Live Nameset
     */
    public function test_product_detail_renders_with_nameset_studio(): void
    {
        $this->seed();
        $product = Product::first();

        $response = $this->get(route('shop.show', $product->slug));

        $response->assertStatus(200);
        $response->assertSee($product->name);
        $response->assertSee('Live Nameset Back POV');
        $response->assertSee('Size Chart');
    }
}
