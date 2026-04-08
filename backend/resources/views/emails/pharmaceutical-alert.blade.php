<x-mail::message>
# ⚠ Important Medication Recall Notice

Dear {{ $customerName }},

We are contacting you because you recently purchased a medication that has been associated with a **pharmacovigilance alert**.

<x-mail::panel>
**Affected Medication Lot:** {{ $lotNumber }}
</x-mail::panel>

## What you need to do

- **Stop using** the medication immediately
- **Contact our pharmacy** as soon as possible
- **Do not discard** the medication — return it to us for proper disposal

## Why are we contacting you?

Our pharmacovigilance system has identified that medication lot **#{{ $lotNumber }}** may pose a health risk. As a precautionary measure, we are notifying all customers who purchased this lot.

If you have any questions or are experiencing any adverse effects, please contact us immediately.

<x-mail::button url="#">
Contact the Pharmacy
</x-mail::button>

We apologize for any inconvenience and thank you for your cooperation in this important health matter.

Regards,<br>
{{ config('app.name') }} — Pharmacovigilance Department
</x-mail::message>
