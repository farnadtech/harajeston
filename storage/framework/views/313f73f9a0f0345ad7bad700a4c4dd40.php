<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['listing']) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['listing']); ?>
<?php foreach (array_filter((['listing']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>
<?php
    $cardStyle = \App\Models\HomepageSetting::get('card_style', 'classic');
?>

<?php switch($cardStyle):
    case ('modern'): ?>
        <?php if (isset($component)) { $__componentOriginal598417998d472ac0b7dd2f31492bedc5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal598417998d472ac0b7dd2f31492bedc5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.listing-cards.modern','data' => ['listing' => $listing]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('listing-cards.modern'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['listing' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($listing)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal598417998d472ac0b7dd2f31492bedc5)): ?>
<?php $attributes = $__attributesOriginal598417998d472ac0b7dd2f31492bedc5; ?>
<?php unset($__attributesOriginal598417998d472ac0b7dd2f31492bedc5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal598417998d472ac0b7dd2f31492bedc5)): ?>
<?php $component = $__componentOriginal598417998d472ac0b7dd2f31492bedc5; ?>
<?php unset($__componentOriginal598417998d472ac0b7dd2f31492bedc5); ?>
<?php endif; ?>
        <?php break; ?>
    <?php case ('minimal'): ?>
        <?php if (isset($component)) { $__componentOriginal4fc64ac38ea2773f8e728fa00ca7f245 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4fc64ac38ea2773f8e728fa00ca7f245 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.listing-cards.minimal','data' => ['listing' => $listing]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('listing-cards.minimal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['listing' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($listing)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4fc64ac38ea2773f8e728fa00ca7f245)): ?>
<?php $attributes = $__attributesOriginal4fc64ac38ea2773f8e728fa00ca7f245; ?>
<?php unset($__attributesOriginal4fc64ac38ea2773f8e728fa00ca7f245); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4fc64ac38ea2773f8e728fa00ca7f245)): ?>
<?php $component = $__componentOriginal4fc64ac38ea2773f8e728fa00ca7f245; ?>
<?php unset($__componentOriginal4fc64ac38ea2773f8e728fa00ca7f245); ?>
<?php endif; ?>
        <?php break; ?>
    <?php case ('horizontal'): ?>
        <?php if (isset($component)) { $__componentOriginala128da1847e99a8e738f0d3766cf6b51 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala128da1847e99a8e738f0d3766cf6b51 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.listing-cards.horizontal','data' => ['listing' => $listing]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('listing-cards.horizontal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['listing' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($listing)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala128da1847e99a8e738f0d3766cf6b51)): ?>
<?php $attributes = $__attributesOriginala128da1847e99a8e738f0d3766cf6b51; ?>
<?php unset($__attributesOriginala128da1847e99a8e738f0d3766cf6b51); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala128da1847e99a8e738f0d3766cf6b51)): ?>
<?php $component = $__componentOriginala128da1847e99a8e738f0d3766cf6b51; ?>
<?php unset($__componentOriginala128da1847e99a8e738f0d3766cf6b51); ?>
<?php endif; ?>
        <?php break; ?>
    <?php default: ?>
        <?php if (isset($component)) { $__componentOriginald26b68482b6a7df4cf55db133f9d17df = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald26b68482b6a7df4cf55db133f9d17df = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.listing-cards.classic','data' => ['listing' => $listing]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('listing-cards.classic'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['listing' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($listing)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald26b68482b6a7df4cf55db133f9d17df)): ?>
<?php $attributes = $__attributesOriginald26b68482b6a7df4cf55db133f9d17df; ?>
<?php unset($__attributesOriginald26b68482b6a7df4cf55db133f9d17df); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald26b68482b6a7df4cf55db133f9d17df)): ?>
<?php $component = $__componentOriginald26b68482b6a7df4cf55db133f9d17df; ?>
<?php unset($__componentOriginald26b68482b6a7df4cf55db133f9d17df); ?>
<?php endif; ?>
<?php endswitch; ?>
<?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views\components\listing-card.blade.php ENDPATH**/ ?>