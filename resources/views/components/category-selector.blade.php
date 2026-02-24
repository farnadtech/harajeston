@props(['selected' => null, 'name' => 'category_id'])

@php
    $componentId = 'category-selector-' . uniqid();
    $selectedCategoryId = old($name, $selected);
@endphp

<div class="category-selector space-y-4" id="{{ $componentId }}">
    {{-- مرحله 1: انتخاب دسته اصلی --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
            <span class="flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">category</span>
                دسته اصلی
            </span>
        </label>
        <select id="{{ $componentId }}-parent" 
                name="parent_category"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
            <option value="">انتخاب دسته اصلی</option>
            @foreach($categories as $parent)
                <option value="{{ $parent['id'] }}">{{ $parent['name'] }}</option>
            @endforeach
        </select>
    </div>
    
    {{-- مرحله 2: انتخاب زیردسته --}}
    <div id="{{ $componentId }}-child-container" style="display: none;">
        <label class="block text-sm font-medium text-gray-700 mb-2">
            <span class="flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">subdirectory_arrow_right</span>
                زیردسته
            </span>
        </label>
        <select id="{{ $componentId }}-child" 
                name="child_category"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
            <option value="">انتخاب زیردسته</option>
        </select>
    </div>
    
    {{-- مرحله 3: انتخاب دسته نهایی --}}
    <div id="{{ $componentId }}-grand-container" style="display: none;">
        <label class="block text-sm font-medium text-gray-700 mb-2">
            <span class="flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">subdirectory_arrow_right</span>
                دسته نهایی <span class="text-red-500">*</span>
            </span>
        </label>
        <select id="{{ $componentId }}-grand" 
                name="final_category"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
            <option value="">انتخاب دسته نهایی (الزامی)</option>
        </select>
        <p class="text-sm text-amber-600 mt-2 flex items-center gap-1">
            <span class="material-symbols-outlined text-base">warning</span>
            لطفا دسته نهایی را انتخاب کنید
        </p>
    </div>
    
    {{-- فیلد مخفی برای ارسال --}}
    <input type="hidden" 
           name="{{ $name ?? 'category_id' }}" 
           id="{{ $componentId }}-final">
    
    {{-- نمایش مسیر انتخاب شده --}}
    <div id="{{ $componentId }}-path" style="display: none;" class="bg-primary/5 border border-primary/20 rounded-lg p-3">
        <p class="text-sm text-gray-700 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-base">check_circle</span>
            <span class="font-medium">دسته انتخاب شده:</span>
            <span id="{{ $componentId }}-path-text" class="text-primary"></span>
        </p>
    </div>
    
    @error($name ?? 'category_id')
        <div class="text-red-500 text-sm mt-1 flex items-center gap-1">
            <span class="material-symbols-outlined text-base">error</span>
            {{ $message }}
        </div>
    @enderror
</div>

<script>
(function() {
    const componentId = '{{ $componentId }}';
    const categories = @json($categories);
    
    console.log('✓ Category selector script loaded for:', componentId);
    console.log('Categories count:', categories.length);
    
    let state = {
        selectedParent: '',
        selectedChild: '',
        selectedFinal: '',
        children: [],
        grandchildren: []
    };
    
    // Get elements
    const parentSelect = document.getElementById(componentId + '-parent');
    const childContainer = document.getElementById(componentId + '-child-container');
    const childSelect = document.getElementById(componentId + '-child');
    const grandContainer = document.getElementById(componentId + '-grand-container');
    const grandSelect = document.getElementById(componentId + '-grand');
    const finalInput = document.getElementById(componentId + '-final');
    const pathDiv = document.getElementById(componentId + '-path');
    const pathText = document.getElementById(componentId + '-path-text');
    
    // Parent change
    parentSelect.addEventListener('change', function() {
        const parentId = this.value;
        console.log('=== Parent selected:', parentId);
        
        // Reset
        state.selectedChild = '';
        state.selectedFinal = '';
        state.children = [];
        state.grandchildren = [];
        childSelect.innerHTML = '<option value="">انتخاب زیردسته</option>';
        grandSelect.innerHTML = '<option value="">انتخاب دسته نهایی (الزامی)</option>';
        childContainer.style.display = 'none';
        grandContainer.style.display = 'none';
        pathDiv.style.display = 'none';
        finalInput.value = '';
        
        if (!parentId) {
            // Dispatch event with null to clear attributes
            window.dispatchEvent(new CustomEvent('category-selected', { 
                detail: { categoryId: null } 
            }));
            return;
        }
        
        const parent = categories.find(c => c.id == parentId);
        console.log('Found parent:', parent);
        
        if (parent && parent.children) {
            const childrenArray = Array.isArray(parent.children) ? parent.children : Object.values(parent.children);
            console.log('Children count:', childrenArray.length);
            
            if (childrenArray.length > 0) {
                state.children = childrenArray;
                state.children.forEach(child => {
                    const option = document.createElement('option');
                    option.value = child.id;
                    option.textContent = child.name;
                    childSelect.appendChild(option);
                });
                childContainer.style.display = 'block';
                console.log('✓ Children loaded');
            } else {
                // No children, set as final
                finalInput.value = parentId;
                pathText.textContent = parent.name;
                pathDiv.style.display = 'block';
                console.log('✓ No children, set as final');
                
                // Dispatch event for attributes
                window.dispatchEvent(new CustomEvent('category-selected', { 
                    detail: { categoryId: parentId } 
                }));
            }
        }
    });
    
    // Child change
    childSelect.addEventListener('change', function() {
        const childId = this.value;
        console.log('=== Child selected:', childId);
        
        // Reset
        state.selectedFinal = '';
        state.grandchildren = [];
        grandSelect.innerHTML = '<option value="">انتخاب دسته نهایی (الزامی)</option>';
        grandContainer.style.display = 'none';
        pathDiv.style.display = 'none';
        finalInput.value = '';
        
        if (!childId) {
            // Dispatch event with null to clear attributes
            window.dispatchEvent(new CustomEvent('category-selected', { 
                detail: { categoryId: null } 
            }));
            return;
        }
        
        const child = state.children.find(c => c.id == childId);
        console.log('Found child:', child);
        
        if (child) {
            const parent = categories.find(c => c.id == state.selectedParent || c.id == parentSelect.value);
            
            let childChildren = child.children;
            if (childChildren && !Array.isArray(childChildren)) {
                childChildren = Object.values(childChildren);
            }
            console.log('Grandchildren count:', childChildren ? childChildren.length : 0);
            
            if (childChildren && childChildren.length > 0) {
                state.grandchildren = childChildren;
                state.grandchildren.forEach(grand => {
                    const option = document.createElement('option');
                    option.value = grand.id;
                    option.textContent = grand.name;
                    grandSelect.appendChild(option);
                });
                grandContainer.style.display = 'block';
                console.log('✓ Grandchildren loaded');
            } else {
                // No grandchildren, set child as final
                finalInput.value = childId;
                pathText.textContent = parent.name + ' > ' + child.name;
                pathDiv.style.display = 'block';
                console.log('✓ No grandchildren, set as final');
                
                // Dispatch event for attributes
                window.dispatchEvent(new CustomEvent('category-selected', { 
                    detail: { categoryId: childId } 
                }));
            }
        }
    });
    
    // Grand change
    grandSelect.addEventListener('change', function() {
        const grandId = this.value;
        console.log('=== Grand selected:', grandId);
        
        pathDiv.style.display = 'none';
        finalInput.value = '';
        
        if (!grandId) {
            // Dispatch event with null to clear attributes
            window.dispatchEvent(new CustomEvent('category-selected', { 
                detail: { categoryId: null } 
            }));
            return;
        }
        
        const grand = state.grandchildren.find(c => c.id == grandId);
        if (grand) {
            const parent = categories.find(c => c.id == parentSelect.value);
            const child = state.children.find(c => c.id == childSelect.value);
            
            finalInput.value = grandId;
            pathText.textContent = parent.name + ' > ' + child.name + ' > ' + grand.name;
            pathDiv.style.display = 'block';
            console.log('✓ Final category set');
            
            // Dispatch event for attributes
            window.dispatchEvent(new CustomEvent('category-selected', { 
                detail: { categoryId: grandId } 
            }));
        }
    });
    
    console.log('✓ Category selector initialized');
    
    // Initialize with selected category (for edit mode)
    const selectedCategoryId = {{ $selectedCategoryId ?? 'null' }};
    
    if (selectedCategoryId) {
        console.log('→ Initializing with selected category:', selectedCategoryId);
        
        // Find the category path
        function findCategoryPath(catId) {
            for (const parent of categories) {
                if (parent.id == catId) {
                    return { parent: parent.id, child: null, grand: null };
                }
                
                if (parent.children) {
                    const childrenArray = Array.isArray(parent.children) ? parent.children : Object.values(parent.children);
                    for (const child of childrenArray) {
                        if (child.id == catId) {
                            return { parent: parent.id, child: child.id, grand: null };
                        }
                        
                        if (child.children) {
                            let grandArray = Array.isArray(child.children) ? child.children : Object.values(child.children);
                            for (const grand of grandArray) {
                                if (grand.id == catId) {
                                    return { parent: parent.id, child: child.id, grand: grand.id };
                                }
                            }
                        }
                    }
                }
            }
            return null;
        }
        
        const path = findCategoryPath(selectedCategoryId);
        
        if (path) {
            console.log('→ Found category path:', path);
            
            // Set parent
            if (path.parent) {
                parentSelect.value = path.parent;
                parentSelect.dispatchEvent(new Event('change'));
                
                // Wait for children to load, then set child
                if (path.child) {
                    setTimeout(() => {
                        childSelect.value = path.child;
                        childSelect.dispatchEvent(new Event('change'));
                        
                        // Wait for grandchildren to load, then set grand
                        if (path.grand) {
                            setTimeout(() => {
                                grandSelect.value = path.grand;
                                grandSelect.dispatchEvent(new Event('change'));
                            }, 100);
                        }
                    }, 100);
                }
            }
        }
    }
})();
</script>
