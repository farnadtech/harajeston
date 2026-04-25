<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class NotificationSetting extends Model
{
    protected $fillable = [
        'event_key', 'event_label', 'recipient',
        'via_database', 'via_sms', 'via_email',
        'sms_pattern_id', 'sms_template',
        'email_subject', 'email_body',
    ];

    protected $casts = [
        'via_database' => 'boolean',
        'via_sms'      => 'boolean',
        'via_email'    => 'boolean',
    ];

    public static function getEvents(): array
    {
        return [
            'order_placed_buyer' => [
                'label'     => 'ثبت سفارش (خریدار)',
                'recipient' => 'buyer',
                'sms_pattern_text' => 'کاربر گرامی، سفارش شما با کد {0} برای {1} به مبلغ {2} تومان ثبت شد.',
                'sms_vars'  => [
                    '{0}' => 'شماره سفارش (order_number)',
                    '{1}' => 'نام محصول (listing_title)',
                    '{2}' => 'مبلغ (amount)',
                ],
            ],
            'order_placed_seller' => [
                'label'     => 'سفارش جدید (فروشنده)',
                'recipient' => 'seller',
                'sms_pattern_text' => 'فروشنده گرامی، سفارش جدید {0} از {1} برای {2} به مبلغ {3} تومان ثبت شد.',
                'sms_vars'  => [
                    '{0}' => 'شماره سفارش (order_number)',
                    '{1}' => 'نام خریدار (buyer_name)',
                    '{2}' => 'نام محصول (listing_title)',
                    '{3}' => 'مبلغ (amount)',
                ],
            ],
            'order_ready_processing' => [
                'label'     => 'سفارش آماده پردازش (فروشنده)',
                'recipient' => 'seller',
                'sms_pattern_text' => 'فروشنده گرامی، سفارش {0} برای {1} آماده پردازش است. خریدار آدرس ارسال را ثبت کرد.',
                'sms_vars'  => [
                    '{0}' => 'شماره سفارش (order_number)',
                    '{1}' => 'نام محصول (listing_title)',
                ],
            ],
            'order_shipped_buyer' => [
                'label'     => 'ارسال سفارش (خریدار)',
                'recipient' => 'buyer',
                'sms_pattern_text' => 'کاربر گرامی، سفارش {0} از فروشگاه {1} ارسال شد. کد رهگیری: {2} می باشد.',
                'sms_vars'  => [
                    '{0}' => 'شماره سفارش (order_number)',
                    '{1}' => 'نام فروشنده (seller_name)',
                    '{2}' => 'کد رهگیری (tracking_number)',
                ],
            ],
            'order_delivered_buyer' => [
                'label'     => 'تحویل سفارش (خریدار)',
                'recipient' => 'buyer',
                'sms_pattern_text' => 'کاربر گرامی، سفارش {0} برای {1} تحویل داده شد. از خرید شما متشکریم.',
                'sms_vars'  => [
                    '{0}' => 'شماره سفارش (order_number)',
                    '{1}' => 'نام محصول (listing_title)',
                ],
            ],
            'order_cancelled_buyer' => [
                'label'     => 'لغو سفارش (خریدار)',
                'recipient' => 'buyer',
                'sms_pattern_text' => 'کاربر گرامی، سفارش {0} برای {1} لغو شد. مبلغ {2} تومان به کیف پول شما بازگشت.',
                'sms_vars'  => [
                    '{0}' => 'شماره سفارش (order_number)',
                    '{1}' => 'نام محصول (listing_title)',
                    '{2}' => 'مبلغ (amount)',
                ],
            ],
            'order_cancelled_seller' => [
                'label'     => 'لغو سفارش (فروشنده)',
                'recipient' => 'seller',
                'sms_pattern_text' => 'فروشنده گرامی، سفارش {0} برای {1} توسط خریدار لغو شد.',
                'sms_vars'  => [
                    '{0}' => 'شماره سفارش (order_number)',
                    '{1}' => 'نام محصول (listing_title)',
                ],
            ],
            'new_bid_seller' => [
                'label'     => 'پیشنهاد جدید (فروشنده)',
                'recipient' => 'seller',
                'sms_pattern_text' => 'فروشنده گرامی، پیشنهاد جدید {0} تومان برای حراجی {1} ثبت شد.',
                'sms_vars'  => [
                    '{0}' => 'مبلغ پیشنهاد (amount)',
                    '{1}' => 'نام محصول (listing_title)',
                ],
            ],
            'outbid_buyer' => [
                'label'     => 'پیشنهاد بالاتر (خریدار)',
                'recipient' => 'buyer',
                'sms_pattern_text' => 'کاربر گرامی، پیشنهاد شما در حراجی {0} پیشی گرفته شد. پیشنهاد جدید {1} تومان می باشد.',
                'sms_vars'  => [
                    '{0}' => 'نام محصول (listing_title)',
                    '{1}' => 'مبلغ پیشنهاد جدید (amount)',
                ],
            ],
            'auction_won_buyer' => [
                'label'     => 'برنده مزایده (خریدار)',
                'recipient' => 'buyer',
                'sms_pattern_text' => 'کاربر گرامی، تبریک! شما برنده حراجی {0} با مبلغ {1} تومان شدید.',
                'sms_vars'  => [
                    '{0}' => 'نام محصول (listing_title)',
                    '{1}' => 'مبلغ نهایی (amount)',
                ],
            ],
            'auction_ended_seller' => [
                'label'     => 'پایان مزایده (فروشنده)',
                'recipient' => 'seller',
                'sms_pattern_text' => 'فروشنده گرامی، حراجی {0} به پایان رسید. برنده با مبلغ {1} تومان مشخص شد.',
                'sms_vars'  => [
                    '{0}' => 'نام محصول (listing_title)',
                    '{1}' => 'مبلغ نهایی (amount)',
                ],
            ],
            'listing_approved_seller' => [
                'label'     => 'تایید آگهی (فروشنده)',
                'recipient' => 'seller',
                'sms_pattern_text' => 'فروشنده گرامی، آگهی {0} شما تایید و در سایت منتشر شد.',
                'sms_vars'  => [
                    '{0}' => 'نام محصول (listing_title)',
                ],
            ],
            'listing_rejected_seller' => [
                'label'     => 'رد آگهی (فروشنده)',
                'recipient' => 'seller',
                'sms_pattern_text' => 'فروشنده گرامی، آگهی {0} شما رد شد. برای اطلاعات بیشتر با پشتیبانی تماس بگیرید.',
                'sms_vars'  => [
                    '{0}' => 'نام محصول (listing_title)',
                ],
            ],
            'withdrawal_approved' => [
                'label'     => 'تایید درخواست برداشت (کاربر)',
                'recipient' => 'buyer',
                'sms_pattern_text' => 'کاربر گرامی، درخواست برداشت {0} تومان شما تایید شد. مبلغ به حساب بانکی شما واریز خواهد شد.',
                'sms_vars'  => [
                    '{0}' => 'مبلغ (amount)',
                ],
            ],
            'withdrawal_rejected' => [
                'label'     => 'رد درخواست برداشت (کاربر)',
                'recipient' => 'buyer',
                'sms_pattern_text' => 'کاربر گرامی، درخواست برداشت {0} تومان شما رد شد. دلیل: {1}',
                'sms_vars'  => [
                    '{0}' => 'مبلغ (amount)',
                    '{1}' => 'دلیل رد (reject_reason)',
                ],
            ],
            'seller_approved' => [
                'label'     => 'تایید درخواست فروشندگی (کاربر)',
                'recipient' => 'buyer',
                'sms_pattern_text' => 'کاربر گرامی، درخواست فروشندگی شما تایید شد. اکنون می‌توانید محصولات خود را ثبت کنید.',
                'sms_vars'  => [],
            ],
            'seller_rejected' => [
                'label'     => 'رد درخواست فروشندگی (کاربر)',
                'recipient' => 'buyer',
                'sms_pattern_text' => 'کاربر گرامی، درخواست فروشندگی شما رد شد. دلیل: {0}',
                'sms_vars'  => [
                    '{0}' => 'دلیل رد (reason)',
                ],
            ],
        ];
    }

    public static function forEvent(string $key): ?self
    {
        return Cache::remember("notif_setting_{$key}", 300, fn() =>
            static::where('event_key', $key)->first()
        );
    }

    public static function seedDefaults(): void
    {
        foreach (static::getEvents() as $key => $info) {
            static::firstOrCreate(
                ['event_key' => $key],
                ['event_label' => $info['label'], 'recipient' => $info['recipient'],
                 'via_database' => true, 'via_sms' => false, 'via_email' => false]
            );
        }
    }
}
