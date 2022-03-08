@if($enabled)
@unless(empty($eventLayer->toArray()))
<!-- Facebook Pixel Events -->
<script>
@foreach($eventLayer->toArray() as $eventName => $parameters)
@if(empty($parameters))
    fbq('track', '{{ $eventName }}');
@else
    fbq('track', '{{ $eventName }}', {!! json_encode($parameters) !!});
@endif
@endforeach
</script>
<!-- End Facebook Pixel Events -->
@endunless
@unless(empty($customEventLayer->toArray()))
<!-- Facebook Pixel Custom Events -->
<script>
@foreach($customEventLayer->toArray() as $customEventName => $parameters)
@if(empty($parameters))
   fbq('trackCustom', '{{ $customEventName }}');
@else
   fbq('trackCustom', '{{ $customEventName }}', {!! json_encode($parameters) !!});
@endif
@endforeach
</script>
<!-- End Facebook Custom Pixel Events -->
@endunless
@endif
