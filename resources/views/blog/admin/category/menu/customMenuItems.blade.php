@foreach($items as $item)
    <p class="item-p">
        {{$delimiter}} <a class="p-1" href="{{route('blog.admin.categories.edit', $item->id)}}">{{$item->title}}</a>

        <span>
            @if(!$item->hasChildren())
                <a href="{{route('blog.admin.categories.mydel').'?id='.$item->id}}" class="delete">
                    <i class="fa fa-fw fa-close text-danger"></i>
                </a>
            @else
                <i class="fa fa-fw fa-close"></i>
            @endif
        </span>
    </p>

    @if($item->hasChildren())
        <div class="list-group">
            @include(env('THEME').'blog.admin.category.menu.customMenuItems',
            ['items' => $item->children(), 'delimiter' => '-' . $delimiter])
        </div>
    @endif
@endforeach
