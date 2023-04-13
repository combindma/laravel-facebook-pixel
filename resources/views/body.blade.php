@if($enabled)
@unless(empty($eventLayer->toArray()))
<!-- Meta Pixel Events -->
<script>
@foreach($eventLayer->toArray() as $eventName => $metaPixel)
@if(empty($metaPixel['event_id']) && empty($metaPixel['data']))
    fbq('track', '{{ $eventName }}');
@elseif(empty($metaPixel['event_id']))
    fbq('track', '{{ $eventName }}', {!! json_encode($metaPixel['data']) !!});
@else
    fbq('track', '{{ $eventName }}', {!! json_encode($metaPixel['data']) !!}, {eventID: '{{ $metaPixel['event_id'] }}'});
@endif
@endforeach
</script>
<!-- End Meta Pixel Events -->
@endunless
@unless(empty($customEventLayer->toArray()))
<!-- Meta Pixel Custom Events -->
<script>
@foreach($customEventLayer->toArray() as $customEventName => $metaPixel)
@if(empty($metaPixel['event_id']) && empty($metaPixel['data']))
   fbq('trackCustom', '{{ $customEventName }}');
@elseif(empty($metaPixel['event_id']))
    fbq('trackCustom', '{{ $customEventName }}', {!! json_encode($metaPixel['data']) !!});
@else
   fbq('trackCustom', '{{ $customEventName }}', {!! json_encode($metaPixel['data']) !!}, {eventID: '{{ $metaPixel['event_id'] }}'});
@endif
@endforeach
</script>
<!-- End Meta Custom Pixel Events -->
@endunless
@endif
