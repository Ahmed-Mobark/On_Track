"use client";

import Link from "next/link";
import Image from "next/image";
import { useState } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { Search, ShoppingBag, Heart, User, Menu, X } from "lucide-react";
import { useCartStore } from "@/store/cart";
import { useAuthStore } from "@/store/auth";

const navLinks = [
  { href: "/shop", label: "Shop All" },
  { href: "/shop?gender=men", label: "Men" },
  { href: "/shop?gender=women", label: "Women" },
  { href: "/shop?category=oversized", label: "Oversized" },
  { href: "/shop?category=compression", label: "Compression" },
  { href: "/shop?category=sets", label: "Sets" },
];

export function Header() {
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
  const [searchOpen, setSearchOpen] = useState(false);
  const { items } = useCartStore();
  const { isAuthenticated } = useAuthStore();

  return (
    <>
      <div className="bg-brand-red text-white text-center py-2 text-sm font-medium">
        Free Shipping on Orders Over 500 EGP
      </div>

      <header className="sticky top-0 z-50 bg-brand-black border-b border-white/10">
        <div className="mx-auto max-w-7xl px-4">
          <div className="flex items-center justify-between h-16 lg:h-20">
            <button
              className="lg:hidden text-white"
              onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
            >
              {mobileMenuOpen ? <X size={24} /> : <Menu size={24} />}
            </button>

            <Link href="/" className="flex items-center">
              <Image
                src="/images/brand/07_ontrack_horizontal_white.svg"
                alt="On Track"
                width={180}
                height={48}
                className="h-8 lg:h-10 w-auto"
                priority
              />
            </Link>

            <nav className="hidden lg:flex items-center gap-8">
              {navLinks.map((link) => (
                <Link
                  key={link.href}
                  href={link.href}
                  className="text-white/80 hover:text-white text-sm font-medium uppercase tracking-wider transition-colors"
                >
                  {link.label}
                </Link>
              ))}
            </nav>

            <div className="flex items-center gap-3">
              <button
                onClick={() => setSearchOpen(true)}
                className="text-white/80 hover:text-white transition-colors"
              >
                <Search size={20} />
              </button>
              <Link href="/wishlist" className="text-white/80 hover:text-white transition-colors hidden sm:block">
                <Heart size={20} />
              </Link>
              <Link href={isAuthenticated ? "/account" : "/login"} className="text-white/80 hover:text-white transition-colors hidden sm:block">
                <User size={20} />
              </Link>
              <Link href="/cart" className="relative text-white/80 hover:text-white transition-colors">
                <ShoppingBag size={20} />
                {items.length > 0 && (
                  <span className="absolute -top-2 -right-2 bg-brand-red text-white text-xs w-5 h-5 rounded-full flex items-center justify-center font-bold">
                    {items.length}
                  </span>
                )}
              </Link>
            </div>
          </div>
        </div>

        <AnimatePresence>
          {mobileMenuOpen && (
            <motion.div
              initial={{ height: 0, opacity: 0 }}
              animate={{ height: "auto", opacity: 1 }}
              exit={{ height: 0, opacity: 0 }}
              className="lg:hidden bg-brand-black border-t border-white/10 overflow-hidden"
            >
              <nav className="mx-auto max-w-7xl px-4 py-4 flex flex-col gap-3">
                {navLinks.map((link) => (
                  <Link
                    key={link.href}
                    href={link.href}
                    className="text-white/80 hover:text-white text-base font-medium py-2"
                    onClick={() => setMobileMenuOpen(false)}
                  >
                    {link.label}
                  </Link>
                ))}
                <div className="flex gap-4 pt-3 border-t border-white/10">
                  <Link href="/wishlist" className="text-white/80 hover:text-white flex items-center gap-2 text-sm">
                    <Heart size={18} /> Wishlist
                  </Link>
                  <Link href={isAuthenticated ? "/account" : "/login"} className="text-white/80 hover:text-white flex items-center gap-2 text-sm">
                    <User size={18} /> Account
                  </Link>
                </div>
              </nav>
            </motion.div>
          )}
        </AnimatePresence>
      </header>

      <AnimatePresence>
        {searchOpen && (
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            className="fixed inset-0 z-[60] bg-black/90 flex items-start justify-center pt-32"
            onClick={() => setSearchOpen(false)}
          >
            <motion.div
              initial={{ y: -20, opacity: 0 }}
              animate={{ y: 0, opacity: 1 }}
              exit={{ y: -20, opacity: 0 }}
              className="w-full max-w-2xl px-4"
              onClick={(e) => e.stopPropagation()}
            >
              <div className="relative">
                <Search className="absolute left-4 top-1/2 -translate-y-1/2 text-white/50" size={24} />
                <input
                  type="text"
                  placeholder="Search products..."
                  className="w-full bg-white/10 border border-white/20 rounded-xl py-4 pl-14 pr-4 text-white text-lg placeholder:text-white/40 focus:outline-none focus:border-brand-red"
                  autoFocus
                />
                <button
                  onClick={() => setSearchOpen(false)}
                  className="absolute right-4 top-1/2 -translate-y-1/2 text-white/50 hover:text-white"
                >
                  <X size={24} />
                </button>
              </div>
            </motion.div>
          </motion.div>
        )}
      </AnimatePresence>
    </>
  );
}
