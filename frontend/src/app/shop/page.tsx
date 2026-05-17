"use client";

import { useState } from "react";
import { ProductCard } from "@/components/home/product-card";
import { Button } from "@/components/ui/button";
import { SlidersHorizontal, X } from "lucide-react";
import { motion, AnimatePresence } from "framer-motion";

const mockProducts = Array.from({ length: 12 }, (_, i) => ({
  id: `${i + 1}`,
  name: ["تيشيرت رياضي بريميوم", "شورت أداء عالي", "هودي أوفرسايز", "بنطلون جوجر سليم", "تانك توب", "توب ضغط رياضي", "بنطلون تراك", "سبورتس برا", "تيشيرت أوفرسايز", "شورت جيم", "هودي بسوستة", "ليجنز"][i],
  slug: `product-${i + 1}`,
  basePrice: [299, 349, 599, 499, 249, 399, 449, 329, 379, 289, 649, 399][i],
  salePrice: i % 4 === 0 ? [249, 0, 0, 0, 199, 0, 0, 0, 319, 0, 0, 0][i] || undefined : undefined,
  images: [] as { url: string; alt?: string }[],
}));

const sizes = ["XS", "S", "M", "L", "XL", "XXL"];
const colors = ["أسود", "أبيض", "رمادي", "أحمر", "كحلي"];

export default function ShopPage() {
  const [filtersOpen, setFiltersOpen] = useState(false);

  return (
    <div className="bg-brand-black min-h-screen">
      <div className="mx-auto max-w-7xl px-4 py-8">
        <div className="flex items-center justify-between mb-8">
          <div>
            <h1 className="text-3xl font-heading font-bold text-white">كل المنتجات</h1>
            <p className="text-white/40 text-sm mt-1">{mockProducts.length} منتج</p>
          </div>
          <div className="flex items-center gap-3">
            <select className="bg-brand-dark border border-white/10 text-white text-sm rounded-lg px-4 py-2 focus:outline-none focus:border-brand-red">
              <option>الأحدث</option>
              <option>الأكثر مبيعاً</option>
              <option>السعر: من الأقل</option>
              <option>السعر: من الأعلى</option>
            </select>
            <Button
              variant="outline"
              size="sm"
              className="lg:hidden border-white/10 text-white"
              onClick={() => setFiltersOpen(true)}
            >
              <SlidersHorizontal size={16} className="mr-2" />
              فلاتر
            </Button>
          </div>
        </div>

        <div className="flex gap-8">
          <aside className="hidden lg:block w-60 shrink-0">
            <div className="sticky top-24 space-y-6">
              <div>
                <h3 className="text-white font-semibold mb-3 text-sm uppercase tracking-wider">التصنيف</h3>
                <div className="space-y-2">
                  {["الكل", "رجالي", "حريمي", "أوفرسايز", "كومبريشن", "شورتات", "أطقم"].map((cat) => (
                    <label key={cat} className="flex items-center gap-2 text-white/60 hover:text-white text-sm cursor-pointer">
                      <input type="checkbox" className="rounded border-white/20 bg-transparent accent-brand-red" />
                      {cat}
                    </label>
                  ))}
                </div>
              </div>
              <div>
                <h3 className="text-white font-semibold mb-3 text-sm uppercase tracking-wider">المقاس</h3>
                <div className="flex flex-wrap gap-2">
                  {sizes.map((size) => (
                    <button key={size} className="px-3 py-1.5 border border-white/10 rounded text-white/60 text-sm hover:border-brand-red hover:text-brand-red transition-colors">
                      {size}
                    </button>
                  ))}
                </div>
              </div>
              <div>
                <h3 className="text-white font-semibold mb-3 text-sm uppercase tracking-wider">اللون</h3>
                <div className="space-y-2">
                  {colors.map((color) => (
                    <label key={color} className="flex items-center gap-2 text-white/60 hover:text-white text-sm cursor-pointer">
                      <input type="checkbox" className="rounded border-white/20 bg-transparent accent-brand-red" />
                      {color}
                    </label>
                  ))}
                </div>
              </div>
              <div>
                <h3 className="text-white font-semibold mb-3 text-sm uppercase tracking-wider">نطاق السعر</h3>
                <div className="flex items-center gap-2">
                  <input type="number" placeholder="أقل" className="w-full bg-brand-dark border border-white/10 rounded px-3 py-2 text-white text-sm focus:outline-none focus:border-brand-red" />
                  <span className="text-white/40">-</span>
                  <input type="number" placeholder="أكثر" className="w-full bg-brand-dark border border-white/10 rounded px-3 py-2 text-white text-sm focus:outline-none focus:border-brand-red" />
                </div>
              </div>
            </div>
          </aside>

          <div className="flex-1">
            <div className="grid grid-cols-2 md:grid-cols-3 gap-4 lg:gap-6">
              {mockProducts.map((product) => (
                <ProductCard key={product.id} product={product} />
              ))}
            </div>
          </div>
        </div>
      </div>

      <AnimatePresence>
        {filtersOpen && (
          <>
            <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} exit={{ opacity: 0 }} className="fixed inset-0 z-50 bg-black/50" onClick={() => setFiltersOpen(false)} />
            <motion.div initial={{ x: "100%" }} animate={{ x: 0 }} exit={{ x: "100%" }} className="fixed right-0 top-0 bottom-0 z-50 w-80 bg-brand-dark p-6 overflow-y-auto">
              <div className="flex items-center justify-between mb-6">
                <h2 className="text-white font-heading font-bold text-lg">فلاتر</h2>
                <button onClick={() => setFiltersOpen(false)} className="text-white/60"><X size={20} /></button>
              </div>
              <div className="space-y-6">
                <div>
                  <h3 className="text-white font-semibold mb-3 text-sm uppercase">المقاس</h3>
                  <div className="flex flex-wrap gap-2">
                    {sizes.map((size) => (
                      <button key={size} className="px-3 py-1.5 border border-white/10 rounded text-white/60 text-sm hover:border-brand-red hover:text-brand-red transition-colors">{size}</button>
                    ))}
                  </div>
                </div>
                <div>
                  <h3 className="text-white font-semibold mb-3 text-sm uppercase">اللون</h3>
                  <div className="space-y-2">
                    {colors.map((color) => (
                      <label key={color} className="flex items-center gap-2 text-white/60 text-sm cursor-pointer">
                        <input type="checkbox" className="rounded border-white/20 bg-transparent accent-brand-red" />
                        {color}
                      </label>
                    ))}
                  </div>
                </div>
              </div>
            </motion.div>
          </>
        )}
      </AnimatePresence>
    </div>
  );
}
