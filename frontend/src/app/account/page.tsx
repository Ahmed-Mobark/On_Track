"use client";

import Link from "next/link";
import { User, Package, Heart, MapPin, Bell, LogOut } from "lucide-react";

const menuItems = [
  { icon: Package, label: "طلباتي", href: "/account/orders", desc: "تابع طلباتك وحالة الشحن" },
  { icon: Heart, label: "المفضلة", href: "/wishlist", desc: "المنتجات اللي حفظتها" },
  { icon: MapPin, label: "العناوين", href: "/account/addresses", desc: "إدارة عناوين التوصيل" },
  { icon: Bell, label: "الإشعارات", href: "/account/notifications", desc: "تحديثات الطلبات والعروض" },
];

export default function AccountPage() {
  return (
    <div className="bg-brand-black min-h-screen" dir="rtl">
      <div className="mx-auto max-w-7xl px-4 py-8">
        <h1 className="text-3xl font-heading font-bold text-white mb-8">حسابي</h1>

        {/* Profile Card */}
        <div className="bg-brand-dark rounded-xl p-6 mb-8 flex items-center gap-4">
          <div className="w-16 h-16 rounded-full bg-brand-red/10 flex items-center justify-center">
            <User size={28} className="text-brand-red" />
          </div>
          <div>
            <h2 className="text-xl font-heading font-semibold text-white">مرحباً!</h2>
            <p className="text-white/50 text-sm">سجل دخول للوصول لحسابك</p>
          </div>
          <Link
            href="/login"
            className="mr-auto bg-brand-red hover:bg-brand-red-dark text-white font-semibold px-6 py-2.5 rounded-xl transition-colors text-sm"
          >
            تسجيل الدخول
          </Link>
        </div>

        {/* Menu */}
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
          {menuItems.map((item) => (
            <Link
              key={item.href}
              href={item.href}
              className="bg-brand-dark rounded-xl p-6 flex items-center gap-4 hover:bg-white/5 transition-colors group"
            >
              <div className="w-12 h-12 rounded-lg bg-brand-red/10 flex items-center justify-center">
                <item.icon size={22} className="text-brand-red" />
              </div>
              <div>
                <h3 className="text-white font-semibold group-hover:text-brand-red transition-colors">{item.label}</h3>
                <p className="text-white/40 text-sm">{item.desc}</p>
              </div>
            </Link>
          ))}
        </div>
      </div>
    </div>
  );
}
