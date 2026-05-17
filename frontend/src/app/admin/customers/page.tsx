"use client";
import { Card } from "@/components/ui/card";
import { Search } from "lucide-react";

export default function AdminCustomers() {
  return (
    <div>
      <h1 className="text-2xl font-heading font-bold text-white mb-8">العملاء</h1>
      <div className="mb-6">
        <div className="relative">
          <Search className="absolute right-3 top-1/2 -translate-y-1/2 text-white/40" size={18} />
          <input placeholder="ابحث عن عميل..." className="w-full bg-brand-dark border border-white/10 rounded-lg pr-10 pl-4 py-3 text-white placeholder:text-white/30 focus:outline-none focus:border-brand-red" />
        </div>
      </div>
      <Card className="bg-brand-dark border-white/10 p-8 text-center">
        <p className="text-white/40">لا يوجد عملاء مسجلين بعد.</p>
      </Card>
    </div>
  );
}
