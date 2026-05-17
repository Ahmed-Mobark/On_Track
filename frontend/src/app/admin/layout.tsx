"use client";

import Link from "next/link";
import Image from "next/image";
import { usePathname } from "next/navigation";
import {
  LayoutDashboard, Package, FolderTree, ShoppingCart,
  Users, Ticket, BarChart3, Settings, LogOut, Menu, X, ExternalLink,
} from "lucide-react";
import { useState } from "react";

const sidebarLinks = [
  { href: "/admin", icon: LayoutDashboard, label: "\u0644\u0648\u062D\u0629 \u0627\u0644\u062A\u062D\u0643\u0645" },
  { href: "/admin/products", icon: Package, label: "\u0627\u0644\u0645\u0646\u062A\u062C\u0627\u062A" },
  { href: "/admin/categories", icon: FolderTree, label: "\u0627\u0644\u062A\u0635\u0646\u064A\u0641\u0627\u062A" },
  { href: "/admin/orders", icon: ShoppingCart, label: "\u0627\u0644\u0637\u0644\u0628\u0627\u062A" },
  { href: "/admin/customers", icon: Users, label: "\u0627\u0644\u0639\u0645\u0644\u0627\u0621" },
  { href: "/admin/coupons", icon: Ticket, label: "\u0627\u0644\u0643\u0648\u0628\u0648\u0646\u0627\u062A" },
  { href: "/admin/analytics", icon: BarChart3, label: "\u0627\u0644\u062A\u062D\u0644\u064A\u0644\u0627\u062A" },
  { href: "/admin/settings", icon: Settings, label: "\u0627\u0644\u0625\u0639\u062F\u0627\u062F\u0627\u062A" },
];

export default function AdminLayout({ children }: { children: React.ReactNode }) {
  const pathname = usePathname();
  const [sidebarOpen, setSidebarOpen] = useState(false);

  return (
    <div dir="rtl" className="min-h-screen bg-brand-black flex">
      <aside
        className={`fixed lg:static inset-y-0 right-0 z-50 w-64 bg-brand-dark border-l border-white/10 transform transition-transform lg:translate-x-0 ${sidebarOpen ? "translate-x-0" : "translate-x-full"}`}
      >
        <div className="flex items-center justify-between h-16 px-6 border-b border-white/10">
          <Link href="/admin" className="flex items-center gap-2">
            <Image
              src="/images/brand/07_ontrack_horizontal_white.svg"
              alt="On Track"
              width={120}
              height={32}
              className="h-7 w-auto"
            />
            <span className="text-white/40 text-xs font-medium">Admin</span>
          </Link>
          <button className="lg:hidden text-white" onClick={() => setSidebarOpen(false)}>
            <X size={20} />
          </button>
        </div>

        <nav className="p-4 space-y-1">
          {sidebarLinks.map((link) => {
            const isActive = pathname === link.href || (link.href !== "/admin" && pathname.startsWith(link.href));
            return (
              <Link
                key={link.href}
                href={link.href}
                className={`flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors ${isActive ? "bg-brand-red/10 text-brand-red" : "text-white/60 hover:text-white hover:bg-white/5"}`}
                onClick={() => setSidebarOpen(false)}
              >
                <link.icon size={18} />
                {link.label}
              </Link>
            );
          })}
        </nav>

        <div className="absolute bottom-0 left-0 right-0 p-4 border-t border-white/10">
          <button className="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium text-white/60 hover:text-white hover:bg-white/5 w-full transition-colors">
            <LogOut size={18} /> تسجيل الخروج
          </button>
        </div>
      </aside>

      {sidebarOpen && (
        <div className="fixed inset-0 z-40 bg-black/50 lg:hidden" onClick={() => setSidebarOpen(false)} />
      )}

      <div className="flex-1 flex flex-col min-h-screen">
        <header className="h-16 border-b border-white/10 flex items-center px-4 lg:px-8 gap-4">
          <button className="lg:hidden text-white" onClick={() => setSidebarOpen(true)}>
            <Menu size={20} />
          </button>
          <div className="flex-1" />
          <Link href="/" target="_blank" className="flex items-center gap-2 text-white/60 hover:text-white text-sm">
            <ExternalLink size={16} />
            عرض المتجر
          </Link>
        </header>
        <main className="flex-1 p-4 lg:p-8">{children}</main>
      </div>
    </div>
  );
}
