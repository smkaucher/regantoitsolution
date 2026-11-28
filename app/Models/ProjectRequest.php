<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** A "Start a project" brief from the Contact page. */
class ProjectRequest extends Model
{
    public const BUDGETS = ['Under ৳50k', '৳50k – 2 lakh', '৳2 – 5 lakh', '৳5 lakh+'];

    public const TIMELINES = ['As soon as possible', 'In the next month', 'In 1–3 months', 'Just exploring'];

    /** "spam": the hidden anti-bot field was filled. Kept out of "All" but never thrown away. */
    public const STATUSES = ['new' => 'New', 'contacted' => 'Contacted', 'closed' => 'Closed', 'spam' => 'Spam'];

    /** A Bangladeshi mobile number: 11 digits, 01 then an operator digit 3-9. */
    public const PHONE_RULE = '/^01[3-9]\d{8}$/';

    protected $guarded = [];

    protected $casts = ['services' => 'array'];

    /** Digits only (Bangla digits ০-৯ count too), with a +880 / 880 country code turned into the local 0. */
    public static function normalisePhone(?string $phone): string
    {
        $phone = strtr((string) $phone, ['০' => '0', '১' => '1', '২' => '2', '৩' => '3', '৪' => '4', '৫' => '5', '৬' => '6', '৭' => '7', '৮' => '8', '৯' => '9']);
        $d = preg_replace('/\D/', '', $phone);
        if (str_starts_with($d, '880') && strlen($d) === 13) {
            $d = '0' . substr($d, 3);
        }

        return $d;
    }
}
