<?php

namespace App\Traits;

use App\Models\EditHistory;
use Illuminate\Support\Facades\Auth;

trait HasEditHistory
{
    protected static function bootHasEditHistory()
    {
        static::created(function ($model) {
            $model->logEditHistory('created');
        });

        static::updated(function ($model) {
            $model->logEditHistory('updated');
        });

        static::deleted(function ($model) {
            $model->logEditHistory('deleted');
        });
    }

    protected function logEditHistory($action)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                \Log::warning('EditHistory: No authenticated user');
                return; // Don't log if no authenticated user
            }

            $changes = null;
            $description = null;

            if ($action === 'created') {
                $description = 'Created ' . strtolower(class_basename($this));
                $changes = [
                    'new' => $this->getAttributes(),
                ];
            } elseif ($action === 'updated') {
                $changedAttributes = $this->getDirty();
                if (empty($changedAttributes)) {
                    \Log::info('EditHistory: No changes detected for ' . class_basename($this) . ' ID: ' . $this->id);
                    return; // Don't log if nothing changed
                }

                $original = $this->getOriginal();
                $changes = [
                    'old' => array_intersect_key($original, $changedAttributes),
                    'new' => $changedAttributes,
                ];

                // Group fields into semantic categories
                $ppaFields = ['ppa_id', 'indicator_id', 'ppa_name', 'name', 'types_id', 'record_type_id', 'ppa_details_id'];
                $officeFields = ['office_id'];
                $dataFields = ['universe', 'accomplishment', 'targets', 'years', 'remarks'];

                $changedCategories = [];
                foreach (array_keys($changedAttributes) as $field) {
                    if (in_array($field, $ppaFields)) {
                        $changedCategories['ppa'] = true;
                    } elseif (in_array($field, $officeFields)) {
                        $changedCategories['office'] = true;
                    } elseif (in_array($field, $dataFields)) {
                        $changedCategories['data'] = true;
                    }
                }

                $descriptions = [];
                if (isset($changedCategories['ppa'])) {
                    $descriptions[] = 'PPA details';
                }
                if (isset($changedCategories['office'])) {
                    $descriptions[] = 'office assignment';
                }
                if (isset($changedCategories['data'])) {
                    $descriptions[] = 'data';
                }

                if (!empty($descriptions)) {
                    $description = 'Edited the ' . implode(', ', $descriptions);
                } else {
                    $description = 'Edited ' . strtolower(class_basename($this));
                }
            } elseif ($action === 'deleted') {
                $description = 'Deleted ' . strtolower(class_basename($this));
                $changes = [
                    'deleted' => $this->getAttributes(),
                ];
            }

            EditHistory::create([
                'user_id' => $user->id,
                'model_type' => get_class($this),
                'model_id' => $this->id,
                'action' => $action,
                'changes' => $changes,
                'description' => $description,
            ]);

            \Log::info('EditHistory: Logged ' . $action . ' for ' . class_basename($this) . ' ID: ' . $this->id);
        } catch (\Exception $e) {
            \Log::error('EditHistory: Error logging edit history - ' . $e->getMessage());
            \Log::error('EditHistory: Stack trace - ' . $e->getTraceAsString());
        }
    }
}
