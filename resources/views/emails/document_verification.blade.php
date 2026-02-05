@extends('emails.default')

@section('content')
<tr>
    <td style="padding:32px;">
        <h2 style="margin-top:0; color:#2f3446; font-size:20px;">
            Document Verification Update
        </h2>

        <p style="color:#444; font-size:14px; line-height:1.7;">
            Hello {{ $user->first_name ?? 'User' }},
        </p>

        <p style="color:#444; font-size:14px; line-height:1.7;">
            The status of your submitted document has been updated.
        </p>

        <!-- Status Box -->
        <p style="margin:24px 0; padding:14px 18px; background:#f8fafc; border-left:4px solid
            {{ $status === 'verified' ? '#22c55e' : '#ef4444' }};">
            <strong>Status:</strong>
            <span style="text-transform:capitalize;">
                {{ $status }}
            </span>
        </p>

        @if ($status === 'rejected' && $note)
            <p style="color:#444; font-size:14px; line-height:1.7;">
                <strong>Reason for Rejection:</strong><br>
                {{ $note }}
            </p>
        @endif

        @if ($status === 'verified')
            <p style="color:#444; font-size:14px; line-height:1.7;">
                Your document has been successfully verified.
            </p>
        @else
            <p style="color:#444; font-size:14px; line-height:1.7;">
                Please review the reason above and re-upload the document if required.
            </p>
        @endif

        <p style="color:#444; font-size:14px; line-height:1.7; margin-top:24px;">
            If you have any questions, feel free to contact our support team.
        </p>
    </td>
</tr>
@endsection
