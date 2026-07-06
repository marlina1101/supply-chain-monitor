@extends('admin.layouts.app')
@section('page-title', 'Manajemen User')
@section('content')

<div class="row g-3">
    {{-- Form Tambah User --}}
    <div class="col-md-4">
        <div class="section-card">
            <div class="section-title">➕ Tambah User Baru</div>
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <div class="mb-2">
                    <label class="form-label small fw-bold">Nama</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-bold">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-bold">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Role</label>
                    <select name="role" class="form-select">
                        <option value="user">User (Perusahaan)</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-person-plus"></i> Tambah User
                </button>
            </form>
        </div>
    </div>

    {{-- Daftar User --}}
    <div class="col-md-8">
        <div class="section-card">
            <div class="section-title">👥 Daftar User ({{ count($users) }})</div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $i => $u)
                        <tr>
                            <td class="text-muted">{{ $i + 1 }}</td>
                            <td class="fw-bold">{{ $u->name }}</td>
                            <td class="small">{{ $u->email }}</td>
                            <td>
                                <span class="badge {{ $u->role === 'admin' ? 'bg-danger' : 'bg-primary' }}">
                                    {{ ucfirst($u->role) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ ($u->is_active ?? true) ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ($u->is_active ?? true) ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    {{-- Toggle Aktif --}}
                                    <form method="POST"
                                          action="{{ route('admin.users.toggle', $u->id) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                                class="btn btn-sm {{ ($u->is_active ?? true) ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                                title="{{ ($u->is_active ?? true) ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            <i class="bi bi-{{ ($u->is_active ?? true) ? 'pause' : 'play' }}"></i>
                                        </button>
                                    </form>

                                    {{-- Edit --}}
                                    <button class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editModal{{ $u->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    {{-- Hapus --}}
                                    @if($u->id !== auth()->id())
                                    <form method="POST"
                                          action="{{ route('admin.users.delete', $u->id) }}"
                                          onsubmit="return confirm('Hapus user ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- Modal Edit --}}
                        <div class="modal fade" id="editModal{{ $u->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit User — {{ $u->name }}</h5>
                                        <button type="button" class="btn-close"
                                                data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST"
                                          action="{{ route('admin.users.update', $u->id) }}">
                                        @csrf @method('PUT')
                                        <div class="modal-body">
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Nama</label>
                                                <input type="text" name="name"
                                                       class="form-control"
                                                       value="{{ $u->name }}" required>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Email</label>
                                                <input type="email" name="email"
                                                       class="form-control"
                                                       value="{{ $u->email }}" required>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">
                                                    Password Baru (kosongkan jika tidak diubah)
                                                </label>
                                                <input type="password" name="password"
                                                       class="form-control">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Role</label>
                                                <select name="role" class="form-select">
                                                    <option value="user"
                                                        {{ $u->role === 'user' ? 'selected' : '' }}>
                                                        User
                                                    </option>
                                                    <option value="admin"
                                                        {{ $u->role === 'admin' ? 'selected' : '' }}>
                                                        Admin
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">
                                                Simpan Perubahan
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection