<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'category_id',
        'name',
        'description',
        'price',
        'stock',
        'image',
        'status',
    ];

    protected $validationRules = [
        'category_id' => 'required|is_natural_no_zero',
        'name'        => 'required|min_length[3]|max_length[120]',
        'description' => 'required|min_length[10]|max_length[1000]',
        'price'       => 'required|decimal|greater_than[0]',
        'stock'       => 'required|integer|greater_than_equal_to[0]',
        'image'       => 'required|max_length[255]',
        'status'      => 'required|in_list[active,inactive]',
    ];

    public function withCategory(): self
    {
        return $this->select('products.*, categories.name AS category_name')
            ->join('categories', 'categories.id = products.category_id');
    }

    public function activeForHome(int $limit = 6): array
    {
        return $this->withCategory()
            ->where('products.status', 'active')
            ->orderBy('products.id', 'DESC')
            ->findAll($limit);
    }

    public function catalog(array $filters = [], int $perPage = 9): array
    {
        $this->withCategory()->where('products.status', 'active');

        if (($filters['q'] ?? '') !== '') {
            $this->groupStart()
                ->like('products.name', $filters['q'])
                ->orLike('products.description', $filters['q'])
                ->groupEnd();
        }

        if ((int) ($filters['category'] ?? 0) > 0) {
            $this->where('products.category_id', (int) $filters['category']);
        }

        switch ($filters['sort'] ?? '') {
            case 'price-low':
                $this->orderBy('products.price', 'ASC');
                break;
            case 'price-high':
                $this->orderBy('products.price', 'DESC');
                break;
            default:
                $this->orderBy('products.id', 'DESC');
        }

        return $this->paginate($perPage);
    }

    public function findActiveWithCategory(int $id): ?array
    {
        $product = $this->withCategory()
            ->where('products.status', 'active')
            ->where('products.id', $id)
            ->first();

        return is_array($product) ? $product : null;
    }

    public function adminList(int $perPage = 10): array
    {
        return $this->withCategory()
            ->orderBy('products.id', 'DESC')
            ->paginate($perPage);
    }

    public function countForCategory(int $categoryId): int
    {
        return $this->where('category_id', $categoryId)->countAllResults();
    }
}
