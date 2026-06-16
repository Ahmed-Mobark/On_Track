@props(['image', 'primaryImageId'])

<div class="relative rounded-lg overflow-hidden border {{ $image->id === $primaryImageId ? 'border-brand-red ring-1 ring-brand-red/40' : 'border-white/10' }}">
    <img src="{{ $image->image_url }}" class="w-full aspect-square object-cover bg-white/5">
    @if($image->id === $primaryImageId)
        <span class="absolute top-1 left-1 bg-brand-red text-white text-[9px] font-bold px-1.5 py-0.5 rounded z-10">أساسية</span>
    @endif

    <input type="checkbox" name="delete_images[]" value="{{ $image->id }}" id="delete-image-{{ $image->id }}" class="peer hidden">
    <label for="delete-image-{{ $image->id }}" class="absolute top-1 right-1 z-20 cursor-pointer">
        <span class="w-6 h-6 flex items-center justify-center bg-black/70 hover:bg-red-600 text-white rounded-full text-base leading-none peer-checked:bg-red-600">&times;</span>
    </label>
    <span class="absolute inset-0 hidden peer-checked:flex items-center justify-center bg-red-600/50 text-white text-[10px] font-bold z-10 pointer-events-none">سيتم الحذف</span>

    <label class="absolute bottom-0 inset-x-0 z-20 cursor-pointer bg-black/75 px-2 py-1 flex items-center justify-center gap-1.5">
        <input type="radio" name="primary_image_id" value="{{ $image->id }}" class="accent-brand-red" {{ $image->id === $primaryImageId ? 'checked' : '' }}>
        <span class="text-white text-[10px]">صورة أساسية</span>
    </label>
</div>
