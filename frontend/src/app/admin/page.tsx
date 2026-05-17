"use client";

import { Card } from "@/components/ui/card";
import { DollarSign, ShoppingCart, Package, TrendingUp } from "lucide-react";

const stats = [
  { title: "إجمالي المبيعات", value: "0 ج.م", icon: DollarSign, change: "+0%" },
  { title: "إجمالي الطلبات", value: "0", icon: ShoppingCart, change: "+0%" },
  { title: "المنتجات", value: "0", icon: Package, change: "" },
  { title: "معدل التحويل", value: "0%", icon: TrendingUp, change: "+0%" },
];

export default function AdminDashboard() {
  return (
    <div>
      <h1 className="text-2xl font-heading font-bold text-white mb-8">لوحة التحكم</h1>

      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        {stats.map((stat) => (
          <Card key={stat.title} className="bg-brand-dark border-white/10 p-6">
            <div className="flex items-center justify-between mb-4">
              <span className="text-white/50 text-sm">{stat.title}</span>
              <div className="w-10 h-10 rounded-lg bg-brand-red/10 flex items-center justify-center">
                <stat.icon size={20} className="text-brand-red" />
              </div>
            </div>
            <div className="text-2xl font-bold text-white">{stat.value}</div>
            {stat.change && <span className="text-green-500 text-sm">{stat.change} من الشهر الماضي</span>}
          </Card>
        ))}
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <Card className="bg-brand-dark border-white/10 p-6">
          <h2 className="text-lg font-heading font-semibold text-white mb-4">آخر الطلبات</h2>
          <div className="text-white/40 text-center py-8">لا توجد طلبات بعد</div>
        </Card>
        <Card className="bg-brand-dark border-white/10 p-6">
          <h2 className="text-lg font-heading font-semibold text-white mb-4">أفضل المنتجات</h2>
          <div className="text-white/40 text-center py-8">لا توجد منتجات بعد</div>
        </Card>
      </div>
    </div>
  );
}
