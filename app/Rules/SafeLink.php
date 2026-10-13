<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * A link an admin can put in the header: a site path (/about, /#faq), an anchor (#contact),
 * a full http(s) URL, mailto: or tel:. Blocks javascript: and other schemes.
 */
class SafeLink implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $value = trim((string) $value);

        $ok = preg_match('~^(/(?!/)\S*|#\S*|https?://[^\s/$.?#][^\s]*|mailto:\S+@\S+|tel:\+?[0-9\s\-()]+)$~i', $value);

        if (! $ok) {
            $fail('Enter a page path like /about or /#faq, a full URL starting with https://, or a mailto:/tel: link.');
        }
    }
}
