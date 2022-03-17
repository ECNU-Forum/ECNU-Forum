@php ($page = $apiDocument->data->attributes)

<div class="container">
    <h2>{{ $page->title }}</h2>

    <div>
        {!! $page->contentHtml !!}
    </div>
</div>
