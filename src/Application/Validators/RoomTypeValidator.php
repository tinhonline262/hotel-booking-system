<?php

namespace App\Application\Validators;

class RoomTypeValidator
{
    public function validate(array $data): array
    {
        $errors = [];

        // Name validation
        if (empty($data['name'])) {
            $errors['name'] = 'Room type name is required';
        } elseif (strlen($data['name']) < 3) {
            $errors['name'] = 'Room type name must be at least 3 characters';
        } elseif (strlen($data['name']) > 100) {
            $errors['name'] = 'Room type name must not exceed 100 characters';
        }

        // Description validation
        if (empty($data['description'])) {
            $errors['description'] = 'Description is required';
        } elseif (strlen($data['description']) < 10) {
            $errors['description'] = 'Description must be at least 10 characters';
        }

        // Capacity validation
        if (empty($data['capacity'])) {
            $errors['capacity'] = 'Capacity is required';
        } elseif (!is_numeric($data['capacity'])) {
            $errors['capacity'] = 'Capacity must be a number';
        } elseif ($data['capacity'] < 1) {
            $errors['capacity'] = 'Capacity must be at least 1';
        } elseif ($data['capacity'] > 10) {
            $errors['capacity'] = 'Capacity must not exceed 10 people';
        }

        // Price validation
        if (empty($data['pricePerNight']) && $data['pricePerNight'] !== 0) {
            $errors['pricePerNight'] = 'Price per night is required';
        } elseif (!is_numeric($data['pricePerNight'])) {
            $errors['pricePerNight'] = 'Price must be a number';
        } elseif ($data['pricePerNight'] < 0) {
            $errors['pricePerNight'] = 'Price must be greater than or equal to 0';
        }

        // Amenities validation
        if (empty($data['amenities'])) {
            $errors['amenities'] = 'At least one amenity is required';
        } elseif (!is_array($data['amenities'])) {
            $errors['amenities'] = 'Amenities must be an array';
        }

        return $errors;
    }

    public function validateUpdate(array $data, int $id): array
    {
        $errors = $this->validate($data);

        if ($id <= 0) {
            $errors['id'] = 'Invalid room type ID';
        }

        return $errors;
    }
}

