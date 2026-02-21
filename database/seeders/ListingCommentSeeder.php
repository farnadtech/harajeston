<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ListingComment;
use App\Models\Listing;
use App\Models\User;

class ListingCommentSeeder extends Seeder
{
    public function run(): void
    {
        $listings = Listing::take(5)->get();
        $users = User::where('role', '!=', 'admin')->take(10)->get();
        
        if ($listings->isEmpty() || $users->isEmpty()) {
            $this->command->warn('No listings or users found. Skipping comment seeding.');
            return;
        }

        foreach ($listings as $listing) {
            // Add 2-3 comments
            for ($i = 0; $i < rand(2, 3); $i++) {
                $comment = ListingComment::create([
                    'listing_id' => $listing->id,
                    'user_id' => $users->random()->id,
                    'type' => 'comment',
                    'content' => $this->getRandomComment(),
                    'rating' => rand(3, 5), // Random rating between 3-5
                    'status' => rand(0, 1) ? 'approved' : 'pending',
                    'approved_at' => rand(0, 1) ? now() : null,
                    'created_at' => now()->subDays(rand(1, 10)),
                ]);
                
                // Update listing rating if comment is approved
                if ($comment->isApproved()) {
                    $listing->updateRating();
                }
                
                // Add seller reply to some comments
                if ($comment->isApproved() && rand(0, 1)) {
                    ListingComment::create([
                        'listing_id' => $listing->id,
                        'user_id' => $listing->seller_id,
                        'parent_id' => $comment->id,
                        'type' => 'comment',
                        'content' => $this->getRandomReply(),
                        'status' => 'approved',
                        'approved_at' => now(),
                        'created_at' => $comment->created_at->addHours(rand(1, 24)),
                    ]);
                }
            }
            
            // Add 1-2 questions
            for ($i = 0; $i < rand(1, 2); $i++) {
                $question = ListingComment::create([
                    'listing_id' => $listing->id,
                    'user_id' => $users->random()->id,
                    'type' => 'question',
                    'content' => $this->getRandomQuestion(),
                    'status' => rand(0, 1) ? 'approved' : 'pending',
                    'approved_at' => rand(0, 1) ? now() : null,
                    'created_at' => now()->subDays(rand(1, 10)),
                ]);
                
                // Add seller answer to some questions
                if ($question->isApproved() && rand(0, 1)) {
                    ListingComment::create([
                        'listing_id' => $listing->id,
                        'user_id' => $listing->seller_id,
                        'parent_id' => $question->id,
                        'type' => 'question',
                        'content' => $this->getRandomAnswer(),
                        'status' => 'approved',
                        'approved_at' => now(),
                        'created_at' => $question->created_at->addHours(rand(1, 24)),
                    ]);
                }
            }
        }

        $this->command->info('Sample comments and questions created successfully!');
    }

    private function getRandomComment(): string
    {
        $comments = [
            'محصول بسیار عالی و با کیفیت است. پیشنهاد می‌کنم حتما خریداری کنید.',
            'من این محصول رو خریدم و واقعا راضی هستم. ارزش خریدش رو داره.',
            'کیفیت ساخت عالیه و دقیقا مطابق توضیحات فروشنده است.',
            'قیمت مناسبی داره و با توجه به کیفیتش ارزشش رو داره.',
            'فروشنده خیلی خوب بود و محصول سالم تحویل داد.',
        ];
        
        return $comments[array_rand($comments)];
    }

    private function getRandomReply(): string
    {
        $replies = [
            'ممنون از نظر خوبتون. خوشحالیم که راضی هستید.',
            'سپاس از اعتماد شما. امیدواریم همیشه رضایتتون جلب بشه.',
            'متشکریم. موفق و پیروز باشید.',
            'خوشحالیم که محصول مورد پسندتون قرار گرفته.',
        ];
        
        return $replies[array_rand($replies)];
    }

    private function getRandomQuestion(): string
    {
        $questions = [
            'آیا این محصول گارانتی داره؟',
            'زمان ارسال چقدر طول میکشه؟',
            'آیا امکان بازگشت کالا وجود داره؟',
            'محصول اصل هست یا کپی؟',
            'آیا رنگ‌های دیگه‌ای هم موجود هست؟',
            'آیا امکان پرداخت اقساطی وجود داره؟',
        ];
        
        return $questions[array_rand($questions)];
    }

    private function getRandomAnswer(): string
    {
        $answers = [
            'بله، محصول دارای گارانتی معتبر است.',
            'زمان ارسال بین ۲ تا ۵ روز کاری است.',
            'بله، تا ۷ روز امکان بازگشت کالا وجود دارد.',
            'محصول کاملا اصل و اورجینال است.',
            'در حال حاضر فقط همین رنگ موجود است.',
            'متاسفانه امکان پرداخت اقساطی وجود ندارد.',
        ];
        
        return $answers[array_rand($answers)];
    }
}
