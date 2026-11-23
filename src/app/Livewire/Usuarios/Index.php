<?php

namespace App\Livewire\Usuarios;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $selectedRole = '';

    // Modal state
    public bool $showingModal = false;
    public ?int $editingUserId = null;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $role = 'Operador';
    public bool $activo = true;

    public function mount(): void
    {
        abort_unless(auth()->user()->hasRole('Administrador'), 403);
    }

    protected function rules(): array
    {
        $userId = $this->editingUserId;
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . ($userId ?: 'NULL'),
            'password' => $userId ? 'nullable|min:6' : 'required|min:6',
            'role' => 'required|exists:roles,name',
            'activo' => 'boolean',
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedRole(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->reset(['editingUserId', 'name', 'email', 'password', 'role', 'activo']);
        $this->role = 'Operador';
        $this->activo = true;
        $this->resetValidation();
        $this->showingModal = true;
    }

    public function openEditModal(int $id): void
    {
        $user = User::findOrFail($id);
        $this->editingUserId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->role = $user->roles->first()?->name ?? 'Operador';
        $this->activo = (bool) $user->activo;
        $this->resetValidation();
        $this->showingModal = true;
    }

    public function save(): void
    {
        $this->validate();

        if ($this->editingUserId) {
            $user = User::findOrFail($this->editingUserId);
            $data = [
                'name' => $this->name,
                'email' => $this->email,
                'activo' => $this->activo,
            ];
            if (!empty($this->password)) {
                $data['password'] = Hash::make($this->password);
            }
            $user->update($data);
            $user->syncRoles([$this->role]);

            AuditLog::log('actualizar_usuario', 'User', $user->id, "Usuario actualizado: {$user->email}");
            session()->flash('message', 'Usuario actualizado exitosamente.');
        } else {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'activo' => $this->activo,
            ]);
            $user->syncRoles([$this->role]);

            AuditLog::log('crear_usuario', 'User', $user->id, "Nuevo usuario creado: {$user->email}");
            session()->flash('message', 'Usuario creado exitosamente.');
        }

        $this->showingModal = false;
    }

    public function toggleActivo(int $id): void
    {
        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) {
            session()->flash('error', 'No puedes desactivar tu propia cuenta.');
            return;
        }

        $user->activo = !$user->activo;
        $user->save();

        $estado = $user->activo ? 'activado' : 'desactivado';
        AuditLog::log('cambio_estado_usuario', 'User', $user->id, "Usuario {$user->email} {$estado}");
        session()->flash('message', "Usuario {$estado} correctamente.");
    }

    public function render()
    {
        $query = User::with('roles')
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->when($this->selectedRole, function ($q) {
                $q->role($this->selectedRole);
            })
            ->orderBy('id', 'asc');

        $users = $query->paginate(10);
        $roles = Role::all();

        return view('livewire.usuarios.index', [
            'users' => $users,
            'roles' => $roles,
        ])->layout('layouts.app');
    }
}
