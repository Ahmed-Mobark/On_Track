"use client";
import { Card } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Plus, Search } from "lucide-react";

export default function AdminProducts() {
  return (
    <div>
      <div className="flex items-center justify-between mb-8">
        <h1 className="text-2xl font-heading font-bold text-white">المنتجات</h1>
        <Button className="bg-brand-red hover:bg-brand-red-dark text-white">
          <Plus size={18} className="ml-2" />
          إضافة منتج
        </Button>
      </div>
      <div className="mb-6">
        <div className="relative">
          <Search className="absolute right-3 top-1/2 -translate-y-1/2 text-white/40" size={18} />
          <input placeholder="ابحث عن منتج..." className="w-full bg-brand-dark border border-white/10 rounded-lg pr-10 pl-4 py-3 text-white placeholder:text-white/30 focus:outline-none focus:border-brand-red" />
        </div>
      </div>
      <Card className="bg-brand-dark border-white/10 p-8 text-center">
        <p className="text-white/40">لا توجد منتجات بعد. اضغط "إضافة منتج" للبدء.</p>
      </Card>
    </div>
  );
}
