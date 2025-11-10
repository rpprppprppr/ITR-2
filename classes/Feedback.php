<?php

class Feedback {
    public int $id;
    public User $user;
    public string $message;
    public string $created_at;

    public function __construct(User $user, string $message) {
        $this->user = $user;
        $this->message = $message;
        $this->created_at = date('c');
    }

    public function submit(): void {
        // имитация отправки
    }

    public function validate(): bool {
        return !empty($this->message);
    }
}

class ProductReview extends Feedback {
    public Product $product;
    public int $rating;

    public function __construct(User $user, Product $product, string $message, int $rating) {
        parent::__construct($user, $message);
        $this->product = $product;
        $this->rating = $rating;
    }
}

class GeneralFeedback extends Feedback {}