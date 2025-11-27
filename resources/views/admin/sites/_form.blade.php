@php
  // untuk create/edit
  $site = $site ?? null;

  // list companies dari controller
  $companies = $companies ?? collect();

  // selected company default:
  // priority: old() -> data site -> companyId (create) -> session
  $selectedCompanyId =
      old('company_id')
      ?? ($site->company_id ?? null)
      ?? ($companyId ?? null)
      ?? session('company_id');
@endphp

{{-- COMPANY DROPDOWN --}}
<div>
  <label class="text-sm font-semibold text-slate-700">
    Perusahaan <span class="text-rose-600">*</span>
  </label>

  @if($companies->isNotEmpty())
    <select name="company_id" required
            class="mt-1 w-full rounded-2xl border border-sky-200 bg-white
                   px-3 py-2.5 text-sm text-slate-700 shadow-sm
                   focus:outline-none focus:ring-2 focus:ring-sky-300/70 focus:border-sky-400">
      <option value="">-- Pilih Perusahaan --</option>
      @foreach($companies as $c)
        <option value="{{ $c->id }}" @selected($selectedCompanyId===$c->id)>
          {{ $c->code }} — {{ $c->name }}
        </option>
      @endforeach
    </select>
  @else
    {{-- fallback kalau companies tidak dikirim --}}
    <div class="mt-1 text-sm text-rose-700 bg-rose-50 border border-rose-200 rounded-2xl p-3">
      Company belum ada. Buat company dulu lewat menu Admin → Companies.
    </div>
    <input type="hidden" name="company_id" value="{{ $selectedCompanyId }}">
  @endif

  @error('company_id')
    <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
  @enderror
</div>

{{-- CODE --}}
<div>
  <label class="text-sm font-semibold text-slate-700">
    Code <span class="text-rose-600">*</span>
  </label>
  <input type="text" name="code" required
         value="{{ old('code', $site->code ?? '') }}"
         placeholder="contoh: PIT-01 / ABN-SITE"
         class="mt-1 w-full rounded-2xl border border-sky-200 bg-white
                px-4 py-2.5 text-sm text-slate-700 shadow-sm
                focus:outline-none focus:ring-2 focus:ring-sky-300/70 focus:border-sky-400">
  @error('code')
    <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
  @enderror
</div>

{{-- NAME --}}
<div>
  <label class="text-sm font-semibold text-slate-700">
    Nama Site <span class="text-rose-600">*</span>
  </label>
  <input type="text" name="name" required
         value="{{ old('name', $site->name ?? '') }}"
         placeholder="Nama lokasi / plant"
         class="mt-1 w-full rounded-2xl border border-sky-200 bg-white
                px-4 py-2.5 text-sm text-slate-700 shadow-sm
                focus:outline-none focus:ring-2 focus:ring-sky-300/70 focus:border-sky-400">
  @error('name')
    <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
  @enderror
</div>

{{-- REGION --}}
<div>
  <label class="text-sm font-semibold text-slate-700">Region</label>
  <input type="text" name="region"
         value="{{ old('region', $site->region ?? '') }}"
         placeholder="contoh: Kalimantan / Sulawesi"
         class="mt-1 w-full rounded-2xl border border-sky-200 bg-white
                px-4 py-2.5 text-sm text-slate-700 shadow-sm
                focus:outline-none focus:ring-2 focus:ring-sky-300/70 focus:border-sky-400">
  @error('region')
    <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
  @enderror
</div>

{{-- ADDRESS --}}
<div>
  <label class="text-sm font-semibold text-slate-700">Alamat</label>
  <textarea name="address" rows="3"
            placeholder="Alamat lengkap site"
            class="mt-1 w-full rounded-2xl border border-sky-200 bg-white
                   px-4 py-3 text-sm text-slate-700 shadow-sm
                   focus:outline-none focus:ring-2 focus:ring-sky-300/70 focus:border-sky-400">{{ old('address', $site->address ?? '') }}</textarea>
  @error('address')
    <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
  @enderror
</div>

{{-- LAT/LNG --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
  <div>
    <label class="text-sm font-semibold text-slate-700">Latitude</label>
    <input type="number" step="any" name="lat"
           value="{{ old('lat', $site->lat ?? '') }}"
           placeholder="-6.2"
           class="mt-1 w-full rounded-2xl border border-sky-200 bg-white
                  px-4 py-2.5 text-sm text-slate-700 shadow-sm
                  focus:outline-none focus:ring-2 focus:ring-sky-300/70 focus:border-sky-400">
    @error('lat')
      <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
    @enderror
  </div>
  <div>
    <label class="text-sm font-semibold text-slate-700">Longitude</label>
    <input type="number" step="any" name="lng"
           value="{{ old('lng', $site->lng ?? '') }}"
           placeholder="106.8"
           class="mt-1 w-full rounded-2xl border border-sky-200 bg-white
                  px-4 py-2.5 text-sm text-slate-700 shadow-sm
                  focus:outline-none focus:ring-2 focus:ring-sky-300/70 focus:border-sky-400">
    @error('lng')
      <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
    @enderror
  </div>
</div>

{{-- ACTIVE --}}
<div class="flex items-center gap-2 pt-1">
  <input id="is_active" type="checkbox" name="is_active" value="1"
         class="rounded border-slate-300 text-maroon-700 focus:ring-maroon-600/30"
         {{ old('is_active', $site->is_active ?? true) ? 'checked' : '' }}>
  <label for="is_active" class="text-sm font-semibold text-slate-700">
    Aktif
  </label>
  @error('is_active')
    <div class="text-xs text-rose-600">{{ $message }}</div>
  @enderror
</div>

{{-- CONFIG (opsional JSON) --}}
<div>
  <label class="text-sm font-semibold text-slate-700">Config (opsional)</label>
  <textarea name="config" rows="4"
            placeholder='{"key":"value"}'
            class="mt-1 w-full rounded-2xl border border-sky-200 bg-white
                   px-4 py-3 text-xs text-slate-700 shadow-sm font-mono
                   focus:outline-none focus:ring-2 focus:ring-sky-300/70 focus:border-sky-400">{{ old('config', isset($site->config) ? json_encode($site->config, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) : '') }}</textarea>
  <div class="text-xs text-slate-500 mt-1">
    Isi JSON kalau ada konfigurasi tambahan.
  </div>
  @error('config')
    <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
  @enderror
</div>
