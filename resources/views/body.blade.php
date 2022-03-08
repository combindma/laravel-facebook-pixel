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
@endif
