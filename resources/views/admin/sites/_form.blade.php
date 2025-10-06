{{-- resources/views/admin/sites/_form.blade.php --}}
@csrf
<div class="grid gap-4 sm:grid-cols-2">

  <div>
    <label class="text-sm font-medium text-slate-700">Code<span class="text-red-500">*</span></label>
    <input type="text" name="code" value="{{ old('code', $site->code ?? '') }}"
           class="mt-1 block w-full rounded-lg border-slate-300" maxlength="20" required>
  </div>

  <div>
    <label class="text-sm font-medium text-slate-700">Name<span class="text-red-500">*</span></label>
    <input type="text" name="name" value="{{ old('name', $site->name ?? '') }}"
           class="mt-1 block w-full rounded-lg border-slate-300" required>
  </div>

  <div>
    <label class="text-sm font-medium text-slate-700">Region</label>
    <input type="text" name="region" value="{{ old('region', $site->region ?? '') }}"
           class="mt-1 block w-full rounded-lg border-slate-300">
  </div>

  <div>
    <label class="text-sm font-medium text-slate-700">Address</label>
    <input type="text" name="address" value="{{ old('address', $site->address ?? '') }}"
           class="mt-1 block w-full rounded-lg border-slate-300">
  </div>

  <div>
    <label class="text-sm font-medium text-slate-700">Latitude</label>
    <input type="number" step="0.0000001" name="lat" value="{{ old('lat', $site->lat ?? '') }}"
           class="mt-1 block w-full rounded-lg border-slate-300" min="-90" max="90">
  </div>

  <div>
    <label class="text-sm font-medium text-slate-700">Longitude</label>
    <input type="number" step="0.0000001" name="lng" value="{{ old('lng', $site->lng ?? '') }}"
           class="mt-1 block w-full rounded-lg border-slate-300" min="-180" max="180">
  </div>

  <div class="sm:col-span-2">
    <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700">
      <input type="hidden" name="is_active" value="0">
      <input type="checkbox" name="is_active" value="1"
             @checked(old('is_active', ($site->is_active ?? true)) )>
      Aktif
    </label>
  </div>

  {{-- Config JSON (opsional) --}}
  <div class="sm:col-span-2">
    <label class="text-sm font-medium text-slate-700">Config (JSON)</label>
    <textarea name="config" rows="4" class="mt-1 block w-full rounded-lg border-slate-300"
      placeholder='{"hba": "...", "thresholds": {"fuel":"..."}}'>{{ old('config', isset($site->config) ? json_encode($site->config, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) : '') }}</textarea>
    <p class="text-xs text-slate-500 mt-1">Kosongkan jika tidak perlu.</p>
  </div>
</div>

{{-- Errors --}}
@if ($errors->any())
  <div class="mt-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-red-800 text-sm">
    <ul class="list-disc list-inside">
      @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
    </ul>
  </div>
@endif
