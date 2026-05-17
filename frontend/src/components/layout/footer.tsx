"use client";

import Link from "next/link";
import Image from "next/image";
import { usePathname } from "next/navigation";
import { Globe, Mail, Phone } from "lucide-react";

const footerLinks = {
  shop: [
    { label: "رجالي", href: "/shop?gender=men" },
    { label: "حريمي", href: "/shop?gender=women" },
    { label: "أوفرسايز", href: "/shop?category=oversized" },
    { label: "كومبريشن", href: "/shop?category=compression" },
    { label: "أطقم", href: "/shop?category=sets" },
  ],
  help: [
    { label: "تواصل معنا", href: "/contact" },
    { label: "أسئلة شائعة", href: "/faq" },
    { label: "سياسة الشحن", href: "/shipping-policy" },
    { label: "سياسة الإرجاع", href: "/return-policy" },
    { label: "دليل المقاسات", href: "/size-guide" },
  ],
  company: [
    { label: "من نحن", href: "/about" },
    { label: "سياسة الخصوصية", href: "/privacy" },
    { label: "الشروط والأحكام", href: "/terms" },
  ],
};

export function Footer() {
  const pathname = usePathname();
  if (pathname.startsWith("/admin")) return null;

  return (
    <footer className="bg-brand-black text-white">
      <div className="border-b border-white/10">
        <div className="mx-auto max-w-7xl px-4 py-12">
          <div className="max-w-xl mx-auto text-center">
            <h3 className="text-2xl font-heading font-bold mb-2">
              انضم لـ <span className="text-brand-red">الحركة</span>
            </h3>
            <p className="text-white/60 mb-6">
              اشترك للحصول على عروض حصرية وإصدارات جديدة قبل الكل.
            </p>
            <form className="flex gap-2">
              <input
                type="email"
                placeholder="أدخل بريدك الإلكتروني"
                className="flex-1 bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder:text-white/40 focus:outline-none focus:border-brand-red"
              />
              <button
                type="submit"
                className="bg-brand-red hover:bg-brand-red-dark text-white font-semibold px-6 py-3 rounded-lg transition-colors"
              >
                اشترك
              </button>
            </form>
          </div>
        </div>
      </div>

      <div className="mx-auto max-w-7xl px-4 py-12">
        <div className="grid grid-cols-2 md:grid-cols-4 gap-8">
          <div>
            <Link href="/" className="inline-block mb-6">
              <Image
                src="/images/brand/02_ontrack_primary_white.svg"
                alt="On Track"
                width={160}
                height={64}
                className="h-12 w-auto"
              />
            </Link>
            <p className="text-white/50 text-sm mb-4">
              ملابس رياضية بريميوم لمن لا يتوقفون عن الحركة.
            </p>
            <div className="flex gap-4">
              <a href="#" className="text-white/50 hover:text-brand-red transition-colors"><Globe size={20} /></a>
              <a href="#" className="text-white/50 hover:text-brand-red transition-colors"><Mail size={20} /></a>
              <a href="#" className="text-white/50 hover:text-brand-red transition-colors"><Phone size={20} /></a>
            </div>
          </div>
          <div>
            <h4 className="font-heading font-semibold mb-4 text-sm uppercase tracking-wider">تسوق</h4>
            <ul className="space-y-2">
              {footerLinks.shop.map((link) => (
                <li key={link.href}>
                  <Link href={link.href} className="text-white/50 hover:text-white text-sm transition-colors">{link.label}</Link>
                </li>
              ))}
            </ul>
          </div>
          <div>
            <h4 className="font-heading font-semibold mb-4 text-sm uppercase tracking-wider">مساعدة</h4>
            <ul className="space-y-2">
              {footerLinks.help.map((link) => (
                <li key={link.href}>
                  <Link href={link.href} className="text-white/50 hover:text-white text-sm transition-colors">{link.label}</Link>
                </li>
              ))}
            </ul>
          </div>
          <div>
            <h4 className="font-heading font-semibold mb-4 text-sm uppercase tracking-wider">الشركة</h4>
            <ul className="space-y-2">
              {footerLinks.company.map((link) => (
                <li key={link.href}>
                  <Link href={link.href} className="text-white/50 hover:text-white text-sm transition-colors">{link.label}</Link>
                </li>
              ))}
            </ul>
          </div>
        </div>
      </div>

      <div className="border-t border-white/10">
        <div className="mx-auto max-w-7xl px-4 py-6 text-center text-white/30 text-sm">
          &copy; {new Date().getFullYear()} On Track. جميع الحقوق محفوظة.
        </div>
      </div>
    </footer>
  );
}
