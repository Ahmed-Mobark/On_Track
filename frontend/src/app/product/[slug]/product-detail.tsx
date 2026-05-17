"use client";

import Image from "next/image";
import Link from "next/link";
import { useState } from "react";
import { motion } from "framer-motion";
import { Heart, ShoppingBag, Minus, Plus, Star, ChevronRight } from "lucide-react";
import { Button } from "@/components/ui/button";

const allProducts = Array.from({ length: 8 }, (_, i) => ({
  id: `${i + 1}`,
  name: [
    "تيشيرت رياضي بريميوم",
    "شورت أداء عالي",
    "هودي أوفرسايز",
    "بنطلون جوجر سليم",
    "تانك توب",
    "توب ضغط رياضي",
    "بنطلون تراك",
    "سبورتس برا",
  ][i],
  slug: `product-${i + 1}`,
  basePrice: [299, 349, 599, 499, 249, 399, 449, 329][i],
  salePrice:
    i % 3 === 0 ? [249, 0, 0, 399, 0, 0, 0, 279][i] || undefined : undefined,
  description:
    "مصمم لأعلى أداء مع قماش بريميوم يسمح بالتهوية. مصنوع للراحة أثناء التمارين المكثفة مع الحفاظ على مظهر عصري وأنيق. يتميز بتقنية امتصاص الرطوبة ومرونة رباعية الاتجاهات لحرية الحركة.",
  materials: "92% بوليستر، 8% إيلاستين",
  care: "غسيل بارد في الغسالة. لا تستخدم المبيض. تجفيف على حرارة منخفضة.",
  images: [] as { url: string; alt?: string }[],
  variants: {
    sizes: ["S", "M", "L", "XL", "XXL"],
    colors: [
      { name: "أسود", hex: "#0A0A0A" },
      { name: "أبيض", hex: "#F5F5F5" },
      { name: "أحمر", hex: "#E63946" },
    ],
  },
  avgRating: 4.5,
  totalReviews: 24,
}));

export default function ProductDetail({ slug }: { slug: string }) {
  const product = allProducts.find((p) => p.slug === slug) || allProducts[0];

  const [selectedSize, setSelectedSize] = useState("");
  const [selectedColor, setSelectedColor] = useState(product.variants.colors[0].name);
  const [quantity, setQuantity] = useState(1);
  const [activeTab, setActiveTab] = useState<"description" | "materials" | "reviews">("description");

  const hasDiscount = product.salePrice && product.salePrice < product.basePrice;
  const currentPrice = hasDiscount ? product.salePrice! : product.basePrice;

  return (
    <div className="bg-brand-black min-h-screen" dir="rtl">
      {/* Breadcrumb */}
      <div className="mx-auto max-w-7xl px-4 py-4">
        <nav className="flex items-center gap-2 text-sm text-white/40">
          <Link href="/" className="hover:text-white">الرئيسية</Link>
          <ChevronRight size={14} className="rotate-180" />
          <Link href="/shop" className="hover:text-white">المتجر</Link>
          <ChevronRight size={14} className="rotate-180" />
          <span className="text-white/70">{product.name}</span>
        </nav>
      </div>

      <div className="mx-auto max-w-7xl px-4 pb-20">
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-16">
          {/* Images */}
          <motion.div
            initial={{ opacity: 0, x: 20 }}
            animate={{ opacity: 1, x: 0 }}
          >
            <div className="aspect-[3/4] bg-brand-dark rounded-2xl overflow-hidden relative">
              <div className="w-full h-full flex items-center justify-center">
                <span className="text-white/10 text-8xl font-heading font-black">OT</span>
              </div>
              {hasDiscount && (
                <span className="absolute top-4 right-4 bg-brand-red text-white text-sm font-bold px-3 py-1 rounded-lg">
                  -{Math.round(((product.basePrice - product.salePrice!) / product.basePrice) * 100)}%
                </span>
              )}
            </div>
            <div className="grid grid-cols-4 gap-2 mt-2">
              {[0, 1, 2, 3].map((i) => (
                <div key={i} className="aspect-square bg-brand-dark rounded-lg flex items-center justify-center cursor-pointer border-2 border-transparent hover:border-brand-red/50 transition-colors">
                  <span className="text-white/10 text-xl font-heading font-black">OT</span>
                </div>
              ))}
            </div>
          </motion.div>

          {/* Product Info */}
          <motion.div
            initial={{ opacity: 0, x: -20 }}
            animate={{ opacity: 1, x: 0 }}
            className="flex flex-col"
          >
            <h1 className="text-3xl lg:text-4xl font-heading font-bold text-white mb-2">
              {product.name}
            </h1>

            <div className="flex items-center gap-2 mb-4">
              <div className="flex items-center gap-0.5">
                {[1, 2, 3, 4, 5].map((star) => (
                  <Star
                    key={star}
                    size={16}
                    className={star <= Math.floor(product.avgRating) ? "text-yellow-400 fill-yellow-400" : "text-white/20"}
                  />
                ))}
              </div>
              <span className="text-white/50 text-sm">({product.totalReviews} تقييم)</span>
            </div>

            <div className="flex items-center gap-3 mb-6">
              <span className="text-3xl font-bold text-white">{currentPrice} ج.م</span>
              {hasDiscount && (
                <span className="text-xl text-white/40 line-through">{product.basePrice} ج.م</span>
              )}
            </div>

            {/* Color */}
            <div className="mb-6">
              <h3 className="text-sm font-semibold text-white/70 uppercase tracking-wider mb-3">
                اللون: <span className="text-white">{selectedColor}</span>
              </h3>
              <div className="flex gap-3">
                {product.variants.colors.map((color) => (
                  <button
                    key={color.name}
                    onClick={() => setSelectedColor(color.name)}
                    className={`w-10 h-10 rounded-full border-2 transition-all ${
                      selectedColor === color.name
                        ? "border-brand-red scale-110"
                        : "border-white/20 hover:border-white/40"
                    }`}
                    style={{ backgroundColor: color.hex }}
                  />
                ))}
              </div>
            </div>

            {/* Size */}
            <div className="mb-6">
              <div className="flex items-center justify-between mb-3">
                <h3 className="text-sm font-semibold text-white/70 uppercase tracking-wider">المقاس</h3>
                <button className="text-brand-red text-sm hover:underline">دليل المقاسات</button>
              </div>
              <div className="flex flex-wrap gap-2">
                {product.variants.sizes.map((size) => (
                  <button
                    key={size}
                    onClick={() => setSelectedSize(size)}
                    className={`px-5 py-2.5 rounded-lg font-medium text-sm transition-all ${
                      selectedSize === size
                        ? "bg-brand-red text-white"
                        : "bg-white/5 text-white/70 border border-white/10 hover:border-brand-red/50"
                    }`}
                  >
                    {size}
                  </button>
                ))}
              </div>
            </div>

            {/* Quantity */}
            <div className="mb-8">
              <h3 className="text-sm font-semibold text-white/70 uppercase tracking-wider mb-3">الكمية</h3>
              <div className="flex items-center gap-3">
                <div className="flex items-center border border-white/10 rounded-lg">
                  <button onClick={() => setQuantity(Math.max(1, quantity - 1))} className="px-3 py-2 text-white/60 hover:text-white">
                    <Minus size={16} />
                  </button>
                  <span className="px-4 py-2 text-white font-medium min-w-[40px] text-center">{quantity}</span>
                  <button onClick={() => setQuantity(quantity + 1)} className="px-3 py-2 text-white/60 hover:text-white">
                    <Plus size={16} />
                  </button>
                </div>
              </div>
            </div>

            {/* Actions */}
            <div className="flex gap-3 mb-4">
              <Button className="flex-1 bg-brand-red hover:bg-brand-red-dark text-white font-semibold py-6 text-lg rounded-xl">
                <ShoppingBag size={20} className="ml-2" />
                أضف للسلة
              </Button>
              <Button variant="outline" size="icon" className="h-auto w-14 border-white/10 text-white/60 hover:text-brand-red hover:border-brand-red/50 rounded-xl">
                <Heart size={22} />
              </Button>
            </div>

            <a
              href={`https://wa.me/201000000000?text=مهتم بـ ${product.name}`}
              className="flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-xl transition-colors mb-8"
            >
              اطلب عبر واتساب
            </a>

            {/* Tabs */}
            <div className="border-t border-white/10 pt-6">
              <div className="flex gap-6 mb-4">
                {([
                  { key: "description" as const, label: "الوصف" },
                  { key: "materials" as const, label: "الخامات" },
                  { key: "reviews" as const, label: "التقييمات" },
                ]).map((tab) => (
                  <button
                    key={tab.key}
                    onClick={() => setActiveTab(tab.key)}
                    className={`text-sm font-semibold pb-2 border-b-2 transition-colors ${
                      activeTab === tab.key
                        ? "text-brand-red border-brand-red"
                        : "text-white/40 border-transparent hover:text-white/60"
                    }`}
                  >
                    {tab.label}
                  </button>
                ))}
              </div>
              <div className="text-white/60 text-sm leading-relaxed">
                {activeTab === "description" && <p>{product.description}</p>}
                {activeTab === "materials" && (
                  <div>
                    <p className="mb-2"><strong className="text-white/80">الخامات:</strong> {product.materials}</p>
                    <p><strong className="text-white/80">العناية:</strong> {product.care}</p>
                  </div>
                )}
                {activeTab === "reviews" && (
                  <p className="text-white/40 text-center py-4">لا توجد تقييمات بعد. كن أول من يقيّم هذا المنتج.</p>
                )}
              </div>
            </div>
          </motion.div>
        </div>
      </div>

      {/* Sticky Mobile Add to Cart */}
      <div className="lg:hidden fixed bottom-16 left-0 right-0 z-40 bg-brand-black/95 backdrop-blur border-t border-white/10 p-4">
        <div className="flex items-center gap-3">
          <div>
            <span className="text-white font-bold text-lg">{currentPrice} ج.م</span>
          </div>
          <Button className="flex-1 bg-brand-red hover:bg-brand-red-dark text-white font-semibold py-3 rounded-xl">
            <ShoppingBag size={18} className="ml-2" />
            أضف للسلة
          </Button>
        </div>
      </div>
    </div>
  );
}
