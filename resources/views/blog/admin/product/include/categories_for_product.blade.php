@foreach($categories as $v)
    <option value="{{$v->id}}"
        @isset($product->id)
            @if($v->id == $product->category_id)
                selected
            @endif
        @endisset
    >
        {!! $delimiter ?? "" !!} {{ $v->title ?? "" }}
    </option>

    @if(count($v->children))
        @include('blog.admin.product.include.categories_for_product',
        [
            'categories' => $v->children,
            'delimiter' => '-' . $delimiter,
            'product' => $product,
        ]
        )
    @endif

@endforeach
