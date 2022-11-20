<nav class="flex flex-col mt-8">
    @foreach ($menu as $item)
        <a href="{{ $item->link() }}" class="self-start py-1 text-dark hover:text-pink text-md font-bold">{{ $item->label() }}</a>
        @endforeach
</nav>