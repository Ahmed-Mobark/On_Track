"use client";
import { Card } from "@/components/ui/card";

export default function AdminAnalytics() {
  return (
    <div>
      <h1 className="text-2xl font-heading font-bold text-white mb-8">التحليلات</h1>
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <Card className="bg-brand-dark border-white/10 p-6">
          <h2 className="text-lg font-heading font-semibold text-white mb-4">المبيعات</h2>
          <div className="text-white/40 text-center py-12">الرسوم البيانية ستظهر هنا عند توفر بيانات</div>
        </Card>
        <Card className="bg-brand-dark border-white/10 p-6">
          <h2 className="text-lg font-heading font-semibold text-white mb-4">الزوار</h2>
          <div className="text-white/40 text-center py-12">تحليلات الزوار ستظهر هنا</div>
        </Card>
      </div>
    </div>
  );
}
