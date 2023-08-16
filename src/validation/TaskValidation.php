<?php

namespace App\validation;

class TaskValidation
{
    public static function validateTaskData($data): array
    {
        $errors = [];

        if (empty($data['title'])) {
            $errors['title'] = 'Title is required';
        }

        if (empty($data['description'])) {
            $errors['description'] = 'Description is required';
        }

        if (empty($data['status'])) {
            $errors['status'] = 'Status is required';
        } elseif (!in_array($data['status'], ['todo', 'in_progress', 'done'])) {
            $errors['status'] = 'Invalid status';
        }

        return $errors;
    }
}
