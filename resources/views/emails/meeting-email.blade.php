  @extends('emails.default')

  @section('content')
      <tr>
          <td style="padding:32px;">
              <h2 style="margin-top:0; color:#2f3446; font-size:20px;">
                  Meeting Status Update
              </h2>

              <p style="color:#444; font-size:14px; line-height:1.7;">
                  Hello {{ $meeting->user->first_name ?? 'User' }},
              </p>

              <p style="color:#444; font-size:14px; line-height:1.7;">
                  This is to inform you that the status of your scheduled meeting has been updated.
              </p>

              <!-- Status Box -->
              <p style="margin:24px 0; padding:14px 18px; background:#f8fafc; border-left:4px solid">
                  <strong>Status:</strong>
                  {!! meetingStatus($meeting->status) !!}
              </p>

              <p style="color:#444; font-size:14px; line-height:1.7;">
                  <strong>Date:</strong> {{ \Carbon\Carbon::parse($meeting->meeting_date)->format('d M Y') }} <br>
                  <strong>Time:</strong> {{ $meeting->meeting_time }}
              </p>

              @if ($meeting->status === 'rejected' && $meeting->notes)
                  <p style="color:#444; font-size:14px; line-height:1.7;">
                      <strong>Reason:</strong> {{ $meeting->notes }}
                  </p>
              @endif

              @if ($meeting->status === 'approved')
                  <p style="color:#444; font-size:14px; line-height:1.7;">
                      Your meeting has been approved. Please be available at the scheduled time.
                  </p>
              @elseif($meeting->status === 'rescheduled')
                  @if ($meeting->admin_notes)
                      <br><strong>Reason:</strong> {{ $meeting->admin_notes }}
                  @endif
                  <p style="color:#444; font-size:14px; line-height:1.7;">
                      We are unable to proceed with the originally scheduled time. Kindly reschedule your meeting by
                      selecting a new date and time.
                  </p>
              @elseif($meeting->status === 'rejected')
                  <p style="color:#444; font-size:14px; line-height:1.7;">
                      Unfortunately, your meeting request has been rejected.
                      @if ($meeting->admin_notes)
                          <br><strong>Reason:</strong> {{ $meeting->admin_notes }}
                      @endif
                      <br>Please feel free to submit a new request with a different date and time.
                  </p>
              @endif

          </td>
      </tr>
  @endsection
