<div class="mb-3">
    <label>Judul Paket</label>
    <input type="text"
           name="name"
           class="form-control"
           value="{{ old('name', $package->name ?? '') }}"
           required>
</div>

<div class="mb-3">
    <label>Deskripsi</label>
    <textarea name="description"
              class="form-control">{{ old('description', $package->description ?? '') }}</textarea>
</div>


