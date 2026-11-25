<?php

if (! function_exists('shop_product_image_url')) {
    function shop_product_image_url(string $image): string
    {
        if (str_starts_with($image, 'uploads/products/')) {
            return url_to('product.image', basename($image));
        }

        return base_url($image);
    }
}
