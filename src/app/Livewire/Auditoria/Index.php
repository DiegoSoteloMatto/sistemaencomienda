<?php

namespace App\Livewire\Auditoria;

use App\Models\AuditLog;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $actionFilter = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingActionFilter(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $logs = AuditLog::with('user')
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('description', 'like', "%{$this->search}%")
                        ->orWhere('action', 'like', "%{$this->search}%")
                        ->orWhere('ip_address', 'like', "%{$this->search}%")
                        ->orWhereHas('user', function ($u) {
                            $u->where('name', 'like', "%{$this->search}%")
                              ->orWhere('email', 'like', "%{$this->search}%");
                        });
                });
            })
            ->when($this->actionFilter, function ($q) {
                $q->where('action', $this->actionFilter);
            })
            ->orderBy('id', 'desc')
            ->paginate(15);

        $actions = AuditLog::select('action')->distinct()->pluck('action');

        return view('livewire.auditoria.index', [
            'logs' => $logs,
            'actions' => $actions,
        ])->layout('layouts.app');
    }
}
