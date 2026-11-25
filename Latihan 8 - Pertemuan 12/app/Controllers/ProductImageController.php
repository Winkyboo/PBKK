<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;

class ProductImageController extends BaseController
{
    public function show(string $filename)
    {
        if (! preg_match('/^[a-zA-Z0-9_-]+\.(?:jpg|jpeg|png|webp)$/i', $filename)) {
            throw PageNotFoundException::forPageNotFound();
        }

        $path = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'products' . DIRECTORY_SEPARATOR . $filename;
        if (! is_file($path) || ! is_readable($path)) {
            throw PageNotFoundException::forPageNotFound();
        }

        $content = file_get_contents($path);
        if ($content === false) {
            throw PageNotFoundException::forPageNotFound();
        }

        $mime = mime_content_type($path) ?: 'application/octet-stream';

        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setBody($content);
    }
}
