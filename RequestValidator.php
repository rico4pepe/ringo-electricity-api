<?php

Class RequestValidator{

    public function validate(array $data): bool{

        if (empty($data['product_id']) || empty($data['mobile']) || empty($data['amount']) || empty($data['opt3'])) {
            return false;
        }

        if (!is_numeric($data['amount']) || !is_string($data['mobile'])) {
            return false;
        }

        return true;
    }
}