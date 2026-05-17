"use client";
import { Card } from "@/components/ui/card";
import { Button } from "@/components/ui/button";

export default function AdminSettings() {
  return (
    <div>
      <h1 className="text-2xl font-heading font-bold text-white mb-8">الإعدادات</h1>
      <div className="space-y-6">
        <Card className="bg-brand-dark border-white/10 p-6">
          <h2 className="text-lg font-heading font-semibold text-white mb-4">إعدادات المتجر</h2>
          <div className="space-y-4">
            <div>
              <label className="block text-white/70 text-sm mb-1.5">اسم المتجر</label>
              <input defaultValue="On Track" className="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-brand-red" />
            </div>
            <div>
              <label className="block text-white/70 text-sm mb-1.5">البريد الإلكتروني</label>
              <input placeholder="info@ontrack.com" className="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white placeholder:text-white/30 focus:outline-none focus:border-brand-red" dir="ltr" />
            </div>
            <div>
              <label className="block text-white/70 text-sm mb-1.5">رقم الموبايل</label>
              <input placeholder="01xxxxxxxxx" className="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white placeholder:text-white/30 focus:outline-none focus:border-brand-red" dir="ltr" />
            </div>
          </div>
          <Button className="bg-brand-red hover:bg-brand-red-dark text-white mt-6">حفظ التغييرات</Button>
        </Card>
        <Card className="bg-brand-dark border-white/10 p-6">
          <h2 className="text-lg font-heading font-semibold text-white mb-4">إعدادات الشحن</h2>
          <div className="space-y-4">
            <div>
              <label className="block text-white/70 text-sm mb-1.5">تكلفة الشحن (ج.م)</label>
              <input defaultValue="50" type="number" className="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-brand-red" />
            </div>
            <div>
              <label className="block text-white/70 text-sm mb-1.5">شحن مجاني للطلبات فوق (ج.م)</label>
              <input defaultValue="500" type="number" className="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-brand-red" />
            </div>
          </div>
          <Button className="bg-brand-red hover:bg-brand-red-dark text-white mt-6">حفظ التغييرات</Button>
        </Card>
      </div>
    </div>
  );
}
