/*!
 * Persian Date Picker - Modern & Complete
 * Based on persian-datepicker library
 */

class PersianDatePicker {
    constructor(input, options = {}) {
        this.input = typeof input === 'string' ? document.querySelector(input) : input;
        if (!this.input) return;

        this.options = {
            format: 'YYYY/MM/DD HH:mm',
            timePicker: true,
            initialValue: true,
            autoClose: false,
            minDate: null, // Can be 'today' or a date object
            ...options
        };

        this.persianMonths = [
            'فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور',
            'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'
        ];

        this.persianDays = ['ش', 'ی', 'د', 'س', 'چ', 'پ', 'ج'];

        // Parse existing value if present
        const existingValue = this.input.value ? this.parseDate(this.input.value) : null;
        
        if (existingValue) {
            // Use existing value for both current and selected
            this.currentDate = { ...existingValue };
            this.selectedDate = { ...existingValue };
        } else {
            // Use today's date
            const today = this.gregorianToJalali(new Date());
            this.currentDate = { ...today };
            this.selectedDate = null;
        }

        this.init();
    }

    init() {
        // Check if already initialized
        if (this.input.dataset.pickerInitialized === 'true') {
            return;
        }
        this.input.dataset.pickerInitialized = 'true';
        
        this.input.setAttribute('readonly', 'readonly');
        this.input.style.cursor = 'pointer';
        
        this.input.addEventListener('click', () => this.show());
        document.addEventListener('click', (e) => {
            if (!this.picker && !this.input.contains(e.target)) return;
            if (this.picker && !this.picker.contains(e.target) && !this.input.contains(e.target)) {
                this.hide();
            }
        });
    }

    show() {
        if (this.picker && this.picker.parentNode) {
            // Picker already exists and is in DOM
            this.picker.style.display = 'flex';
            
            // If there's a selected date, navigate to that month
            if (this.selectedDate) {
                this.currentDate = { ...this.selectedDate };
            }
            
            this.updateCalendar();
            return;
        }

        // If there's a selected date, navigate to that month
        if (this.selectedDate) {
            this.currentDate = { ...this.selectedDate };
        }

        this.createPicker();
        document.body.appendChild(this.picker);
        this.positionPicker();
        this.updateCalendar();
    }

    hide() {
        if (this.picker) {
            this.picker.remove();
            this.picker = null;
        }
    }

    createPicker() {
        this.picker = document.createElement('div');
        this.picker.className = 'pdp-modal';
        this.picker.innerHTML = `
            <div class="pdp-overlay"></div>
            <div class="pdp-picker">
                <div class="pdp-header">
                    <button type="button" class="pdp-btn pdp-btn-prev">‹</button>
                    <div class="pdp-title">
                        <select class="pdp-month-select"></select>
                        <select class="pdp-year-select"></select>
                    </div>
                    <button type="button" class="pdp-btn pdp-btn-next">›</button>
                </div>
                <div class="pdp-body">
                    <div class="pdp-weekdays"></div>
                    <div class="pdp-days"></div>
                </div>
                ${this.options.timePicker ? `
                <div class="pdp-time">
                    <label>
                        ساعت:
                        <input type="number" class="pdp-hour" min="0" max="23" value="${this.selectedDate ? this.selectedDate.hour : 12}">
                    </label>
                    <label>
                        دقیقه:
                        <input type="number" class="pdp-minute" min="0" max="59" value="${this.selectedDate ? this.selectedDate.minute : 0}">
                    </label>
                </div>
                ` : ''}
                <div class="pdp-footer">
                    <button type="button" class="pdp-btn-action" data-action="today">امروز</button>
                    <button type="button" class="pdp-btn-action pdp-btn-confirm">تایید</button>
                </div>
            </div>
        `;

        // Prevent clicks inside picker from closing it
        this.picker.querySelector('.pdp-picker').addEventListener('click', (e) => {
            e.stopPropagation();
        });

        // Event listeners
        this.picker.querySelector('.pdp-btn-prev').addEventListener('click', (e) => {
            e.stopPropagation();
            this.prevMonth();
        });
        this.picker.querySelector('.pdp-btn-next').addEventListener('click', (e) => {
            e.stopPropagation();
            this.nextMonth();
        });
        this.picker.querySelector('[data-action="today"]').addEventListener('click', (e) => {
            e.stopPropagation();
            this.selectToday();
        });
        this.picker.querySelector('.pdp-btn-confirm').addEventListener('click', (e) => {
            e.stopPropagation();
            this.confirm();
        });
        this.picker.querySelector('.pdp-overlay').addEventListener('click', () => this.hide());

        // Month and year dropdowns
        this.picker.querySelector('.pdp-month-select').addEventListener('change', (e) => {
            this.currentDate.month = parseInt(e.target.value);
            this.updateCalendar();
        });
        
        this.picker.querySelector('.pdp-year-select').addEventListener('change', (e) => {
            this.currentDate.year = parseInt(e.target.value);
            this.updateCalendar();
        });

        if (this.options.timePicker) {
            const hourInput = this.picker.querySelector('.pdp-hour');
            const minuteInput = this.picker.querySelector('.pdp-minute');
            
            hourInput.addEventListener('input', (e) => {
                let val = parseInt(e.target.value) || 0;
                if (val > 23) val = 23;
                if (val < 0) val = 0;
                e.target.value = val;
            });

            minuteInput.addEventListener('input', (e) => {
                let val = parseInt(e.target.value) || 0;
                if (val > 59) val = 59;
                if (val < 0) val = 0;
                e.target.value = val;
            });
        }
    }

    positionPicker() {
        // Modal positioning is handled by CSS
    }

    updateCalendar() {
        const monthSelect = this.picker.querySelector('.pdp-month-select');
        const yearSelect = this.picker.querySelector('.pdp-year-select');
        const weekdaysEl = this.picker.querySelector('.pdp-weekdays');
        const daysEl = this.picker.querySelector('.pdp-days');

        // Populate month dropdown
        monthSelect.innerHTML = this.persianMonths.map((month, index) => 
            `<option value="${index + 1}" ${this.currentDate.month === index + 1 ? 'selected' : ''}>${month}</option>`
        ).join('');

        // Populate year dropdown (current year ± 10 years)
        const currentYear = this.currentDate.year;
        const yearOptions = [];
        for (let y = currentYear - 10; y <= currentYear + 10; y++) {
            yearOptions.push(`<option value="${y}" ${this.currentDate.year === y ? 'selected' : ''}>${this.toPersianNumber(y)}</option>`);
        }
        yearSelect.innerHTML = yearOptions.join('');

        // Weekdays
        weekdaysEl.innerHTML = this.persianDays.map(day => 
            `<div class="pdp-weekday">${day}</div>`
        ).join('');

        // Days
        const daysInMonth = this.getDaysInMonth(this.currentDate.year, this.currentDate.month);
        const firstDayOfWeek = this.getFirstDayOfWeek(this.currentDate.year, this.currentDate.month);

        let daysHTML = '';
        
        // Empty cells
        for (let i = 0; i < firstDayOfWeek; i++) {
            daysHTML += '<div class="pdp-day pdp-day-empty"></div>';
        }

        // Days
        const today = this.gregorianToJalali(new Date());
        const minDate = this.options.minDate === 'today' ? today : (this.options.minDate ? this.gregorianToJalali(this.options.minDate) : null);
        
        for (let day = 1; day <= daysInMonth; day++) {
            const isToday = today.year === this.currentDate.year && 
                          today.month === this.currentDate.month && 
                          today.day === day;
            
            const isSelected = this.selectedDate && 
                             this.selectedDate.year === this.currentDate.year && 
                             this.selectedDate.month === this.currentDate.month && 
                             this.selectedDate.day === day;

            // Check if date is before minDate
            let isPast = false;
            if (minDate) {
                const currentYear = this.currentDate.year;
                const currentMonth = this.currentDate.month;
                
                if (currentYear < minDate.year) {
                    isPast = true;
                } else if (currentYear === minDate.year) {
                    if (currentMonth < minDate.month) {
                        isPast = true;
                    } else if (currentMonth === minDate.month && day < minDate.day) {
                        isPast = true;
                    }
                }
            }

            let classes = 'pdp-day';
            if (isToday) classes += ' pdp-day-today';
            if (isSelected) classes += ' pdp-day-selected';
            if (isPast) classes += ' pdp-day-disabled';

            daysHTML += `<div class="${classes}" data-day="${day}" ${isPast ? 'data-disabled="true"' : ''}>${this.toPersianNumber(day)}</div>`;
        }

        daysEl.innerHTML = daysHTML;

        // Add click events to days
        daysEl.querySelectorAll('.pdp-day:not(.pdp-day-empty)').forEach(dayEl => {
            dayEl.addEventListener('click', () => {
                // Don't allow selecting disabled days
                if (dayEl.dataset.disabled === 'true') {
                    return;
                }
                const day = parseInt(dayEl.dataset.day);
                this.selectDate(day);
            });
        });
    }

    selectDate(day) {
        this.selectedDate = {
            year: this.currentDate.year,
            month: this.currentDate.month,
            day: day,
            hour: this.selectedDate ? this.selectedDate.hour : 12,
            minute: this.selectedDate ? this.selectedDate.minute : 0
        };

        if (this.options.timePicker) {
            const hourInput = this.picker.querySelector('.pdp-hour');
            const minuteInput = this.picker.querySelector('.pdp-minute');
            if (hourInput && minuteInput) {
                this.selectedDate.hour = parseInt(hourInput.value) || 12;
                this.selectedDate.minute = parseInt(minuteInput.value) || 0;
            }
        }

        this.updateCalendar();

        if (this.options.autoClose && !this.options.timePicker) {
            this.confirm();
        }
    }

    selectToday() {
        const today = this.gregorianToJalali(new Date());
        this.currentDate = { ...today };
        this.selectDate(today.day);
    }

    prevMonth() {
        if (this.currentDate.month === 1) {
            this.currentDate.month = 12;
            this.currentDate.year--;
        } else {
            this.currentDate.month--;
        }
        this.updateCalendar();
    }

    nextMonth() {
        if (this.currentDate.month === 12) {
            this.currentDate.month = 1;
            this.currentDate.year++;
        } else {
            this.currentDate.month++;
        }
        this.updateCalendar();
    }

    confirm() {
        if (!this.selectedDate) {
            alert('لطفاً ابتدا یک تاریخ انتخاب کنید');
            return;
        }

        if (this.options.timePicker) {
            const hourInput = this.picker.querySelector('.pdp-hour');
            const minuteInput = this.picker.querySelector('.pdp-minute');
            this.selectedDate.hour = parseInt(hourInput.value) || 0;
            this.selectedDate.minute = parseInt(minuteInput.value) || 0;
        }

        const formatted = this.formatDate(this.selectedDate);
        this.input.value = formatted;
        
        // Trigger change event
        const event = new Event('change', { bubbles: true });
        this.input.dispatchEvent(event);

        this.hide();
    }

    formatDate(date) {
        if (!date) return '';
        
        // Return Persian date - server will convert using morilog/jalali
        const year = date.year;
        const month = String(date.month).padStart(2, '0');
        const day = String(date.day).padStart(2, '0');
        
        let result = `${year}/${month}/${day}`;
        
        if (this.options.timePicker) {
            const hour = String(date.hour).padStart(2, '0');
            const minute = String(date.minute).padStart(2, '0');
            result += ` ${hour}:${minute}`;
        }
        
        return result;
    }

    parseDate(dateStr) {
        if (!dateStr) return null;
        
        // Convert Persian numbers to English
        dateStr = this.toEnglishNumber(dateStr);
        
        // Check if it's Gregorian format (YYYY-MM-DD HH:mm or YYYY-MM-DDTHH:mm)
        if (dateStr.match(/^\d{4}-\d{2}-\d{2}[T\s]\d{2}:\d{2}/)) {
            // Parse Gregorian and convert to Jalali
            const parts = dateStr.replace('T', ' ').split(' ');
            const dateParts = parts[0].split('-');
            const timeParts = parts[1] ? parts[1].split(':') : ['12', '00'];
            
            const gDate = new Date(
                parseInt(dateParts[0]),
                parseInt(dateParts[1]) - 1,
                parseInt(dateParts[2]),
                parseInt(timeParts[0]),
                parseInt(timeParts[1])
            );
            
            const jalali = this.gregorianToJalali(gDate);
            return {
                year: jalali.year,
                month: jalali.month,
                day: jalali.day,
                hour: parseInt(timeParts[0]) || 12,
                minute: parseInt(timeParts[1]) || 0
            };
        }
        
        // Parse Persian date format: 1404/12/01 12:00
        const parts = dateStr.split(' ');
        const dateParts = parts[0].split('/');
        
        if (dateParts.length !== 3) return null;
        
        const result = {
            year: parseInt(dateParts[0]),
            month: parseInt(dateParts[1]),
            day: parseInt(dateParts[2]),
            hour: 12,
            minute: 0
        };
        
        if (parts.length > 1 && this.options.timePicker) {
            const timeParts = parts[1].split(':');
            if (timeParts.length >= 2) {
                result.hour = parseInt(timeParts[0]) || 0;
                result.minute = parseInt(timeParts[1]) || 0;
            }
        }
        
        return result;
    }

    // Jalali calendar utilities
    gregorianToJalali(date) {
        let gy = date.getFullYear();
        const gm = date.getMonth() + 1;
        const gd = date.getDate();
        
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
        
        return {
            year: jy,
            month: jm,
            day: jd,
            hour: date.getHours(),
            minute: date.getMinutes()
        };
    }

    jalaliToGregorian(jy, jm, jd) {
        // Well-tested Jalali to Gregorian conversion
        // Based on Kazimierz M. Borkowski algorithm
        let epyear = jy - 474;
        let cycle = Math.floor(epyear / 2820) + 1;
        let cyear = epyear - 2820 * (cycle - 1);
        let aux1, aux2;
        
        if (cyear < 1029) {
            aux1 = cyear;
            aux2 = cyear - 29;
        } else {
            aux1 = cyear - 1029;
            aux2 = cyear - 1030;
        }
        
        let ycycle = Math.floor(aux1 / 128) * 128 + 
                     Math.floor((aux1 % 128) / 33) * 33 + 
                     ((aux1 % 128) % 33);
        
        let jdn = 1948321 + 1029983 * (cycle - 1) + 365 * cyear + 
                  Math.floor(aux2 / 128) * 31 + 
                  Math.floor((aux2 % 128) / 33) * 8 + 
                  Math.floor(((aux2 % 128) % 33 + 3) / 4);
        
        if (jm <= 7) {
            jdn += (jm - 1) * 31;
        } else {
            jdn += (jm - 7) * 30 + 186;
        }
        
        jdn += jd;
        
        // Convert JDN to Gregorian
        let l = jdn + 68569;
        let n = Math.floor((4 * l) / 146097);
        l = l - Math.floor((146097 * n + 3) / 4);
        let i = Math.floor((4000 * (l + 1)) / 1461001);
        l = l - Math.floor((1461 * i) / 4) + 31;
        let j = Math.floor((80 * l) / 2447);
        let gd = l - Math.floor((2447 * j) / 80);
        l = Math.floor(j / 11);
        let gm = j + 2 - (12 * l);
        let gy = 100 * (n - 49) + i + l;
        
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
        const leap = -14;
        let jp = breaks[0];
        
        let jump = 0;
        for (let i = 1; i < breaks.length; i++) {
            const jm = breaks[i];
            jump = jm - jp;
            if (year < jm) break;
            jp = jm;
        }
        
        let n = year - jp;
        if (jump - n < 6) n = n - jump + (Math.floor((jump + 4) / 33) * 33);
        
        let leapJ = ((n + 1) % 33 - 1) % 4;
        if (leapJ === -1) leapJ = 4;
        
        return leapJ === 0;
    }

    getFirstDayOfWeek(year, month) {
        const gDate = this.jalaliToGregorian(year, month, 1);
        return (gDate.getDay() + 1) % 7; // Adjust for Saturday start
    }

    toPersianNumber(num) {
        const persianDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        return num.toString().replace(/\d/g, x => persianDigits[x]);
    }

    toEnglishNumber(str) {
        const persianDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        const arabicDigits = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        
        for (let i = 0; i < 10; i++) {
            str = str.replace(new RegExp(persianDigits[i], 'g'), i);
            str = str.replace(new RegExp(arabicDigits[i], 'g'), i);
        }
        
        return str;
    }

    destroy() {
        if (this.picker) {
            this.picker.remove();
            this.picker = null;
        }
    }
}

// Note: Auto-initialization is disabled. 
// Initialize manually in each page with specific options.
// Example: new PersianDatePicker('#inputId', { minDate: 'today' });
