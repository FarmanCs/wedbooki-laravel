<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupportQuerySeeder extends Seeder
{
    public function run(): void
    {
        $supportQueries = [
            // High Priority - Urgent Issues
            [
                'full_name' => 'Ahmed Hassan',
                'email' => 'ahmed.hassan@example.com',
                'phone_number' => '+92-300-1234567',
                'subject' => 'Payment Failed but Amount Deducted',
                'priority' => 'high',
                'message' => 'I tried to place an order #ORD-12345 and the payment was deducted from my bank account but the order shows as failed. Please refund my amount of Rs. 15,000 immediately or process my order.',
                'attachments' => json_encode(['screenshots/payment_proof.jpg', 'screenshots/bank_statement.pdf']),
                'status' => 'pending',
                'created_at' => Carbon::now()->subHours(2),
            ],
            [
                'full_name' => 'Fatima Khan',
                'email' => 'fatima.k@gmail.com',
                'phone_number' => '+92-321-9876543',
                'subject' => 'Account Hacked - Unauthorized Access',
                'priority' => 'high',
                'message' => 'Someone has accessed my account and changed my password. I can see orders that I did not place. Please lock my account immediately and help me recover it. This is very urgent!',
                'attachments' => json_encode(['screenshots/unauthorized_orders.jpg']),
                'status' => 'resolved',
                'created_at' => Carbon::now()->subDays(1),
            ],
            [
                'full_name' => 'Bilal Iqbal',
                'email' => 'bilal.iqbal@business.com',
                'phone_number' => '+92-333-4567890',
                'subject' => 'Urgent: Wrong Product Delivered - Business Loss',
                'priority' => 'high',
                'message' => 'I ordered 50 units of Product A for my store opening tomorrow, but you delivered Product B instead. This is causing major business loss. I need immediate replacement or full refund with compensation.',
                'attachments' => json_encode(['photos/wrong_product.jpg', 'documents/invoice.pdf']),
                'status' => 'pending',
                'created_at' => Carbon::now()->subHours(5),
            ],

            // Medium Priority - Important Issues
            [
                'full_name' => 'Sarah Ali',
                'email' => 'sarah.ali@yahoo.com',
                'phone_number' => '+92-300-2345678',
                'subject' => 'Defective Product Received - Need Replacement',
                'priority' => 'medium',
                'message' => 'I received a laptop (Order #ORD-67890) yesterday but the screen has dead pixels and the keyboard is not working properly. I would like to return this and get a replacement. Please guide me through the process.',
                'attachments' => json_encode(['photos/defective_screen.jpg', 'videos/keyboard_issue.mp4']),
                'status' => 'pending',
                'created_at' => Carbon::now()->subHours(8),
            ],
            [
                'full_name' => 'Usman Malik',
                'email' => 'usman.m@hotmail.com',
                'phone_number' => '+92-345-6789012',
                'subject' => 'Tracking Not Updating - Order Delayed',
                'priority' => 'medium',
                'message' => 'My order #ORD-45678 was supposed to be delivered 3 days ago but the tracking shows it is still in Karachi. I am in Lahore and need this urgently. Can you please check with the courier?',
                'attachments' => null,
                'status' => 'resolved',
                'created_at' => Carbon::now()->subDays(3),
            ],
            [
                'full_name' => 'Ayesha Siddiqui',
                'email' => 'ayesha.siddiqui@gmail.com',
                'phone_number' => '+92-320-8901234',
                'subject' => 'Promotional Code Not Working',
                'priority' => 'medium',
                'message' => 'I am trying to use the promo code "SAVE20" that I received via email for 20% off but it keeps saying "Invalid Code". The email says it is valid until 31st December. Please help.',
                'attachments' => json_encode(['screenshots/promo_error.jpg', 'screenshots/email.jpg']),
                'status' => 'resolved',
                'created_at' => Carbon::now()->subDays(2),
            ],
            [
                'full_name' => 'Hassan Raza',
                'email' => 'hassan.raza@outlook.com',
                'phone_number' => '+92-301-1122334',
                'subject' => 'Return Request - Size Issue',
                'priority' => 'medium',
                'message' => 'I ordered shoes in size 42 but they are too tight. I want to exchange them for size 43. Order number is #ORD-78901. What is the return process and how long will it take?',
                'attachments' => json_encode(['photos/shoes_order.jpg']),
                'status' => 'pending',
                'created_at' => Carbon::now()->subHours(12),
            ],

            // Low Priority - General Inquiries
            [
                'full_name' => 'Zainab Ahmed',
                'email' => 'zainab.a@gmail.com',
                'phone_number' => '+92-322-3344556',
                'subject' => 'Question About Product Warranty',
                'priority' => 'low',
                'message' => 'I purchased a mobile phone 3 months ago. Can you please tell me what is covered under warranty? Also, if the screen breaks accidentally, will it be covered?',
                'attachments' => null,
                'status' => 'resolved',
                'created_at' => Carbon::now()->subDays(5),
            ],
            [
                'full_name' => 'Ali Haider',
                'email' => 'ali.haider@student.edu.pk',
                'phone_number' => '+92-334-5566778',
                'subject' => 'Product Availability Inquiry',
                'priority' => 'low',
                'message' => 'Hi, I want to buy the Samsung Galaxy S24 but it shows out of stock. When will it be back in stock? Can I pre-order it? Also, do you offer any student discounts?',
                'attachments' => null,
                'status' => 'pending',
                'created_at' => Carbon::now()->subHours(15),
            ],
            [
                'full_name' => 'Maria Khan',
                'email' => 'maria.khan@company.pk',
                'phone_number' => '+92-300-7788990',
                'subject' => 'Bulk Order Discount Inquiry',
                'priority' => 'low',
                'message' => 'Our company wants to order 20 laptops for our employees. Do you provide bulk order discounts? Can we get corporate pricing? Please share a quote.',
                'attachments' => null,
                'status' => 'pending',
                'created_at' => Carbon::now()->subHours(20),
            ],
            [
                'full_name' => 'Imran Sher',
                'email' => 'imran.sher@gmail.com',
                'phone_number' => '+92-315-9900112',
                'subject' => 'How to Update My Profile Information',
                'priority' => 'low',
                'message' => 'I moved to a new address and want to update my delivery address in my account. I tried to do it but could not find the option. Can you please guide me?',
                'attachments' => null,
                'status' => 'resolved',
                'created_at' => Carbon::now()->subDays(4),
            ],
            [
                'full_name' => 'Sana Tariq',
                'email' => 'sana.tariq@yahoo.com',
                'phone_number' => '+92-321-2233445',
                'subject' => 'Request for Invoice Copy',
                'priority' => 'low',
                'message' => 'I need a copy of my invoice for order #ORD-34567 for my office records. Can you please email it to me? I cannot find it in my account.',
                'attachments' => null,
                'status' => 'resolved',
                'created_at' => Carbon::now()->subDays(6),
            ],

            // Rejected Cases
            [
                'full_name' => 'Hamza Nasir',
                'email' => 'hamza.n@gmail.com',
                'phone_number' => '+92-333-5544667',
                'subject' => 'Refund Request After 60 Days',
                'priority' => 'medium',
                'message' => 'I want to return a product I bought 60 days ago. It is not working properly now. Please process my refund.',
                'attachments' => null,
                'status' => 'rejected',
                'created_at' => Carbon::now()->subDays(7),
            ],
            [
                'full_name' => 'Nida Malik',
                'email' => 'nida.malik@hotmail.com',
                'phone_number' => '+92-300-6677889',
                'subject' => 'Return Without Original Packaging',
                'priority' => 'low',
                'message' => 'I want to return a product but I threw away the original box. Can I still return it?',
                'attachments' => null,
                'status' => 'rejected',
                'created_at' => Carbon::now()->subDays(8),
            ],
        ];

        foreach ($supportQueries as $query) {
            DB::table('support_queries')->insert(array_merge($query, [
                'updated_at' => $query['created_at'],
            ]));
        }
    }
}
