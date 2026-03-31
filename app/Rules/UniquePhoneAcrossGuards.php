<?php

namespace App\Rules;

use App\Models\User;
use App\Models\Vendor;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniquePhoneAcrossGuards implements ValidationRule
{
    protected ?int $ignoreId;
    protected ?string $ignoreModel;

    public function __construct(?int $ignoreId = null, ?string $ignoreModel = null)
    {
        $this->ignoreId = $ignoreId;
        $this->ignoreModel = $ignoreModel;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value) {
            return;
        }

        $models = [
            Vendor::class,
            User::class,
        ];

        foreach ($models as $model) {
            $query = $model::withTrashed()->where('phone', $value);

            if ($this->ignoreModel === $model && $this->ignoreId) {
                $query->where('id', '!=', $this->ignoreId);
            }

            if ($query->exists()) {
                $fail(__('validation.unique_phone_across_guards'));
                return;
            }
        }
    }
}
