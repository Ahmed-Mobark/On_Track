"use client";

import Link from "next/link";
import { ShoppingBag, Trash2, Plus, Minus, ArrowRight } from "lucide-react";
import { Button } from "@/components/ui/button";

export default function CartPage() {
  return (
    <div className="bg-brand-black min-h-screen" dir="rtl">
      <div className="mx-auto max-w-7xl px-4 py-8">
        <h1 className="text-3xl font-heading font-bold text-white mb-8">سلة التسوق</h1>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <div className="lg:col-span-2">
            {/* Empty Cart */}
            <div className="bg-brand-dark rounded-xl p-12 text-center">
              <ShoppingBag size={64} className="text-white/10 mx-auto mb-4" />
              <h2 className="text-xl font-heading font-semibold text-white mb-2">سلتك فاضية</h2>
              <p className="text-white/50 mb-6">مفيش منتجات في السلة لسه. ابدأ التسوق دلوقتي!</p>
              <Link href="/shop">
                <Button className="bg-brand-red hover:bg-brand-red-dark text-white font-semibold px-8 py-3 rounded-xl">
                  تسوق الآن
                  <ArrowRight size={18} className="mr-2 rotate-180" />
                </Button>
              </Link>
            </div>
          </div>

          {/* Order Summary */}
          <div className="lg:col-span-1">
            <div className="bg-brand-dark rounded-xl p-6 sticky top-24">
              <h2 className="text-lg font-heading font-semibold text-white mb-4">ملخص الطلب</h2>
              <div className="space-y-3 text-sm">
                <div className="flex justify-between text-white/60">
                  <span>المجموع الفرعي</span>
                  <span>0 ج.م</span>
                </div>
                <div className="flex justify-between text-white/60">
                  <span>الشحن</span>
                  <span>0 ج.م</span>
                </div>
                <div className="border-t border-white/10 pt-3 flex justify-between text-white font-bold text-lg">
                  <span>الإجمالي</span>
                  <span>0 ج.م</span>
                </div>
              </div>
              <Button className="w-full bg-brand-red hover:bg-brand-red-dark text-white font-semibold py-4 rounded-xl mt-6" disabled>
                إتمام الشراء
              </Button>
              <div className="mt-4">
                <input
                  type="text"
                  placeholder="كود الخصم"
                  className="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white text-sm placeholder:text-white/30 focus:outline-none focus:border-brand-red"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
