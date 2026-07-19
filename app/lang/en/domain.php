<?php

declare(strict_types=1);

return [
    'auth' => [
        'email_already_registered' => 'The email address ":email" is already registered.',
        'invalid_credentials' => 'Invalid credentials.',
        'password_confirmation_mismatch' => 'Password confirmation does not match.',
    ],
    'cart' => [
        'insufficient_stock' => 'Only :available item(s) are available, requested quantity is :requested.',
        'invalid_quantity' => 'Cart quantity must be greater than zero.',
        'product_not_purchasable' => 'Product is not available for purchase.',
    ],
    'inventory' => [
        'insufficient_available_stock' => 'Insufficient available stock. Available: :available, requested: :requested.',
        'insufficient_reserved_stock' => 'Insufficient reserved stock. Reserved: :reserved, requested: :requested.',
        'invalid_quantity' => 'Stock quantity must be greater than zero.',
    ],
    'media' => [
        'invalid_type' => 'Unsupported media mime type: :mime.',
        'too_large' => 'Media file exceeds max size of :maxBytes bytes.',
    ],
    'order' => [
        'auth_required' => 'Checkout requires an authenticated customer.',
        'empty_cart' => 'Cart is empty.',
        'invalid_status' => 'Invalid order status: :status.',
        'invalid_status_transition' => 'Invalid order status transition: :from -> :to',
        'invalid_payment_status' => 'Invalid payment status: :status.',
    ],
    'payment' => [
        'invalid_webhook' => ':message',
    ],
    'shared' => [
        'authorization_denied' => 'This action is not authorized.',
        'missing_self_constructing_request' => 'Request ":class" must implement createFromArray().',
    ],
    'user' => [
        'current_password_mismatch' => 'Current password is not valid.',
        'email_already_taken' => 'Email address ":email" is already taken.',
        'invalid_address_type' => 'Invalid address type ":type".',
        'password_confirmation_mismatch' => 'Password confirmation does not match.',
    ],
];
