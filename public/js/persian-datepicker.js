/**
 * Persian (Jalali) Date Picker
 * A custom, beautiful Persian calendar picker with time selection
 */

class PersianDatePicker {
    constructor(inputElement, options = {}) {
        this.input = inputElement;
        this.options = {
            format: 'YYYY/MM/DD HH:mm',
            showTime: true,
            ...options
        };
        
        this.selectedDate = null;
        this.currentViewDate = this.getCurrentJalaliDate();
        this.isOpen = false;
        
        this.init();
    }
    
    init() {
        // Create picker container
        this.createPicker();
        
        // Bind events
        this.input.addEventListener('click', (e) => {
            e.preventDefault();
            this.open();
        });
        
        this.input.setAttribute('readonly', 'readonly');
        
        // Close on outside click
        document.addEventListener('click', (e) => {
            if (this.isOpen && !this.picker.contains(e.target) && e.target !== this.input) {
                this.close();
            }
        });
    }
    
    createPicker() {
        this.picker = document.createElement('div');
        this.picker.className = 'persian-datepicker';
        this.picker.style.display = 'none';
        document.body.appendChild(this.picker);
    }
    
    open() {
        if (this.isOpen) return;
        
        this.isOpen = true;
        this.render();
        this.position();
        this.picker.style.display = 'block';
        
        // Animate in
        setTimeout(() => {
            this.picker.classList.add('active');
        }, 10);
    }
    
    close() {
        if (!this.isOpen) return;
        
        this.isOpen = false;
        this.picker.classList.remove('active');
        
        setTimeout(() => {
            this.picker.style.display = 'none';
        }, 200);
    }
    
    position() {
        const rect = this.input.getBoundingClientRect();
        const pickerHeight = 400;
        const spaceBelow = window.innerHeight - rect.bottom;
        
        this.picker.style.position = 'fixed';
        this.picker.style.left = rect.left + 'px';
        this.picker.style.width = Math.max(320, rect.width) + 'px';
        
        if (spaceBelow < pickerHeight && rect.top > pickerHeight) {
            // Show above
            this.picker.style.top = (rect.top - pickerHeight - 5) + 'px';
        } else {
            // Show below
            this.picker.style.top = (rect.bottom + 5) + 'px';
        }
    }
    
    render() {
        const { year, month, day } = this.currentViewDate;
        const monthName = this.getMonthName(month);
        
        this.picker.innerHTML = `
            <div class="pdp-header">
                <button type="button" class="pdp-nav-btn" data-action="prevYear">
                    <span>«</span>
                </button>
                <button type="button" class="pdp-nav-btn" data-action="prevMonth">
                    <span>‹</span>
                </button>
                <div class="pdp-title">
                    <span class="pdp-month">${monthName}</span>
                    <span class="pdp-year">${this.toPersianNumber(year)}</span>
                </div>
                <button type="button" class="pdp-nav-btn" data-action="nextMonth">
                    <span>›</span>
                </button>
                <button type="button" class="pdp-nav-btn" data-action="nextYear">
                    <span>»</span>
                </button>
            </div>
            <div class="pdp-calendar">
                ${this.renderCalendar()}
            </div>
            ${this.options.showTime ? this.renderTimePicker() : ''}
            <div class="pdp-footer">
                <button type="button" class="pdp-btn pdp-btn-today" data-action="today">امروز</button>
                <button type="button" class="pdp-btn pdp-btn-clear" data-action="clear">پاک کردن</button>
                <button type="button" class="pdp-btn pdp-btn-confirm" data-action="confirm">تایید</button>
            </div>
        `;
        
        this.bindEvents();
    }
    
    renderCalendar() {
        const { year, month } = this.currentViewDate;
        const daysInMonth = this.getDaysInMonth(year, month);
        const firstDayOfWeek = this.getFirstDayOfWeek(year, month);
        
        let html = '<div class="pdp-weekdays">';
        const weekdays = ['ش', 'ی', 'د', 'س', 'چ', 'پ', 'ج'];
        weekdays.forEach(day => {
            html += `<div class="pdp-weekday">${day}</div>`;
        });
        html += '</div><div class="pdp-days">';
        
        // Empty cells before first day
        for (let i = 0; i < firstDayOfWeek; i++) {
            html += '<div class="pdp-day pdp-day-empty"></div>';
        }
        
        // Days of month
        const today = this.getCurrentJalaliDate();
        const isCurrentMonth = today.year === year && today.month === month;
        
        for (let day = 1; day <= daysInMonth; day++) {
            const isToday = isCurrentMonth && today.day === day;
            const isSelected = this.selectedDate && 
                              this.selectedDate.year === year && 
                              this.selectedDate.month === month && 
                              this.selectedDate.day === day;
            
            let classes = 'pdp-day';
            if (isToday) classes += ' pdp-day-today';
            if (isSelected) classes += ' pdp-day-selected';
            
            html += `<div class="${classes}" data-day="${day}">${this.toPersianNumber(day)}</div>`;
        }
        
        html += '</div>';
        return html;
    }
    
    renderTimePicker() {
        const hour = this.selectedDate?.hour || 12;
        const minute = this.selectedDate?.minute || 0;
        
        return `
            <div class="pdp-time">
                <div class="pdp-time-group">
                    <label>ساعت</label>
                    <input type="number" class="pdp-time-input" id="pdp-hour" min="0" max="23" value="${hour}">
                </div>
                <div class="pdp-time-separator">:</div>
                <div class="pdp-time-group">
                    <label>دقیقه</label>
                    <input type="number" class="pdp-time-input" id="pdp-minute" min="0" max="59" value="${minute}">
                </div>
            </div>
        `;
    }
    
    bindEvents() {
        // Navigation buttons
        this.picker.querySelectorAll('[data-action]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const action = btn.dataset.action;
                this.handleAction(action);
            });
        });
        
        // Day selection
        this.picker.querySelectorAll('.pdp-day:not(.pdp-day-empty)').forEach(dayEl => {
            dayEl.addEventListener('click', () => {
                const day = parseInt(dayEl.dataset.day);
                this.selectDate(day);
            });
        });
        
        // Time inputs
        if (this.options.showTime) {
            const hourInput = this.picker.querySelector('#pdp-hour');
            const minuteInput = this.picker.querySelector('#pdp-minute');
            
            if (hourInput) {
                hourInput.addEventListener('change', () => {
                    if (this.selectedDate) {
                        this.selectedDate.hour = parseInt(hourInput.value) || 0;
                    }
                });
            }
            
            if (minuteInput) {
                minuteInput.addEventListener('change', () => {
                    if (this.selectedDate) {
                        this.selectedDate.minute = parseInt(minuteInput.value) || 0;
                    }
                });
            }
        }
    }
    
    handleAction(action) {
        switch (action) {
            case 'prevYear':
                this.currentViewDate.year--;
                this.render();
                break;
            case 'nextYear':
                this.currentViewDate.year++;
                this.render();
                break;
            case 'prevMonth':
                this.currentViewDate.month--;
                if (this.currentViewDate.month < 1) {
                    this.currentViewDate.month = 12;
                    this.currentViewDate.year--;
                }
                this.render();
                break;
            case 'nextMonth':
                this.currentViewDate.month++;
                if (this.currentViewDate.month > 12) {
                    this.currentViewDate.month = 1;
                    this.currentViewDate.year++;
                }
                this.render();
                break;
            case 'today':
                const today = this.getCurrentJalaliDate();
                this.currentViewDate = { ...today };
                this.selectDate(today.day);
                break;
            case 'clear':
                this.selectedDate = null;
                this.input.value = '';
                this.close();
                break;
            case 'confirm':
                if (this.selectedDate) {
                    this.applySelection();
                }
                this.close();
                break;
        }
    }
    
    selectDate(day) {
        this.selectedDate = {
            year: this.currentViewDate.year,
            month: this.currentViewDate.month,
            day: day,
            hour: this.selectedDate?.hour || 12,
            minute: this.selectedDate?.minute || 0
        };
        this.render();
    }
    
    applySelection() {
        if (!this.selectedDate) return;
        
        const { year, month, day, hour, minute } = this.selectedDate;
        
        // Convert to Gregorian
        const gregorianDate = this.jalaliToGregorian(year, month, day);
        gregorianDate.setHours(hour || 0, minute || 0, 0, 0);
        
        // Format for datetime-local input
        const formatted = this.formatDateTimeLocal(gregorianDate);
        this.input.value = formatted;
        
        // Trigger change event
        this.input.dispatchEvent(new Event('change', { bubbles: true }));
    }
    
    formatDateTimeLocal(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        const hour = String(date.getHours()).padStart(2, '0');
        const minute = String(date.getMinutes()).padStart(2, '0');
        
        return `${year}-${month}-${day}T${hour}:${minute}`;
    }
    
    // Jalali calendar utilities
    getCurrentJalaliDate() {
        const now = new Date();
        return this.gregorianToJalali(now.getFullYear(), now.getMonth() + 1, now.getDate());
    }
    
    gregorianToJalali(gy, gm, gd) {
        const g_d_m = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];
        
        let jy = (gy <= 1600) ? 0 : 979;
        gy -= (gy <= 1600) ? 621 : 1600;
        
        let gy2 = (gm > 2) ? (gy + 1) : gy;
        let days = (365 * gy) + (Math.floor((gy2 + 3) / 4)) - (Math.floor((gy2 + 99) / 100)) + 
                   (Math.floor((gy2 + 399) / 400)) - 80 + gd + g_d_m[gm - 1];
        
        jy += 33 * Math.floor(days / 12053);
        days %= 12053;
        jy += 4 * Math.floor(days / 1461);
        days %= 1461;
        
        if (days > 365) {
            jy += Math.floor((days - 1) / 365);
            days = (days - 1) % 365;
        }
        
        let jm, jd;
        if (days < 186) {
            jm = 1 + Math.floor(days / 31);
            jd = 1 + (days % 31);
        } else {
            jm = 7 + Math.floor((days - 186) / 30);
            jd = 1 + ((days - 186) % 30);
        }
        
        return { year: jy, month: jm, day: jd };
    }
    
    jalaliToGregorian(jy, jm, jd) {
        let gy, gm, gd;
        
        jy += 1595;
        let days = 365 * jy + Math.floor(jy / 33) * 8 + Math.floor((jy % 33 + 3) / 4) + 78 + jd;
        
        if (jm < 7) {
            days += (jm - 1) * 31;
        } else {
            days += (jm - 7) * 30 + 186;
        }
        
        gy = 400 * Math.floor(days / 146097);
        days %= 146097;
        
        let flag = true;
        if (days >= 36525) {
            days--;
            gy += 100 * Math.floor(days / 36524);
            days %= 36524;
            if (days >= 365) days++;
            else flag = false;
        }
        
        if (flag) {
            gy += 4 * Math.floor(days / 1461);
            days %= 1461;
            if (days >= 366) {
                days--;
                gy += Math.floor(days / 365);
                days = days % 365;
            }
        }
        
        const g_d_m = [0, 31, flag && ((gy % 4 === 0 && gy % 100 !== 0) || gy % 400 === 0) ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        
        gm = 0;
        while (gm < 13 && days >= g_d_m[gm]) {
            days -= g_d_m[gm];
            gm++;
        }
        
        gd = days + 1;
        
        return new Date(gy, gm - 1, gd);
    }
    
    getDaysInMonth(year, month) {
        if (month <= 6) return 31;
        if (month <= 11) return 30;
        return this.isLeapYear(year) ? 30 : 29;
    }
    
    isLeapYear(year) {
        const breaks = [1, 5, 9, 13, 17, 22, 26, 30];
        const gy = year + 621;
        const jp = breaks[0];
        
        let jump = 0;
        for (let i = 1; i < breaks.length; i++) {
            const jm = breaks[i];
            jump = jm - jp;
            if (year < jm) break;
        }
        
        let n = year - jp;
        if (jump - n < 6) n = n - jump + (Math.floor((jump + 4) / 33) * 33);
        
        let leapJ = ((n + 1) % 33) - 1;
        if (leapJ === -1) leapJ = 32;
        
        return (leapJ % 4 === 0);
    }
    
    getFirstDayOfWeek(year, month) {
        const gregorianDate = this.jalaliToGregorian(year, month, 1);
        let dayOfWeek = gregorianDate.getDay();
        // Convert to Persian week (Saturday = 0)
        dayOfWeek = (dayOfWeek + 1) % 7;
        return dayOfWeek;
    }
    
    getMonthName(month) {
        const months = [
            '', 'فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور',
            'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'
        ];
        return months[month];
    }
    
    toPersianNumber(num) {
        const persianDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        return String(num).replace(/\d/g, x => persianDigits[x]);
    }
}

// Auto-initialize
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.persian-datepicker-input').forEach(input => {
        new PersianDatePicker(input);
    });
});
