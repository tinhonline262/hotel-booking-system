<?php

namespace App\Application\Validators;

class RoomValidator
{
    public function validate(array $data): array
    {
        $errors = [];

        // Name validation
        if (empty($data['room_number'])) {
            $errors['room_number'] = 'Room number is required';
        } elseif (strlen($data['room_number']) < 1) {
            $errors['room_number'] = 'Room number must be at least 1 characters';
        } elseif (strlen($data['room_number']) > 100) {
            $errors['room_number'] = 'Room number must not exceed 100 characters';
        }

        // Description validation
        if (empty($data['status'])) {
            $errors['status'] = 'status is required';
        } elseif ($data['status']!='available' && $data['status']!='occupied' && $data['status']!='cleaning') {
            $errors['status'] = 'status is available or occupied or cleaning';
        }
        // Capacity validation
        return $errors;
    }

    public function validateUpdate(array $data, int $id): array
    {
        $errors = $this->validate($data);

        if ($id <= 0) {
            $errors['id'] = 'Invalid room ID';
        }

        return $errors;
    }
}