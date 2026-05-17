"use client";
import { Card } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Plus } from "lucide-react";

export default function AdminCoupons() {
  return (
    <div>
      <div className="flex items-center justify-between mb-8">
        <h1 className="text-2xl font-heading font-bold text-white">الكوبونات</h1>
        <Button className="bg-brand-red hover:bg-brand-red-dark text-white">
          <Plus size={18} className="ml-2" />
          إضافة كوبون
        </Button>
      </div>
      <Card className="bg-brand-dark border-white/10 p-8 text-center">
        <p className="text-white/40">لا توجد كوبونات بعد.</p>
      </Card>
    </div>
  );
}
