@component('mail::message')

# {{$maildata['title']}}
{{$maildata['body']}}

<br/>
<div style="font-size:xx-large;font-weight:bold;text-align:center">
    {{$maildata['token']}}
</div>
<br/>

Rahasiakan kode ini dan jangan berikan kode pada siapapun.
<br/>
Terima kasih.
@endcomponent