<div class="border border-white/10 rounded-xl p-4 mt-4 bg-white/[0.02]">
    <p class="text-white/70 text-sm font-medium mb-1">لون مخصص</p>
    <p class="text-white/30 text-xs mb-3">لو اللون درجة مش موجودة في القائمة — اختار اللون من البلtte وسمّيه</p>
    <div class="flex flex-wrap items-end gap-3">
        <div>
            <label class="block text-white/40 text-xs mb-1">اللون</label>
            <input type="color" id="custom-color-hex" value="#808080"
                class="w-12 h-10 rounded cursor-pointer border border-white/10 bg-transparent p-0.5">
        </div>
        <div class="flex-1 min-w-[160px]">
            <label class="block text-white/40 text-xs mb-1">اسم اللون</label>
            <input type="text" id="custom-color-name" placeholder="مثال: رمادي فاتح، بيج داكن"
                class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-brand-red">
        </div>
        <button type="button" onclick="addCustomColor()"
            class="px-4 py-2 rounded-lg bg-brand-red/90 hover:bg-brand-red text-white text-sm font-medium transition-colors">
            + إضافة اللون
        </button>
    </div>
    <p id="custom-color-error" class="text-red-400 text-xs mt-2 hidden"></p>
</div>
