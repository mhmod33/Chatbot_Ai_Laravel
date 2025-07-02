@component('mail::message')
# Reminder

You asked to be reminded:

**Task:** {{ $reminder->task }}

**Time:** {{ $reminder->remind_at }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
