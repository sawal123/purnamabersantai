<?php

namespace App\Livewire\Dashboard;

use App\Models\MerchandiseProductCategory;
use App\Support\DashboardResourceRegistry;
use Flux\Flux;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Throwable;

#[Layout('layouts::app')]
class MerchandiseProductPage extends ResourcePage
{
    public function mount(string $resource = 'merchandise-product'): void
    {
        $this->resource = 'merchandise-product';
        $this->resourceConfig = DashboardResourceRegistry::get($this->resource);
        $this->loadFieldOptions();
        $this->resetForm();
    }

    public function render()
    {
        return view('livewire.dashboard.merchandise-product-page', [
            'records' => $this->records,
            'summary' => $this->summary,
        ]);
    }

    public function updatedFormName(mixed $value): void
    {
        $this->form['slug'] = Str::slug((string) $value);
        $this->syncThumbnailAltFromSlug();
    }

    public function updatedFormSlug(mixed $value): void
    {
        $this->form['slug'] = Str::slug((string) $value);
        $this->syncThumbnailAltFromSlug();
    }

    public function generateMerchandiseDescriptionWithAi(): void
    {
        $this->resetValidation();

        $productName = trim((string) ($this->form['name'] ?? ''));
        $categoryId = $this->form['merchandise_product_category_id'] ?? null;
        $category = $categoryId ? MerchandiseProductCategory::query()->find($categoryId) : null;

        if ($productName === '') {
            $this->addError('form.name', 'Product Name harus diisi terlebih dahulu.');
        }

        if (! $category) {
            $this->addError('form.merchandise_product_category_id', 'Category harus dipilih terlebih dahulu.');
        }

        if ($productName === '' || ! $category) {
            Flux::toast(variant: 'danger', text: __('Isi Product Name dan Category terlebih dahulu.'));

            return;
        }

        $apiKey = config('services.openai.key');

        if (! is_string($apiKey) || trim($apiKey) === '') {
            Flux::toast(variant: 'danger', text: __('OPENAI_API_KEY belum tersedia di konfigurasi.'));

            return;
        }

        try {
            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->asJson()
                ->timeout(45)
                ->post('https://api.openai.com/v1/responses', [
                    'model' => config('services.openai.model', 'gpt-5.6-luna'),
                    'store' => false,
                    'input' => [
                        [
                            'role' => 'system',
                            'content' => 'You write concise Indonesian product descriptions for official music festival merchandise. Return clean HTML only through the requested JSON schema. Avoid claims about materials, stock, discounts, or images unless provided. Use a warm, premium, festival-ready tone.',
                        ],
                        [
                            'role' => 'user',
                            'content' => json_encode([
                                'brand' => 'Purnama Bersantai',
                                'product_name' => $productName,
                                'category' => $category->name,
                                'size_options' => $this->form['size_options'] ?? null,
                                'color_options' => $this->form['color_options'] ?? null,
                                'requirements' => [
                                    'language' => 'id_ID',
                                    'length' => '2 short paragraphs or 1 paragraph plus 3 short bullet points',
                                    'allowed_html_tags' => ['p', 'strong', 'ul', 'li'],
                                    'must_not_include' => ['price', 'stock quantity', 'image description', 'unverified material claims'],
                                ],
                            ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                        ],
                    ],
                    'text' => [
                        'format' => [
                            'type' => 'json_schema',
                            'name' => 'merchandise_description',
                            'strict' => true,
                            'schema' => [
                                'type' => 'object',
                                'additionalProperties' => false,
                                'required' => ['description_html'],
                                'properties' => [
                                    'description_html' => [
                                        'type' => 'string',
                                        'description' => 'Clean Indonesian HTML using only p, strong, ul, and li tags.',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ])
                ->throw()
                ->json();

            $generated = $this->decodeOpenAiJsonResponse($response);
            $description = $this->sanitizeGeneratedDescription($generated['description_html'] ?? '');

            if ($description === '') {
                throw new \RuntimeException('OpenAI response did not include a usable description.');
            }

            $this->form['description'] = $description;

            Flux::toast(variant: 'success', text: __('Description generated with AI.'));
        } catch (Throwable $exception) {
            report($exception);

            Flux::toast(variant: 'danger', text: __('Gagal generate description dengan AI. Cek API key, model, atau koneksi server.'));
        }
    }

    protected function applyAutoSlugFromName(): void
    {
        $this->form['slug'] = Str::slug((string) ($this->form['name'] ?? ''));
        $this->syncThumbnailAltFromSlug();
    }

    protected function syncThumbnailAltFromSlug(): void
    {
        $this->form['thumbnail_alt'] = (string) ($this->form['slug'] ?? '');
    }

    protected function sanitizeGeneratedDescription(mixed $description): string
    {
        if (! is_string($description)) {
            return '';
        }

        $description = trim($description);
        $description = strip_tags($description, '<p><strong><ul><li>');
        $description = preg_replace('/\s+on[a-z]+\s*=\s*(["\']).*?\1/i', '', $description) ?? $description;
        $description = preg_replace('/javascript\s*:/i', '', $description) ?? $description;

        return trim($description);
    }
}
