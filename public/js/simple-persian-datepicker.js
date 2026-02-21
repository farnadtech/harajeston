// Simple Persian Date Picker - Modal Version
(function() {
    'use strict';
    
    const persianMonths = ['فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'];
    const persianDays = ['ش', 'ی', 'د', 'س', 'چ', 'پ', 'ج'];
    
    function toPersianNum(num) {
        const persianDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        return String(num).replace(/\d/g, x => persianDigits[x]);
    }
    
    function g2j(gy, gm, gd) {
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
        let jm = (days < 186) ? 1 + Math.floor(days / 31) : 7 + Math.floor((days - 186) / 30);
        let jd = 1 + ((days < 186) ? (days % 31) : ((days - 186) % 30));
        return [jy, jm, jd];
    }
    
    function j2g(jy, jm, jd) {
        jy += 1595;
        let days = 365 * jy + Math.floor(jy / 33) * 8 + Math.floor((jy % 33 + 3) / 4) + 78 + jd;
        if (jm < 7) days += (jm - 1) * 31;
        else days += (jm - 7) * 30 + 186;
        
        let gy = 400 * Math.floor(days / 146097);
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
        let gm = 0;
        while (gm < 13 && days >= g_d_m[gm]) {
            days -= g_d_m[gm];
            gm++;
        }
        return new Date(gy, gm - 1, days + 1);
    }
    
    function jDaysInMonth(jy, jm) {
        if (jm <= 6) return 31;
        if (jm <= 11) return 30;
        return jIsLeapYear(jy) ? 30 : 29;
    }
    
    function jIsLeapYear(jy) {
        const breaks = [1, 5, 9, 13, 17, 22, 26, 30];
        let jp = breaks[0];
        let jump = 0;
        for (let i = 1; i < breaks.length; i++) {
            const jm = breaks[i];
            jump = jm - jp;
            if (jy < jm) break;
            jp = jm;
        }
        let n = jy - jp;
        if (jump - n < 6) n = n - jump + (Math.floor((jump + 4) / 33) * 33);
        let leapJ = ((n + 1) % 33) - 1;
        if (leapJ === -1) leapJ = 32;
        return (leapJ % 4 === 0);
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.persian-datepicker-input').forEach(input => {
            input.addEventListener('click', function(e) {
                e.preventDefault();
                showDatePicker(this);
            });
            input.setAttribute('readonly', 'readonly');
        });
    });
    
    function showDatePicker(input) {
        const existing = document.querySelector('.spd-modal');
        if (existing) existing.remove();
        
        const now = new Date();
        const [jy, jm, jd] = g2j(now.getFullYear(), now.getMonth() + 1, now.getDate());
        
        const modal = document.createElement('div');
        modal.className = 'spd-modal';
        modal.innerHTML = `
            <div class="spd-overlay"></div>
            <div class="spd-picker">
                <div class="spd-header">
                    <button type="button" class="spd-btn" data-action="prev-month">‹</button>
                    <div class="spd-title">
                        <span class="spd-month">${persianMonths[jm - 1]}</span>
                        <span class="spd-year">${toPersianNum(jy)}</span>
                    </div>
                    <button type="button" class="spd-btn" data-action="next-month">›</button>
                </div>
                <div class="spd-body">
                    <div class="spd-weekdays">
                        ${persianDays.map(d => `<div>${d}</div>`).join('')}
                    </div>
                    <div class="spd-days" id="spdDays"></div>
                </div>
                <div class="spd-time">
                    <label>ساعت: <input type="number" id="spdHour" min="0" max="23" value="12"></label>
                    <label>دقیقه: <input type="number" id="spdMinute" min="0" max="59" value="0"></label>
                </div>
                <div class="spd-footer">
                    <button type="button" class="spd-btn-action" data-action="today">امروز</button>
                    <button type="button" class="spd-btn-action spd-btn-confirm" data-action="confirm">تایید</button>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        let currentJY = jy;
        let currentJM = jm;
        let selectedJD = null;
        
        function renderDays() {
            const daysContainer = modal.querySelector('#spdDays');
            daysContainer.innerHTML = '';
            
            const daysInMonth = jDaysInMonth(currentJY, currentJM);
            const firstDay = j2g(currentJY, currentJM, 1);
            let firstDayOfWeek = (firstDay.getDay() + 1) % 7;
            
            for (let i = 0; i < firstDayOfWeek; i++) {
                const empty = document.createElement('div');
                empty.className = 'spd-day spd-day-empty';
                daysContainer.appendChild(empty);
            }
            
            for (let d = 1; d <= daysInMonth; d++) {
                const day = document.createElement('div');
                day.className = 'spd-day';
                day.textContent = toPersianNum(d);
                day.dataset.day = d;
                
                if (d === jd && currentJY === jy && currentJM === jm) {
                    day.classList.add('spd-day-today');
                }
                
                day.addEventListener('click', function() {
                    modal.querySelectorAll('.spd-day').forEach(d => d.classList.remove('spd-day-selected'));
                    this.classList.add('spd-day-selected');
                    selectedJD = parseInt(this.dataset.day);
                });
                
                daysContainer.appendChild(day);
            }
            
            modal.querySelector('.spd-month').textContent = persianMonths[currentJM - 1];
            modal.querySelector('.spd-year').textContent = toPersianNum(currentJY);
        }
        
        renderDays();
        
        modal.querySelector('[data-action="prev-month"]').addEventListener('click', function() {
            currentJM--;
            if (currentJM < 1) {
                currentJM = 12;
                currentJY--;
            }
            renderDays();
        });
        
        modal.querySelector('[data-action="next-month"]').addEventListener('click', function() {
            currentJM++;
            if (currentJM > 12) {
                currentJM = 1;
                currentJY++;
            }
            renderDays();
        });
        
        modal.querySelector('[data-action="today"]').addEventListener('click', function() {
            const [ty, tm, td] = g2j(now.getFullYear(), now.getMonth() + 1, now.getDate());
            currentJY = ty;
            currentJM = tm;
            selectedJD = td;
            renderDays();
            modal.querySelectorAll('.spd-day').forEach(d => {
                if (parseInt(d.dataset.day) === td) {
                    d.classList.add('spd-day-selected');
                }
            });
        });
        
        modal.querySelector('[data-action="confirm"]').addEventListener('click', function() {
            if (!selectedJD) {
                alert('لطفاً یک روز را انتخاب کنید');
                return;
            }
            
            const hour = parseInt(modal.querySelector('#spdHour').value) || 0;
            const minute = parseInt(modal.querySelector('#spdMinute').value) || 0;
            
            const gDate = j2g(currentJY, currentJM, selectedJD);
            gDate.setHours(hour, minute, 0, 0);
            
            const formatted = gDate.getFullYear() + '-' + 
                            String(gDate.getMonth() + 1).padStart(2, '0') + '-' + 
                            String(gDate.getDate()).padStart(2, '0') + 'T' + 
                            String(hour).padStart(2, '0') + ':' + 
                            String(minute).padStart(2, '0');
            
            input.value = formatted;
            modal.remove();
        });
        
        modal.querySelector('.spd-overlay').addEventListener('click', function() {
            modal.remove();
        });
    }
})();
