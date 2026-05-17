import Link from "next/link";
import Image from "next/image";
import { Globe, Mail, Phone } from "lucide-react";

const footerLinks = {
  shop: [
    { label: "Men", href: "/shop?gender=men" },
    { label: "Women", href: "/shop?gender=women" },
    { label: "Oversized", href: "/shop?category=oversized" },
    { label: "Compression", href: "/shop?category=compression" },
    { label: "Sets", href: "/shop?category=sets" },
  ],
  help: [
    { label: "Contact Us", href: "/contact" },
    { label: "FAQ", href: "/faq" },
    { label: "Shipping Policy", href: "/shipping-policy" },
    { label: "Return Policy", href: "/return-policy" },
    { label: "Size Guide", href: "/size-guide" },
  ],
  company: [
    { label: "About Us", href: "/about" },
    { label: "Privacy Policy", href: "/privacy" },
    { label: "Terms & Conditions", href: "/terms" },
  ],
};

export function Footer() {
  return (
    <footer className="bg-brand-black text-white">
      <div className="border-b border-white/10">
        <div className="mx-auto max-w-7xl px-4 py-12">
          <div className="max-w-xl mx-auto text-center">
            <h3 className="text-2xl font-heading font-bold mb-2">
              Join the <span className="text-brand-red">Movement</span>
            </h3>
            <p className="text-white/60 mb-6">
              Subscribe for exclusive drops, early access & special offers.
            </p>
            <form className="flex gap-2">
              <input
                type="email"
                placeholder="Enter your email"
                className="flex-1 bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder:text-white/40 focus:outline-none focus:border-brand-red"
              />
              <button
                type="submit"
                className="bg-brand-red hover:bg-brand-red-dark text-white font-semibold px-6 py-3 rounded-lg transition-colors"
              >
                Subscribe
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
              Premium sportswear for those who never stop moving.
            </p>
            <div className="flex gap-4">
              <a href="#" className="text-white/50 hover:text-brand-red transition-colors"><Globe size={20} /></a>
              <a href="#" className="text-white/50 hover:text-brand-red transition-colors"><Mail size={20} /></a>
              <a href="#" className="text-white/50 hover:text-brand-red transition-colors"><Phone size={20} /></a>
            </div>
          </div>
          <div>
            <h4 className="font-heading font-semibold mb-4 text-sm uppercase tracking-wider">Shop</h4>
            <ul className="space-y-2">
              {footerLinks.shop.map((link) => (
                <li key={link.href}>
                  <Link href={link.href} className="text-white/50 hover:text-white text-sm transition-colors">{link.label}</Link>
                </li>
              ))}
            </ul>
          </div>
          <div>
            <h4 className="font-heading font-semibold mb-4 text-sm uppercase tracking-wider">Help</h4>
            <ul className="space-y-2">
              {footerLinks.help.map((link) => (
                <li key={link.href}>
                  <Link href={link.href} className="text-white/50 hover:text-white text-sm transition-colors">{link.label}</Link>
                </li>
              ))}
            </ul>
          </div>
          <div>
            <h4 className="font-heading font-semibold mb-4 text-sm uppercase tracking-wider">Company</h4>
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
          &copy; {new Date().getFullYear()} On Track. All rights reserved.
        </div>
      </div>
    </footer>
  );
}
