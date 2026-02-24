<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['listing' => null]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['listing' => null]); ?>
<?php foreach (array_filter((['listing' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<div id="attributesSection">
    <!-- پیام راهنما قبل از انتخاب دسته -->
    <div id="noCategory" class="bg-blue-50 border border-blue-200 rounded-lg p-6 text-center" style="<?php echo e($listing && $listing->category_id ? 'display: none;' : ''); ?>">
        <div class="flex flex-col items-center gap-3">
            <span class="material-symbols-outlined text-blue-600 text-4xl">info</span>
            <div>
                <p class="text-base font-medium text-blue-900 mb-1">ویژگی‌های فنی محصول</p>
                <p class="text-sm text-blue-700">
                    برای نمایش فیلدهای ویژگی‌های فنی، ابتدا دسته‌بندی محصول را انتخاب کنید
                </p>
            </div>
        </div>
    </div>
    
    <!-- بارگذاری -->
    <div id="loading" class="text-center py-8" style="display: none;">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-primary border-t-transparent"></div>
        <p class="text-sm text-gray-600 mt-2">در حال بارگذاری ویژگی‌ها...</p>
    </div>
    
    <!-- ویژگی‌ها -->
    <div id="attributesContainer" class="space-y-4" style="display: none;"></div>
    
    <!-- پیام عدم وجود ویژگی -->
    <div id="noAttributes" class="bg-gray-50 border border-gray-200 rounded-lg p-6 text-center" style="display: none;">
        <span class="material-symbols-outlined text-gray-400 text-3xl">check_circle</span>
        <p class="text-sm text-gray-600 mt-2">این دسته‌بندی ویژگی فنی خاصی ندارد</p>
    </div>
</div>

<script>
(function() {
    console.log('✓ Attributes component loaded');
    
    // HTML escape function
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Existing attribute values (for edit mode)
    const existingValues = <?php echo json_encode($listing && $listing->relationLoaded('attributeValues') ? $listing->attributeValues->pluck('value', 'category_attribute_id')->toArray() : [], 512) ?>;
    console.log('→ Existing attribute values:', existingValues);
    console.log('→ Listing has attributeValues:', <?php echo e($listing && $listing->relationLoaded('attributeValues') ? 'true' : 'false'); ?>);
    console.log('→ AttributeValues count:', <?php echo e($listing && $listing->relationLoaded('attributeValues') ? $listing->attributeValues->count() : 0); ?>);
    
    // Initial category (for edit mode)
    const initialCategoryId = <?php echo e($listing && $listing->category_id ? $listing->category_id : 'null'); ?>;
    console.log('→ Initial category ID:', initialCategoryId);
    
    // Elements
    const noCategory = document.getElementById('noCategory');
    const loading = document.getElementById('loading');
    const attributesContainer = document.getElementById('attributesContainer');
    const noAttributes = document.getElementById('noAttributes');
    
    // Hide all sections
    function hideAll() {
        noCategory.style.display = 'none';
        loading.style.display = 'none';
        attributesContainer.style.display = 'none';
        noAttributes.style.display = 'none';
    }
    
    // Show specific section
    function show(element) {
        hideAll();
        element.style.display = 'block';
    }
    
    // Load attributes
    function loadAttributes(categoryId) {
        console.log('→ Loading attributes for category:', categoryId);
        
        show(loading);
        
        // استفاده از Laravel URL helper
        const apiUrl = '<?php echo e(url("/api/categories")); ?>/' + categoryId + '/attributes';
        
        console.log('→ API URL:', apiUrl);
        
        fetch(apiUrl)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('✓ Attributes received:', data);
                
                if (data.attributes && data.attributes.length > 0) {
                    // Render attributes
                    attributesContainer.innerHTML = '';
                    data.attributes.forEach(attr => {
                        const div = document.createElement('div');
                        const existingValue = existingValues[attr.id] || '';
                        
                        console.log(`→ Attribute ${attr.id} (${attr.name}): existing value = "${existingValue}"`);
                        
                        div.innerHTML = `
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                ${escapeHtml(attr.name)}
                                ${attr.is_required ? '<span class="text-red-500">*</span>' : ''}
                            </label>
                        `;
                        
                        let inputElement;
                        if (attr.type === 'select' && attr.options) {
                            inputElement = document.createElement('select');
                            inputElement.name = `attributes[${attr.id}]`;
                            inputElement.className = 'w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors';
                            if (attr.is_required) inputElement.required = true;
                            
                            // Add empty option
                            const emptyOpt = document.createElement('option');
                            emptyOpt.value = '';
                            emptyOpt.textContent = 'انتخاب کنید';
                            inputElement.appendChild(emptyOpt);
                            
                            // Add options
                            attr.options.forEach(opt => {
                                const option = document.createElement('option');
                                option.value = opt;
                                option.textContent = opt;
                                if (existingValue && existingValue === opt) {
                                    option.selected = true;
                                }
                                inputElement.appendChild(option);
                            });
                        } else if (attr.type === 'number') {
                            inputElement = document.createElement('input');
                            inputElement.type = 'number';
                            inputElement.name = `attributes[${attr.id}]`;
                            inputElement.placeholder = attr.name;
                            inputElement.className = 'w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors';
                            if (attr.is_required) inputElement.required = true;
                            if (existingValue) inputElement.value = existingValue;
                        } else {
                            inputElement = document.createElement('input');
                            inputElement.type = 'text';
                            inputElement.name = `attributes[${attr.id}]`;
                            inputElement.placeholder = attr.name;
                            inputElement.className = 'w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors';
                            if (attr.is_required) inputElement.required = true;
                            if (existingValue) inputElement.value = existingValue;
                        }
                        
                        div.appendChild(inputElement);
                        attributesContainer.appendChild(div);
                    });
                    
                    show(attributesContainer);
                    console.log(`✓ ${data.attributes.length} attributes loaded with values`);
                } else {
                    show(noAttributes);
                    console.log('✓ No attributes for this category');
                }
            })
            .catch(error => {
                console.error('✗ Error loading attributes:', error);
                show(noCategory);
            });
    }
    
    // Clear attributes
    function clearAttributes() {
        console.log('✓ Clearing attributes');
        attributesContainer.innerHTML = '';
        show(noCategory);
    }
    
    // Listen to category-selected event
    window.addEventListener('category-selected', (event) => {
        console.log('✓ Category-selected event received:', event.detail);
        
        const categoryId = event.detail.categoryId;
        
        if (!categoryId) {
            clearAttributes();
        } else {
            loadAttributes(categoryId);
        }
    });
    
    console.log('✓ Event listener registered');
    
    // Load attributes on page load if category is already selected (edit mode)
    if (initialCategoryId) {
        console.log('→ Loading attributes for initial category');
        loadAttributes(initialCategoryId);
    }
})();
</script>
<?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views/components/listing-attributes.blade.php ENDPATH**/ ?>