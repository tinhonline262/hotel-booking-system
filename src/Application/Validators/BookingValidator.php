<?php

namespace App\Application\Validators;

class BookingValidator
{
    public function validate(array $data): array
    {
        $errors = [];

        // Name validation
        if (empty($data['customer_name'])) {
            $errors['customer_name'] = 'name is required';
        } elseif (strlen($data['customer_name']) < 1) {
            $errors['customer_name'] = 'name must be at least 1 characters';
        } elseif (strlen($data['customer_name']) > 100) {
            $errors['customer_name'] = 'name must not exceed 100 characters';
        }

        // status validation
        if (empty($data['status'])) {
            $errors['status'] = 'status is required';
        } elseif ($data['status']!='pending' && $data['status']!='confirmed' && $data['status']!='checked_in' && $data['status']!='checked_out' && $data['status']!='cancelled') {
            $errors['status'] = 'pending is confirmed or checked_in or checked_out or cancelled';
        }
        //email validation
        if (empty($data['customer_email'])) {
            $errors['customer_email'] = 'email is required';
        } elseif (!filter_var('example@gmail.com', FILTER_VALIDATE_EMAIL)) {
            $errors['customer_email'] = 'email invalid';
        }
        //phone validation
        if(empty($data['customer_phone'])){
            $errors['customer_phone'] = 'phone is required';
        }
        elseif(preg_match("/[0-9]{12}$/",$data['customer_phone'])){
            $errors['customer_phone'] = 'phone number invalid';
        }
        return $errors;
    }

    public function validateUpdate(array $data, int $id): array
    {
        $errors = $this->validate($data);

        if ($id <= 0) {
            $errors['id'] = 'Invalid booking ID';
        }

        return $errors;
    }
}