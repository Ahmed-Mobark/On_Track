"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { Home, Search, ShoppingBag, Heart, User } from "lucide-react";
import { useCartStore } from "@/store/cart";

const navItems = [
  { href: "/", icon: Home, label: "الرئيسية" },
  { href: "/shop", icon: Search, label: "المتجر" },
  { href: "/cart", icon: ShoppingBag, label: "السلة" },
  { href: "/wishlist", icon: Heart, label: "المفضلة" },
  { href: "/account", icon: User, label: "حسابي" },
];

export function MobileNav() {
  const pathname = usePathname();
  const { items } = useCartStore();

  if (pathname.startsWith("/admin")) return null;

  return (
    <nav className="lg:hidden fixed bottom-0 left-0 right-0 z-50 bg-brand-black border-t border-white/10">
      <div className="flex items-center justify-around py-2 pb-[env(safe-area-inset-bottom)]">
        {navItems.map((item) => {
          const isActive = pathname === item.href;
          return (
            <Link
              key={item.href}
              href={item.href}
              className={`flex flex-col items-center gap-1 py-1 px-3 relative ${isActive ? "text-brand-red" : "text-white/50"}`}
            >
              <item.icon size={20} />
              <span className="text-[10px] font-medium">{item.label}</span>
              {item.label === "السلة" && items.length > 0 && (
                <span className="absolute -top-1 right-0 bg-brand-red text-white text-[10px] w-4 h-4 rounded-full flex items-center justify-center">
                  {items.length}
                </span>
              )}
            </Link>
          );
        })}
      </div>
    </nav>
  );
}
