<a href="{{ route('catalog', $item) }}" class="p-3 sm:p-4 2xl:p-6 rounded-xl bg-card hover:bg-pink text-xxs sm:text-xs lg:text-sm text-white font-semibold
@isset($category)
@if ($category->exists)
@if ($category->title == $item->title)
     bg-pink
@endif
@endif
@endisset">{{ $item->title }}</a>
