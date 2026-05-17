"use client";

import Link from "next/link";
import { Heart, ArrowRight } from "lucide-react";
import { Button } from "@/components/ui/button";

export default function WishlistPage() {
  return (
    <div className="bg-brand-black min-h-screen" dir="rtl">
      <div className="mx-auto max-w-7xl px-4 py-8">
        <h1 className="text-3xl font-heading font-bold text-white mb-8">المفضلة</h1>

        <div className="bg-brand-dark rounded-xl p-12 text-center">
          <Heart size={64} className="text-white/10 mx-auto mb-4" />
          <h2 className="text-xl font-heading font-semibold text-white mb-2">قائمة المفضلة فاضية</h2>
          <p className="text-white/50 mb-6">ضيف منتجات للمفضلة عشان ترجعلها بسهولة بعدين.</p>
          <Link href="/shop">
            <Button className="bg-brand-red hover:bg-brand-red-dark text-white font-semibold px-8 py-3 rounded-xl">
              تصفح المنتجات
              <ArrowRight size={18} className="mr-2 rotate-180" />
            </Button>
          </Link>
        </div>
      </div>
    </div>
  );
}
