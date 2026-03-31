<?php

namespace App\Rules;

use App\Models\User;
use App\Models\Vendor;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueEmailAcrossGuards implements ValidationRule
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
        if (empty($value)) {
            return;
        }

        $userQuery = Vendor::withTrashed()->where('email', $value);
        if ($this->ignoreModel === Vendor::class && $this->ignoreId) {
            $userQuery->where('id', '!=', $this->ignoreId);
        }

        $employeeQuery = User::withTrashed()->where('email', $value);
        if ($this->ignoreModel === User::class && $this->ignoreId) {
            $employeeQuery->where('id', '!=', $this->ignoreId);
        }

        if ($userQuery->exists() || $employeeQuery->exists()) {
            $fail(__('validation.unique_email_across_guards'));
        }
    }
}

