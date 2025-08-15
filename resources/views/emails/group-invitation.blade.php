@component('mail::message')
# Group Invitation

You have been invited to join the group **{{ $invitation->group->name }}** in **{{ $invitation->group->organization->name }}**.

@component('mail::button', ['url' => $url])
Accept Invitation
@endcomponent

This invitation link will expire on {{ $invitation->expires_at->format('F j, Y') }}.

Thanks,<br>
{{ config('app.name') }}
@endcomponent