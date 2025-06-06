<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait KeuanganManagementTrait
{
    /**
     * Scope untuk menampilkan hanya yang aktif
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk menampilkan hanya yang tidak aktif
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    /**
     * Toggle status aktif
     */
    public function toggleActive(): bool
    {
        $this->is_active = !$this->is_active;
        return $this->save();
    }

    /**
     * Aktivasi entitas
     */
    public function activate(): bool
    {
        $this->is_active = true;
        return $this->save();
    }

    /**
     * Deaktivasi entitas
     */
    public function deactivate(): bool
    {
        $this->is_active = false;
        return $this->save();
    }

    /**
     * Get status badge untuk UI
     */
    public function getStatusBadgeAttribute(): string
    {
        return $this->is_active ? 
            '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>' :
            '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Tidak Aktif</span>';
    }

    /**
     * Get status text
     */
    public function getStatusTextAttribute(): string
    {
        return $this->is_active ? 'Aktif' : 'Tidak Aktif';
    }

    /**
     * Search scope untuk pencarian umum
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        // Default search implementation - bisa di-override di model
        return $query->where(function($q) use ($search) {
            $searchableFields = $this->getSearchableFields();
            
            foreach ($searchableFields as $index => $field) {
                if ($index === 0) {
                    $q->where($field, 'like', "%{$search}%");
                } else {
                    $q->orWhere($field, 'like', "%{$search}%");
                }
            }
        });
    }

    /**
     * Get searchable fields - harus di-implement di model
     */
    abstract protected function getSearchableFields(): array;

    /**
     * Format currency untuk display
     */
    public function formatCurrency($amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    /**
     * Get created date in Indonesian format
     */
    public function getFormattedCreatedAtAttribute(): string
    {
        return $this->created_at ? $this->created_at->format('d/m/Y H:i') : '-';
    }

    /**
     * Get updated date in Indonesian format
     */
    public function getFormattedUpdatedAtAttribute(): string
    {
        return $this->updated_at ? $this->updated_at->format('d/m/Y H:i') : '-';
    }

    /**
     * Get age in days
     */
    public function getAgeInDaysAttribute(): int
    {
        return $this->created_at ? $this->created_at->diffInDays(now()) : 0;
    }

    /**
     * Check if entity is recently created (within last 7 days)
     */
    public function getIsRecentAttribute(): bool
    {
        return $this->age_in_days <= 7;
    }

    /**
     * Audit log helper
     */
    protected function logActivity(string $action, array $data = []): void
    {
        // Implementation untuk audit log
        // Bisa menggunakan package seperti spatie/laravel-activitylog
        if (class_exists('\Spatie\Activitylog\Traits\LogsActivity')) {
            activity()
                ->performedOn($this)
                ->withProperties($data)
                ->log($action);
        }
    }

    /**
     * Boot trait
     */
    protected static function bootKeuanganManagementTrait(): void
    {
        static::creating(function ($model) {
            // Set default values
            if (!isset($model->is_active)) {
                $model->is_active = true;
            }
        });

        static::created(function ($model) {
            $model->logActivity('created');
        });

        static::updated(function ($model) {
            $model->logActivity('updated', $model->getChanges());
        });

        static::deleted(function ($model) {
            $model->logActivity('deleted');
        });
    }
}
