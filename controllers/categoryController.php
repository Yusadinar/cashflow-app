<?php
// ─────────────────────────────────────────
// controllers/categoryController.php
// ─────────────────────────────────────────
require_once __DIR__ . '/../models/Category.php';

class CategoryController
{
    private Category $category;

    public function __construct()
    {
        $this->category = new Category();
    }

    public function index(): array
    {
        return $this->category->getAll();
    }

    public function byType(string $type): array
    {
        return $this->category->getByType($type);
    }

    public function show(int $id): array|null
    {
        return $this->category->getById($id);
    }

    public function store(string $name, string $type): array
    {
        $errors = $this->validate($name, $type);
        if ($errors) return ['success' => false, 'errors' => $errors];

        $ok = $this->category->create(trim($name), $type);
        return $ok
            ? ['success' => true,  'message' => 'Category created.']
            : ['success' => false, 'message' => 'Failed to create category.'];
    }

    public function update(int $id, string $name, string $type): array
    {
        $errors = $this->validate($name, $type);
        if ($errors) return ['success' => false, 'errors' => $errors];

        $ok = $this->category->update($id, trim($name), $type);
        return $ok
            ? ['success' => true,  'message' => 'Category updated.']
            : ['success' => false, 'message' => 'Failed to update category.'];
    }

    public function destroy(int $id): array
    {
        return $this->category->delete($id);
    }

    private function validate(string $name, string $type): array
    {
        $errors = [];
        if (empty(trim($name)))   $errors[] = 'Category name is required.';
        if (!in_array($type, ['income','expense']))
            $errors[] = 'Type must be income or expense.';
        return $errors;
    }
}
