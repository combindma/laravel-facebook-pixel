@if($metaPixel->isEnabled())
@unless(empty($eventLayer))
<!-- Meta Pixel Events -->
<script>
@foreach($eventLayer as $event)
@if(empty($event['event_id']) && empty($event['data']))
    fbq('track', '{{ $event['event_name'] }}');
@elseif(empty($event['event_id']))
    fbq('track', '{{ $event['event_name'] }}', {{ Js::from($event['data']) }});
@else
    fbq('track', '{{ $event['event_name'] }}', {{ Js::from($event['data']) }}, {eventID: '{{ $event['event_id'] }}'});
@endif
@endforeach
</script>
<!-- End Meta Pixel Events -->
@endunless
@unless(empty($customEventLayer))
<!-- Meta Pixel Custom Events -->
<script>
@foreach($customEventLayer as $event)
@if(empty($event['event_id']) && empty($event['data']))
   fbq('trackCustom', '{{ $event['event_name'] }}');
@elseif(empty($event['event_id']))
    fbq('trackCustom', '{{ $event['event_name'] }}', {{ Js::from($event['data']) }});
@else
   fbq('trackCustom', '{{ $event['event_name'] }}', {{ Js::from($event['data']) }}, {eventID: '{{ $event['event_id'] }}'});
@endif
@endforeach
</script>
<!-- End Meta Custom Pixel Events -->
@endunless
@endif
