<div class="category-selector" x-data="categorySelector()">
    <label class="form-label">{{ $label ?? 'دسته‌بندی' }} *</label>
    
    {{-- مرحله 1: انتخاب دسته اصلی --}}
    <div class="mb-3">
        <label class="text-xs text-gray-600 mb-1 block">دسته اصلی</label>
        <select @change="selectParent($event.target.value)" 
                class="form-select" 
                x-model="selectedParent">
            <option value="">انتخاب دسته اصلی</option>
            @foreach($categories as $parent)
                <option value="{{ $parent['id'] }}">{{ $parent['name'] }}</option>
            @endforeach
        </select>
    </div>
    
    {{-- مرحله 2: انتخاب زیردسته --}}
    <div class="mb-3" x-show="showChildren" x-transition>
        <label class="text-xs text-gray-600 mb-1 block">زیردسته</label>
        <select @change="selectChild($event.target.value)" 
                class="form-select" 
                x-model="selectedChild">
            <option value="">انتخاب زیردسته</option>
            <template x-for="child in children" :key="child.id">
                <option :value="child.id" x-text="child.name"></option>
            </template>
        </select>
    </div>
    
    {{-- مرحله 3: انتخاب دسته نهایی --}}
    <div x-show="showGrandchildren" x-transition>
        <label class="text-xs text-gray-600 mb-1 block">دسته نهایی *</label>
        <select @change="selectFinal($event.target.value)" 
                class="form-select" 
                x-model="selectedFinal"
                required>
            <option value="">انتخاب دسته نهایی (الزامی)</option>
            <template x-for="grand in grandchildren" :key="grand.id">
                <option :value="grand.id" x-text="grand.name"></option>
            </template>
        </select>
        <p class="text-xs text-amber-600 mt-1">⚠ لطفا دسته نهایی را انتخاب کنید</p>
    </div>
    
    {{-- فیلد مخفی برای ارسال --}}
    <input type="hidden" 
           name="{{ $name ?? 'category_id' }}" 
           id="categorySelect"
           :value="finalCategoryId" 
           required>
    
    <p class="text-xs text-gray-500 mt-2" x-show="selectedPath" x-text="selectedPath"></p>
    
    @error($name ?? 'category_id')
        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
    @enderror
</div>

<script>
function categorySelector() {
    return {
        categories: @json($categories),
        selectedParent: '',
        selectedChild: '',
        selectedFinal: '',
        children: [],
        grandchildren: [],
        showChildren: false,
        showGrandchildren: false,
        finalCategoryId: '{{ old($name ?? "category_id", $selected ?? "") }}',
        selectedPath: '',
        
        init() {
            // اگر مقدار قبلی وجود داره، بارگذاری کن
            if (this.finalCategoryId) {
                this.loadExistingSelection();
            }
        },
        
        selectParent(parentId) {
            this.selectedChild = '';
            this.selectedFinal = '';
            this.children = [];
            this.grandchildren = [];
            this.showChildren = false;
            this.showGrandchildren = false;
            this.finalCategoryId = '';
            this.selectedPath = '';
            
            if (!parentId) return;
            
            const parent = this.categories.find(c => c.id == parentId);
            
            if (parent && parent.children && parent.children.length > 0) {
                this.children = Array.isArray(parent.children) ? parent.children : Object.values(parent.children);
                this.showChildren = true;
            } else if (parent) {
                // اگر دسته اصلی هیچ زیردسته‌ای نداره
                this.finalCategoryId = parentId;
                this.selectedPath = parent.name;
            }
        },
        
        selectChild(childId) {
            this.selectedFinal = '';
            this.grandchildren = [];
            this.showGrandchildren = false;
            this.finalCategoryId = '';
            this.selectedPath = '';
            
            if (!childId) return;
            
            const child = this.children.find(c => c.id == childId);
            
            if (child) {
                const parent = this.categories.find(c => c.id == this.selectedParent);
                
                // تبدیل children به array اگر object باشه
                let childChildren = child.children;
                if (childChildren && !Array.isArray(childChildren)) {
                    childChildren = Object.values(childChildren);
                }
                
                if (childChildren && childChildren.length > 0) {
                    // اگر سطح 2 زیردسته داره، باید سطح 3 انتخاب بشه
                    this.grandchildren = childChildren;
                    this.showGrandchildren = true;
                } else {
                    // اگر سطح 2 زیردسته نداره، خودش انتخاب نهایی است
                    this.finalCategoryId = childId;
                    this.selectedPath = parent.name + ' > ' + child.name;
                }
            }
        },
        
        selectFinal(finalId) {
            if (!finalId) {
                this.finalCategoryId = '';
                this.selectedPath = '';
                return;
            }
            
            const grand = this.grandchildren.find(c => c.id == finalId);
            if (grand) {
                const parent = this.categories.find(c => c.id == this.selectedParent);
                const child = this.children.find(c => c.id == this.selectedChild);
                
                this.finalCategoryId = finalId;
                this.selectedPath = parent.name + ' > ' + child.name + ' > ' + grand.name;
            }
        },
        
        loadExistingSelection() {
            // پیدا کردن دسته انتخاب شده و بارگذاری مسیر کامل
            fetch(`/api/categories/${this.finalCategoryId}/path`)
                .then(res => res.json())
                .then(data => {
                    if (data.path && data.path.length > 0) {
                        // سطح 1
                        if (data.path.length >= 1) {
                            this.selectedParent = data.path[0].id;
                            this.children = data.path[0].children || [];
                            this.showChildren = this.children.length > 0;
                        }
                        // سطح 2
                        if (data.path.length >= 2) {
                            this.selectedChild = data.path[1].id;
                            const child = this.children.find(c => c.id == this.selectedChild);
                            if (child && child.children) {
                                this.grandchildren = child.children;
                                this.showGrandchildren = this.grandchildren.length > 0;
                            }
                        }
                        // سطح 3
                        if (data.path.length >= 3) {
                            this.selectedFinal = data.path[2].id;
                            this.selectedPath = data.path.map(p => p.name).join(' > ');
                        } else if (data.path.length === 2) {
                            this.selectedPath = data.path.map(p => p.name).join(' > ');
                        } else {
                            this.selectedPath = data.path[0].name;
                        }
                    }
                });
        }
    }
}
</script>
