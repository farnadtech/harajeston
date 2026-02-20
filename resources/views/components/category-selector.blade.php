<div class="category-selector">
    <label class="form-label">{{ $label ?? 'دسته‌بندی' }} *</label>
    <select name="{{ $name ?? 'category_id' }}" 
            id="categorySelect"
            class="form-select @error($name ?? 'category_id') is-invalid @enderror" 
            required>
        <option value="">انتخاب دسته‌بندی</option>
        @foreach($categories as $parent)
            <optgroup label="{{ $parent->name }}">
                @if($parent->children && count($parent->children) > 0)
                    @foreach($parent->children as $child)
                        <option value="{{ $child->id }}" 
                                data-parent="{{ $parent->name }}"
                                {{ old($name ?? 'category_id', $selected ?? null) == $child->id ? 'selected' : '' }}>
                            {{ $child->name }}
                        </option>
                    @endforeach
                @else
                    <option value="" disabled class="text-gray-400">
                        این دسته زیردسته ندارد
                    </option>
                @endif
            </optgroup>
        @endforeach
    </select>
    <p class="text-xs text-gray-500 mt-1">فقط زیردسته‌ها قابل انتخاب هستند</p>
    @error($name ?? 'category_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
