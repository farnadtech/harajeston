<div id="attributesSection">
    <!-- پیام راهنما قبل از انتخاب دسته -->
    <div id="noCategory" class="bg-blue-50 border border-blue-200 rounded-lg p-6 text-center">
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
        const apiUrl = '{{ url("/api/categories") }}/' + categoryId + '/attributes';
        
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
                        
                        let inputHtml = '';
                        if (attr.type === 'select' && attr.options) {
                            inputHtml = `
                                <select name="attributes[${attr.id}]" ${attr.is_required ? 'required' : ''} 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                                    <option value="">انتخاب کنید</option>
                                    ${attr.options.map(opt => `<option value="${opt}">${opt}</option>`).join('')}
                                </select>
                            `;
                        } else if (attr.type === 'number') {
                            inputHtml = `
                                <input type="number" name="attributes[${attr.id}]" ${attr.is_required ? 'required' : ''} 
                                       placeholder="${attr.name}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                            `;
                        } else {
                            inputHtml = `
                                <input type="text" name="attributes[${attr.id}]" ${attr.is_required ? 'required' : ''} 
                                       placeholder="${attr.name}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                            `;
                        }
                        
                        div.innerHTML = `
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                ${attr.name}
                                ${attr.is_required ? '<span class="text-red-500">*</span>' : ''}
                            </label>
                            ${inputHtml}
                        `;
                        
                        attributesContainer.appendChild(div);
                    });
                    
                    show(attributesContainer);
                    console.log(`✓ ${data.attributes.length} attributes loaded`);
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
})();
</script>
