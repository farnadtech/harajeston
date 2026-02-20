<div x-data="countdown(@js($listing->ends_at))" x-init="startCountdown()" class="inline-flex items-center gap-2" dir="ltr">
    <template x-if="!ended">
        <div class="flex items-center gap-2">
            <!-- Days -->
            <div class="flex flex-col items-center">
                <span class="bg-secondary/10 text-secondary px-3 py-2 rounded-lg text-lg font-bold min-w-[50px] text-center" x-text="days"></span>
                <span class="text-xs text-gray-500 mt-1">روز</span>
            </div>
            
            <span class="text-2xl text-gray-400 font-bold">:</span>
            
            <!-- Hours -->
            <div class="flex flex-col items-center">
                <span class="bg-secondary/10 text-secondary px-3 py-2 rounded-lg text-lg font-bold min-w-[50px] text-center" x-text="hours"></span>
                <span class="text-xs text-gray-500 mt-1">ساعت</span>
            </div>
            
            <span class="text-2xl text-gray-400 font-bold">:</span>
            
            <!-- Minutes -->
            <div class="flex flex-col items-center">
                <span class="bg-secondary/10 text-secondary px-3 py-2 rounded-lg text-lg font-bold min-w-[50px] text-center" x-text="minutes"></span>
                <span class="text-xs text-gray-500 mt-1">دقیقه</span>
            </div>
            
            <span class="text-2xl text-gray-400 font-bold">:</span>
            
            <!-- Seconds -->
            <div class="flex flex-col items-center">
                <span class="bg-secondary/10 text-secondary px-3 py-2 rounded-lg text-lg font-bold min-w-[50px] text-center" x-text="seconds"></span>
                <span class="text-xs text-gray-500 mt-1">ثانیه</span>
            </div>
        </div>
    </template>
    <template x-if="ended">
        <span class="text-red-500 font-bold text-xl">پایان یافته</span>
    </template>
</div>

<script>
function countdown(endTime) {
    return {
        endTime: new Date(endTime).getTime(),
        days: '۰',
        hours: '۰۰',
        minutes: '۰۰',
        seconds: '۰۰',
        ended: false,
        interval: null,
        
        startCountdown() {
            this.updateTime();
            this.interval = setInterval(() => {
                this.updateTime();
            }, 1000);
        },
        
        updateTime() {
            const now = new Date().getTime();
            const distance = this.endTime - now;
            
            if (distance < 0) {
                this.ended = true;
                if (this.interval) clearInterval(this.interval);
                return;
            }
            
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            // Convert to Persian numbers
            this.days = this.toPersian(String(days));
            this.hours = this.toPersian(String(hours).padStart(2, '0'));
            this.minutes = this.toPersian(String(minutes).padStart(2, '0'));
            this.seconds = this.toPersian(String(seconds).padStart(2, '0'));
        },
        
        toPersian(num) {
            const persianDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
            return String(num).replace(/\d/g, x => persianDigits[parseInt(x)]);
        }
    }
}
</script>
