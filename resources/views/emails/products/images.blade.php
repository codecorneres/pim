@component('mail::message')
Hi

Please check attchment to view products missing images.

Store: {{ $domain }}

@component('mail::button', ['url' => $url])
View Products
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
