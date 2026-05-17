"use client";
import { Card } from "@/components/ui/card";

export default function AdminOrders() {
  return (
    <div>
      <div className="flex items-center justify-between mb-8">
        <h1 className="text-2xl font-heading font-bold text-white">الطلبات</h1>
        <select className="bg-brand-dark border border-white/10 text-white text-sm rounded-lg px-4 py-2">
          <option>كل الطلبات</option>
          <option>قيد الانتظار</option>
          <option>مؤكد</option>
          <option>قيد التجهيز</option>
          <option>تم الشحن</option>
          <option>تم التوصيل</option>
          <option>ملغي</option>
        </select>
      </div>
      <Card className="bg-brand-dark border-white/10 p-8 text-center">
        <p className="text-white/40">لا توجد طلبات بعد.</p>
      </Card>
    </div>
  );
}
