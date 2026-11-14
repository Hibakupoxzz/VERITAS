{{--
    Form bersama untuk Tambah & Edit Akun.
    Variabel: $account (boleh null), $roleOptions, $accessOptions,
              $action, $method, $submitLabel.
--}}

<form action="{{ $action }}" method="POST">
    @csrf
    @if(! empty($method))
        @method($method)
    @endif

    <div class="pv-card">
        <div style="padding: var(--sp-9);">

            <div class="pv-section-label">
                <i class="fa-solid fa-id-card"></i>
                Identitas Akun
            </div>

            <div class="form-row">
                <div class="pv-field">
                    <label class="pv-label" for="name">Nama Lengkap <span class="req">*</span></label>
                    <input type="text" id="name" name="name" class="pv-input"
                           value="{{ old('name', $account?->name) }}" required>
                </div>

                <div class="pv-field">
                    <label class="pv-label" for="email">Email <span class="req">*</span></label>
                    <input type="email" id="email" name="email" class="pv-input"
                           value="{{ old('email', $account?->email) }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="pv-field">
                    <label class="pv-label" for="nig">NIG</label>
                    <input type="text" id="nig" name="nig" class="pv-input"
                           value="{{ old('nig', $account?->nig) }}"
                           inputmode="numeric" autocomplete="off"
                           placeholder="Kosongkan bila belum ada">
                    <p class="form-help">Dipakai untuk login. Contoh: 0170194016.</p>
                </div>

                <div class="pv-field">
                    <label class="pv-label" for="jabatan">Jabatan</label>
                    <input type="text" id="jabatan" name="jabatan" class="pv-input"
                           value="{{ old('jabatan', $account?->jabatan) }}"
                           placeholder="mis. Waka Kesiswaan">
                </div>
            </div>

            <div class="form-row">
                <div class="pv-field">
                    <label class="pv-label" for="role">Role <span class="req">*</span></label>
                    <select id="role" name="role" class="pv-select" required>
                        @foreach($roleOptions as $value => $label)
                            <option value="{{ $value }}"
                                @selected(old('role', $account?->role) === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="pv-field">
                    <label class="pv-label" for="kelas">Kelas</label>
                    <input type="text" id="kelas" name="kelas" class="pv-input"
                           value="{{ old('kelas', $account?->kelas) }}"
                           placeholder="Khusus Wali Kelas / kelas binaan">
                </div>
            </div>

            <div class="pv-field">
                <label class="pv-label" for="access_level">Hak Akses Tambahan</label>
                <select id="access_level" name="access_level" class="pv-select">
                    @foreach($accessOptions as $value => $label)
                        <option value="{{ $value }}"
                            @selected((string) old('access_level', $account?->access_level ?? '') === (string) $value)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                <p class="form-help">
                    &ldquo;Akses Penuh&rdquo; membuka seluruh fitur, setara Super Admin,
                    termasuk Manajemen Akun (mis. untuk Wakil Kepala Kesiswaan).
                </p>
            </div>

        </div>
    </div>

    <div class="pv-card">
        <div style="padding: var(--sp-9);">

            <div class="pv-section-label">
                <i class="fa-solid fa-key"></i>
                Keamanan
            </div>

            <div class="pv-field" style="max-width: 420px;">
                <label class="pv-label" for="password">
                    Password
                    @if($account)
                        <span style="font-weight:400;color:var(--c-text-muted);">(kosongkan bila tidak diubah)</span>
                    @else
                        <span class="req">*</span>
                    @endif
                </label>
                <input type="password" id="password" name="password" class="pv-input"
                       autocomplete="new-password"
                       {{ $account ? '' : 'required' }}
                       minlength="6">
                <p class="form-help">Minimal 6 karakter.</p>
            </div>

        </div>
    </div>

    <div class="form-footer">
        <a href="{{ route('account.index') }}" class="btn btn-secondary">
            Batal
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ $submitLabel }}
        </button>
    </div>
</form>
