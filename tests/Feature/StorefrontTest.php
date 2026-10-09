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
        $response->assertSee('Official Archive');
    }

    /**
     * Test Shop Catalog renders with filters and products
     */
    public function test_shop_catalog_renders_with_filters(): void
    {
        $this->seed();

        $response = $this->get(route('shop.index'));

        $response->assertStatus(200);
        $response->assertSee('Katalog Ngizan');
        $response->assertSee('Jersey');
    }

    /**
     * Test Product Detail renders with Dual POV and Live Nameset
     */
    public function test_product_detail_renders_with_nameset_studio(): void
    {
        $this->seed();
        $category = Category::first();
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Arsenal 2024/25 Home Kit',
            'slug' => 'arsenal-2024-25-home-kit',
            'base_price' => 350000,
            'weight_grams' => 250,
            'description' => 'Jersey kandang Arsenal',
            'is_active' => true,
        ]);

        ProductVariant::create([
            'product_id' => $product->id,
            'size' => 'L',
            'type' => 'Fans Issue',
            'stock' => 10,
            'price_adjustment' => 0,
            'sku' => 'ARS-L',
        ]);

        $response = $this->get(route('shop.show', $product->slug));

        $response->assertStatus(200);
        $response->assertSee($product->name);
        $response->assertSee('Size Chart');
    }

    /**
     * Test Contact page renders successfully with channels and FAQ
     */
    public function test_contact_page_renders_successfully(): void
    {
        $response = $this->get(route('contact'));

        $response->assertStatus(200);
        $response->assertSee('Hubungi Kami');
        $response->assertSee('WhatsApp CS Resmi');
        $response->assertSee('Email Support');
        $response->assertSee('Pertanyaan Umum (FAQ)');
    }
}
