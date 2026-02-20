<div id="attributesSection" style="display: none;" class="mb-6 p-4 bg-gray-50 rounded-lg">
    <h3 class="text-lg font-bold mb-4 text-gray-800">ویژگی‌های محصول</h3>
    <div id="attributesContainer" class="space-y-4">
        <!-- ویژگی‌ها به صورت داینامیک اضافه می‌شوند -->
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('categorySelect');
    const attributesSection = document.getElementById('attributesSection');
    const attributesContainer = document.getElementById('attributesContainer');
    
    if (categorySelect) {
        categorySelect.addEventListener('change', function() {
            const categoryId = this.value;
            
            if (!categoryId) {
                attributesSection.style.display = 'none';
                attributesContainer.innerHTML = '';
                return;
            }
            
            // دریافت ویژگی‌های دسته‌بندی
            fetch(`{{ url('/api/categories') }}/${categoryId}/attributes`)
                .then(response => response.json())
                .then(data => {
                    if (data.attributes && data.attributes.length > 0) {
                        attributesContainer.innerHTML = '';
                        
                        data.attributes.forEach(attr => {
                            const div = document.createElement('div');
                            div.className = 'mb-4';
                            
                            let inputHtml = '';
                            const fieldName = `attributes[${attr.id}]`;
                            const required = attr.is_required ? 'required' : '';
                            const requiredLabel = attr.is_required ? '<span class="text-red-500">*</span>' : '';
                            
                            if (attr.type === 'select' && attr.options) {
                                inputHtml = `
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        ${attr.name} ${requiredLabel}
                                    </label>
                                    <select name="${fieldName}" ${required}
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                        <option value="">انتخاب کنید</option>
                                        ${attr.options.map(opt => `<option value="${opt}">${opt}</option>`).join('')}
                                    </select>
                                `;
                            } else if (attr.type === 'number') {
                                inputHtml = `
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        ${attr.name} ${requiredLabel}
                                    </label>
                                    <input type="number" name="${fieldName}" ${required}
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                           placeholder="${attr.name}">
                                `;
                            } else {
                                inputHtml = `
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        ${attr.name} ${requiredLabel}
                                    </label>
                                    <input type="text" name="${fieldName}" ${required}
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                           placeholder="${attr.name}">
                                `;
                            }
                            
                            div.innerHTML = inputHtml;
                            attributesContainer.appendChild(div);
                        });
                        
                        attributesSection.style.display = 'block';
                    } else {
                        attributesSection.style.display = 'none';
                        attributesContainer.innerHTML = '';
                    }
                })
                .catch(error => {
                    console.error('Error fetching attributes:', error);
                    attributesSection.style.display = 'none';
                });
        });
        
        // اگر دسته‌بندی از قبل انتخاب شده (مثلاً در ویرایش)
        if (categorySelect.value) {
            categorySelect.dispatchEvent(new Event('change'));
        }
    }
});
</script>
